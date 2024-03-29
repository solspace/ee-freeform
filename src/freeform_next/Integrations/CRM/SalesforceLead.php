<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Integrations\CRM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Integrations\CRM\Salesforce\AbstractSalesforceIntegration;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\CRMIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;

class SalesforceLead extends AbstractSalesforceIntegration implements TokenRefreshInterface
{
    const TITLE        = 'Salesforce Lead';
    const LOG_CATEGORY = 'Salesforce';

    const SETTING_CLIENT_ID     = 'salesforce_client_id';
    const SETTING_CLIENT_SECRET = 'salesforce_client_secret';
    const SETTING_USER_LOGIN    = 'salesforce_username';
    const SETTING_USER_PASSWORD = 'salesforce_password';
    const SETTING_LEAD_OWNER    = 'salesforce_lead_owner';
    const SETTING_SANDBOX       = 'salesforce_sandbox';
    const SETTING_CUSTOM_URL    = 'salesforce_custom_url';
    const SETTING_INSTANCE      = 'instance';

    /**
     * Returns a list of additional settings for this integration
     * Could be used for anything, like - AccessTokens
     *
     * @return SettingBlueprint[]
     */
    public static function getSettingBlueprints()
    {
        return [
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_LEAD_OWNER,
                'Assign lead owner?',
                'Enabling this will have leads assigned to a lead owner based on lead owner assignment rules in Salesforce.',
                false
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_SANDBOX,
                'Sandbox mode',
                'Enabling this connects to "test.salesforce.com" instead of "login.salesforce.com".',
                false
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_CUSTOM_URL,
                'Using custom URL?',
                'Enable this if your Salesforce account uses a custom URL to login.',
                false
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG,
                self::SETTING_CLIENT_ID,
                'Client ID',
                'Enter the Client ID of your app in here',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG,
                self::SETTING_CLIENT_SECRET,
                'Client Secret',
                'Enter the Client Secret of your app here',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG,
                self::SETTING_USER_LOGIN,
                'Username',
                'Enter your Salesforce username here',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG,
                self::SETTING_USER_PASSWORD,
                'Password',
                'Enter your Salesforce password here',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_INTERNAL,
                self::SETTING_INSTANCE,
                'Instance',
                'This will be fetched automatically upon authorizing your credentials.',
                false,
                false
            ),
        ];
    }

    /**
     * A method that initiates the authentication
     */
    public function initiateAuthentication()
    {
    }

    /**
     * Authorizes the application
     * Returns the access_token
     *
     * @return string
     * @throws IntegrationException
     */
    public function fetchAccessToken()
    {
        $client = new Client();

        $clientId     = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $username     = $this->getUsername();
        $password     = $this->getPassword();

        if (!$clientId || !$clientSecret || !$username || !$password) {
            throw new IntegrationException('Some or all of the configuration values are missing');
        }

        $payload = [
            'grant_type'    => 'password',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'username'      => $username,
            'password'      => $password,
        ];

        $body = http_build_query($payload);

        try {

			$response = $client->post($this->getAccessTokenUrl(), [
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded'
				],
				'body' => $body
			]);

            $json = json_decode($response->getBody());

            if (!isset($json->access_token)) {
                throw new IntegrationException(
                    $this->getTranslator()->translate(
                        'No \'access_token\' present in auth response for {serviceProvider}',
                        ['serviceProvider' => $this->getServiceProvider()]
                    )
                );
            }

            $this->setAccessToken($json->access_token);
            $this->setAccessTokenUpdated(true);

            $this->onAfterFetchAccessToken($json);

        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);
        }

        return $this->getAccessToken();
    }

    /**
     * Perform anything necessary before this integration is saved
     *
     * @param IntegrationStorageInterface $model
     */
    public function onBeforeSave(IntegrationStorageInterface $model)
    {
        $clientId     = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $username     = $this->getUsername();
        $password     = $this->getPassword();

        // If one of these isn't present, we just return void
        if (!$clientId || !$clientSecret || !$username || !$password) {
            return;
        }

        $this->fetchAccessToken();
        $model->updateAccessToken($this->getAccessToken());
        $model->updateSettings($this->getSettings());
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    public function checkConnection()
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint('/');

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];

		$response  = $client->get($endpoint, ['headers' => $headers]);

        $json = json_decode($response->getBody(true), true);

        return !empty($json);
    }

    /**
     * Push objects to the CRM
     *
     * @param array $keyValueList
     *
     * @return bool
     * @throws \Exception
     */
    public function pushObject(array $keyValueList, $formFields = NULL)
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint('/sobjects/Lead');

        $setOwner = $this->getSetting(self::SETTING_LEAD_OWNER);

        $keyValueList = array_filter($keyValueList);

        try {
            $headers = [
                'Authorization'      => 'Bearer ' . $this->getAccessToken(),
                'Accept'             => 'application/json',
                'Content-Type'       => 'application/json',
                'Sforce-Auto-Assign' => $setOwner ? 'TRUE' : 'FALSE',
            ];

			$response = $client->post($endpoint, [
				'headers' => $headers ,
				'body' => json_encode($keyValueList)
			]);

            return $response->getStatusCode() === 201;
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

            if ($e->getResponse()->getStatusCode() === 400) {
                $errors = json_decode($e->getResponse()->getBody());

                if (is_array($errors)) {
                    foreach ($errors as $error) {
                        if (strtoupper($error->errorCode) === 'REQUIRED_FIELD_MISSING') {
                            return false;
                        }
                    }

                }
            }

            throw $e;
        }
    }

    /**
     * Fetch the custom fields from the integration
     *
     * @return FieldObject[]
     */
    public function fetchFields()
    {
        $client = $this->generateAuthorizedClient();

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];


        try {
			$response  = $client->get($this->getEndpoint('/sobjects/Lead/describe'), [
				'headers' => $headers
			]);
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

            return [];
        }

        $data = json_decode($response->getBody(true));

        $fieldList = [];
        foreach ($data->fields as $field) {
            if (!$field->updateable || !empty($field->referenceTo)) {
                continue;
            }

            $type = null;
            switch ($field->type) {
                case 'string':
                case 'textarea':
                case 'email':
                case 'url':
                case 'address':
                case 'picklist':
                case 'phone':
                    $type = FieldObject::TYPE_STRING;
                    break;

                case 'boolean':
                    $type = FieldObject::TYPE_BOOLEAN;
                    break;

                case 'multipicklist':
                    $type = FieldObject::TYPE_ARRAY;
                    break;

                case 'number':
                case 'currency':
                case 'double':
                    $type = FieldObject::TYPE_NUMERIC;
                    break;
            }

            if (null === $type) {
                continue;
            }

            $fieldObject = new FieldObject(
                $field->name,
                $field->label,
                $type,
                !$field->nillable
            );

            $fieldList[] = $fieldObject;
        }

        return $fieldList;
    }

    /**
     * Initiate a token refresh and fetch a refreshed token
     * Returns true on success
     *
     * @return bool
     */
    public function refreshToken()
    {
        return (bool) $this->fetchAccessToken();
    }

    /**
     * @param FieldObject $fieldObject
     * @param mixed|null  $value
     *
     * @return bool|string
     */
    public function convertCustomFieldValue(FieldObject $fieldObject, $value = null)
    {
        if ($fieldObject->getType() === FieldObject::TYPE_ARRAY) {
            return is_array($value) ? implode(';', $value) : $value;
        }

        return parent::convertCustomFieldValue($fieldObject, $value);
    }

    /**
     * @param \stdClass $responseData
     *
     * @throws CRMIntegrationNotFoundException
     */
    protected function onAfterFetchAccessToken(\stdClass $responseData)
    {
        if (!isset($responseData->instance_url)) {
            throw new CRMIntegrationNotFoundException('Salesforce response data doesn\'t contain the instance URL');
        }

        $this->setSetting(self::SETTING_INSTANCE, $responseData->instance_url);
    }

    /**
     * URL pointing to the OAuth2 authorization endpoint
     *
     * @return string
     */
    protected function getAuthorizeUrl()
    {
        return 'https://' . $this->getLoginUrl() . '.salesforce.com/services/oauth2/authorize';
    }

    /**
     * URL pointing to the OAuth2 access token endpoint
     *
     * @return string
     */
    protected function getAccessTokenUrl()
    {
        return 'https://' . $this->getLoginUrl() . '.salesforce.com/services/oauth2/token';
    }

    /**
     * @return string
     */
    protected function getApiRootUrl()
    {
        $instance        = $this->getSetting(self::SETTING_INSTANCE);
        $usingCustomUrls = $this->getSetting(self::SETTING_CUSTOM_URL);

        if ($instance && strpos($instance, 'https://') !== 0) {
            return sprintf(
                'https://%s%s.salesforce.com/services/data/v44.0/',
                $instance,
                ($usingCustomUrls ? '.my' : '')
            );
        }

        return $instance . '/services/data/v44.0/';
    }

    protected function getAuthorizationCheckUrl(): string
    {
        return $this->getEndpoint('/sobjects/Lead/describe');
    }

    /**
     * @return string
     */
    private function getLoginUrl()
    {
        $isSandboxMode = $this->getSetting(self::SETTING_SANDBOX);

        if ($isSandboxMode) {
            return 'test';
        }

        return 'login';
    }

    /**
     * @return mixed|null
     */
    private function getClientId()
    {
        return $this->getSetting(self::SETTING_CLIENT_ID);
    }

    /**
     * @return mixed|null
     */
    private function getClientSecret()
    {
        return $this->getSetting(self::SETTING_CLIENT_SECRET);
    }

    /**
     * @return mixed|null
     */
    private function getUsername()
    {
        return $this->getSetting(self::SETTING_USER_LOGIN);
    }

    /**
     * @return mixed|null
     */
    private function getPassword()
    {
        return $this->getSetting(self::SETTING_USER_PASSWORD);
    }
}
