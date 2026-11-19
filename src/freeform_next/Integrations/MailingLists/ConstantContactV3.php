<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Integrations\MailingLists;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListOAuthConnector;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationSecret;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use stdClass;

class ConstantContactV3 extends MailingListOAuthConnector implements TokenRefreshInterface
{
    public const TITLE = "Constant Contact (v3)";
    public const LOG_CATEGORY = "ConstantContact_v3";

    private const SETTING_REFRESH_TOKEN = 'refresh_token';

    public static function getSettingBlueprints(): array
    {
        return array_merge(parent::getSettingBlueprints(), [
            new SettingBlueprint(SettingBlueprint::TYPE_INTERNAL, self::SETTING_REFRESH_TOKEN, '', ''),
        ]);
    }

    protected function onAfterFetchAccessToken(stdClass $responseData): void
    {
        $token = $responseData->refresh_token ?? null;
        $this->setSetting(
            self::SETTING_REFRESH_TOKEN,
            is_string($token) && $token !== '' ? IntegrationSecret::encrypt($token) : null
        );
    }

    /**
     * @return string
     */
    public function getServiceProvider(): string
    {
        return "Constant Contact";
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    public function checkConnection(): bool
    {
        try {
            $response = $this->requestApi('GET', "/contact_lists");
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody();

            $errorMessage = implode(", ", [$responseBody, $e->getMessage()]);

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            return false;
        } catch (IntegrationException $e) {
            return false;
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $errorMessage = "Could not connect to ConstantContact API";

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            return false;
        }

        $json = json_decode($response->getBody());

        return isset($json->lists);
    }

    /**
     * @return void
     */
    public function initiateAuthentication(): void
    {
        $data = [
            "response_type" => "code",
            "client_id"     => $this->getClientId(),
            "redirect_uri"  => $this->getReturnUri(),
            "scope"         => "offline_access contact_data account_read account_update campaign_data",
            "state"         => $this->getId()
        ];

        header("Location: " . $this->getAuthorizeUrl() . "?" . http_build_query($data));
        die();
    }

    /**
     * @return bool
     * @throws IntegrationException|GuzzleException
     */
    public function refreshToken(): bool
    {
        $db = ee()->db;
        $id = (int) $this->getId();
        // Constant Contact rotates refresh tokens. Read and replace them under one lock.
        $lock = 'ff:cc:' . sha1($db->database . ':' . $db->dbprefix . ':' . $id);
        $result = $db->query('SELECT GET_LOCK(?, 15) AS acquired', [$lock]);
        if (!$result || (int) ($result->row()->acquired ?? 0) !== 1) {
            throw new IntegrationException('Could not acquire the Constant Contact token renewal lock.');
        }

        try {
            $model = MailingListRepository::getInstance()->getIntegrationById($id);
            if (!$model || $model->class !== self::class) {
                return false;
            }

            $settings = json_decode($model->settings ?: '{}', true) ?: [];
            $storedToken = $settings[self::SETTING_REFRESH_TOKEN] ?? null;
            if ($model->accessToken && $model->accessToken !== $this->getAccessToken()) {
                // A concurrent request already renewed this connection.
                $this->setAccessToken($model->accessToken);
                $this->setSetting(self::SETTING_REFRESH_TOKEN, $storedToken);
                return true;
            }
            if (!$storedToken) {
                return false;
            }

            $response = $this->requestRenewal($settings, IntegrationSecret::decrypt($storedToken));
            $accessToken = $response->access_token;
            $refreshToken = $response->refresh_token ?? null;
            if (is_string($refreshToken) && $refreshToken !== '') {
                $settings[self::SETTING_REFRESH_TOKEN] = IntegrationSecret::encrypt($refreshToken);
            }

            // A new authorization from the control panel can supersede this renewal.
            $saved = $db->where([
                'id' => $id,
                'accessToken' => $model->accessToken,
                'settings' => $model->settings,
            ])->update(IntegrationModel::TABLE, [
                'accessToken' => $accessToken,
                'settings' => json_encode($settings, JSON_THROW_ON_ERROR),
            ]);
            if (!$saved || $db->affected_rows() !== 1) {
                throw new IntegrationException('Could not save the renewed Constant Contact credentials.');
            }

            $this->setAccessToken($accessToken);
            $this->setSetting(self::SETTING_REFRESH_TOKEN, $settings[self::SETTING_REFRESH_TOKEN]);

            return true;
        } finally {
            $db->query('SELECT RELEASE_LOCK(?)', [$lock]);
        }
    }

    private function requestRenewal(array $settings, string $refreshToken): stdClass
    {
        try {
            $response = $this->createClient()->post($this->getAccessTokenUrl(), [
                'auth' => [
                    $settings[self::SETTING_CLIENT_ID] ?? '',
                    $settings[self::SETTING_CLIENT_SECRET] ?? '',
                ],
                'headers' => ['Accept' => 'application/json'],
                'form_params' => ['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken],
                'http_errors' => false,
                'allow_redirects' => false,
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            throw new IntegrationException('Could not reach Constant Contact to renew authorization.');
        }

        $data = json_decode((string) $response->getBody());
        if (($data->error ?? null) === 'invalid_grant') {
            throw new IntegrationException('Constant Contact rejected the refresh token. Reauthorize the integration in Freeform.');
        }
        if ($response->getStatusCode() !== 200) {
            throw new IntegrationException('Constant Contact token renewal failed (HTTP ' . $response->getStatusCode() . ').');
        }
        if (!is_string($data->access_token ?? null) || $data->access_token === '') {
            throw new IntegrationException('Constant Contact did not return an access token.');
        }

        return $data;
    }

    protected function createClient(): Client
    {
        return new Client();
    }

    private function requestApi(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        $client = $this->createClient();
        $url = $this->getEndpoint($endpoint);
        $options['headers'] = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ];

        try {
            return $client->request($method, $url, $options);
        } catch (BadResponseException $e) {
            if ($e->getResponse()->getStatusCode() !== 401) {
                throw $e;
            }
            try {
                if (!$this->refreshToken()) {
                    throw new IntegrationException('Reauthorize the Constant Contact integration in Freeform to enable automatic renewal.');
                }
            } catch (IntegrationException $renewalError) {
                $this->getLogger()->error($renewalError->getMessage(), self::LOG_CATEGORY);
                throw $renewalError;
            }
        }

        $options['headers']['Authorization'] = 'Bearer ' . $this->getAccessToken();

        return $client->request($method, $url, $options);
    }

    /**
     * @param ListObject $mailingList
     * @param array $emails
     * @param array $mappedValues
     * @return bool
     * @throws IntegrationException|GuzzleException
     */
    public function pushEmails(ListObject $mailingList, array $emails, array $mappedValues): bool
    {
        try {
            $data = [];

            foreach ($mappedValues as $key => $value) {
                if (preg_match("/^street_address_(.*)/", $key, $matches)) {
                    if (empty($cdta["street_address"])) {
                        $data["street_address"] = [];
                    }

                    $data["street_address"][$matches[1]] = $value;
                } elseif (preg_match("/^custom_(.*)/", $key, $matches)) {
                    if (empty($data["custom_fields"])) {
                        $data["custom_fields"] = [];
                    }

                    $data["custom_fields"][] = [
                        "custom_field_id" => $matches[1],
                        "value" => $value,
                    ];
                } else {
                    $data[$key] = $value;
                }
            }

            if (isset($data["street_address"]) && empty($data["street_address"]["kind"])) {
                $data["street_address"]["kind"] = "home";
            }

            $email = reset($emails);
            $email = strtolower($email);

            $data = array_merge(
                [
                    "email_address" => $email,
                    "create_source" => "Contact",
                    "list_memberships" => [$mailingList->getId()]
                ],
                $data
            );

            $response = $this->requestApi('POST', "/contacts/sign_up_form", ['body' => json_encode($data)]);
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody();

            $errorMessage = implode(", ", [$responseBody, $e->getMessage()]);

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        $status = $response->getStatusCode();
        if (!in_array($status, [200, 201], true)) {
            $errorMessage = "Could not add contacts to ConstantContact list";

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        return true;
    }

    /**
     * @return ListObject[]
     * @throws IntegrationException|GuzzleException
     */
    public function fetchLists(): array
    {
        try {
            $response = $this->requestApi('GET', "/contact_lists");
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody();

            $errorMessage = implode(', ', [$responseBody, $e->getMessage()]);

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $errorMessage = "Could not fetch ConstantContact lists";

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        $json = json_decode($response->getBody());

        $lists = [];

        if (isset($json->lists)) {
            foreach ($json->lists as $list) {
                if (isset($list->list_id, $list->name)) {
                    $lists[] = new ListObject(
                        $this,
                        $list->list_id,
                        $list->name,
                        $this->fetchFields($list->list_id)
                    );
                }
            }
        }

        return $lists;
    }

    /**
     * @param string $listId
     * @return FieldObject[]
     * @throws IntegrationException|GuzzleException
     */
    public function fetchFields($listId)
    {
        try {
            $response = $this->requestApi('GET', "/contact_custom_fields?limit=100");
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody();

            $errorMessage = implode(", ", [$responseBody, $e->getMessage()]);

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $errorMessage = "Could not fetch ConstantContact custom fields";

            $this->getLogger()->error($errorMessage, self::LOG_CATEGORY);

            throw new IntegrationException($this->getTranslator()->translate($errorMessage));
        }

        $json = json_decode($response->getBody());

        $fieldList = [];

        $fieldList[] = new FieldObject("first_name", "First Name", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("last_name", "Last Name", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("job_title", "Job Title", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("company_name", "Company Name", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("phone_number", "Phone Number", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("anniversary", "Anniversary", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("birthday_month", "Birthday Month", FieldObject::TYPE_NUMERIC, false);
        $fieldList[] = new FieldObject("birthday_day", "Birthday Day", FieldObject::TYPE_NUMERIC, false);
        $fieldList[] = new FieldObject("street_address_kind", "Address: Kind", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("street_address_street", "Address: Street", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("street_address_city", "Address: City", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("street_address_state", "Address: State", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("street_address_postal_code", "Address: Postal Code", FieldObject::TYPE_STRING, false);
        $fieldList[] = new FieldObject("street_address_country", "Address: Country", FieldObject::TYPE_STRING, false);

        if (empty($json->custom_fields)) {
            return $fieldList;
        }

        foreach ($json->custom_fields as $field) {
            $fieldList[] = new FieldObject(
                "custom_".$field->custom_field_id,
                $field->label,
                FieldObject::TYPE_STRING,
                false
            );
        }

        return $fieldList;
    }

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string
    {
        return "https://authz.constantcontact.com/oauth2/default/v1/authorize";
    }

    /**
     * @return string
     */
    public function getAccessTokenUrl(): string
    {
        return "https://authz.constantcontact.com/oauth2/default/v1/token";
    }

    /**
     * @return string
     */
    public function getApiRootUrl(): string
    {
        return "https://api.cc.email/v3";
    }
}
