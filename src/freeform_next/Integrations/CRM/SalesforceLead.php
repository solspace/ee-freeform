<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Integrations\CRM;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\CRMIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;

class SalesforceLead extends AbstractCRMIntegration implements TokenRefreshInterface
{
    const TITLE        = 'Salesforce Lead';
    const LOG_CATEGORY = 'Salesforce';

    const SETTING_CLIENT_ID     = 'salesforce_client_id';
    const SETTING_CLIENT_SECRET = 'salesforce_client_secret';
    const SETTING_USER_LOGIN    = 'salesforce_username';
    const SETTING_USER_PASSWORD = 'salesforce_password';
    const SETTING_SANDBOX       = 'salesforce_sandbox';
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
                self::SETTING_SANDBOX,
                'Sandbox Mode',
                'Enabling this connects to "test.salesforce.com" instead of "login.salesforce.com"',
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

        $payload      = [
            'grant_type'    => 'password',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'username'      => $username,
            'password'      => $password,
        ];

        $body = http_build_query($payload);

        try {

            $request = $client->post($this->getAccessTokenUrl(), ['Content-Type' => 'application/x-www-form-urlencoded']);
            $request->setBody($body);
            $response = $request->send();

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

        $request  = $client->get($endpoint, $headers);
        $response = $request->send();

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
    public function pushObject(array $keyValueList)
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint('/sobjects/Lead');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Accept'        => 'application/json',
            ];

            $request = $client->post($endpoint, $headers);
            $request->setHeader('Content-Type', 'application/json');
            $request->setBody(json_encode($keyValueList));
            $response = $request->send();

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
        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];


        try {
            $request  = $client->get($this->getEndpoint('/sobjects/Lead/describe'), $headers);
            $response = $request->send();
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
                    $type = FieldObject::TYPE_STRING;
                    break;

                case 'multipicklist':
                    $type = FieldObject::TYPE_ARRAY;
                    break;

                case 'number':
                case 'phone':
                case 'currency':
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

        $pattern = '/https:\/\/([a-z0-9]+)\./';

        preg_match($pattern, $responseData->instance_url, $matches);

        if (!isset($matches[1])) {
            throw new CRMIntegrationNotFoundException(
                sprintf('Could not pull the instance from \'%s\'', $responseData->instance_url)
            );
        }

        $this->setSetting(self::SETTING_INSTANCE, $matches[1]);
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
        $instance = $this->getSetting(self::SETTING_INSTANCE);

        return sprintf('https://%s.salesforce.com/services/data/v20.0/', $instance);
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
