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

namespace Solspace\Addons\FreeformNext\Integrations\CRM;

use Exception;
use stdClass;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
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
    public const TITLE        = 'Salesforce Lead';
    public const LOG_CATEGORY = 'Salesforce';

    public const SETTING_CLIENT_ID     = 'salesforce_client_id';
    public const SETTING_CLIENT_SECRET = 'salesforce_client_secret';
    public const SETTING_USER_LOGIN    = 'salesforce_username';
    public const SETTING_USER_PASSWORD = 'salesforce_password';
    public const SETTING_SECURITY_TOKEN = 'salesforce_security_token';
    public const SETTING_LEAD_OWNER    = 'salesforce_lead_owner';
    public const SETTING_SANDBOX       = 'salesforce_sandbox';
    public const SETTING_CUSTOM_URL    = 'salesforce_custom_url';
    public const SETTING_INSTANCE      = 'instance';
    public const SETTING_CLIENT_CREDENTIALS        = 'salesforce_client_credentials';
    public const SETTING_MY_DOMAIN                 = 'salesforce_my_domain';
    public const SETTING_CLIENT_CREDENTIALS_ID     = 'salesforce_client_credentials_id';
    public const SETTING_CLIENT_CREDENTIALS_SECRET = 'salesforce_client_credentials_secret';

    /**
     * Returns a list of additional settings for this integration
     * Could be used for anything, like - AccessTokens
     *
     * @return SettingBlueprint[]
     */
    public static function getSettingBlueprints(): array
    {
        return [
            new SettingBlueprint(
                SettingBlueprint::TYPE_RADIO,
                self::SETTING_CLIENT_CREDENTIALS,
                'Authentication Method',
                'Choose how this integration connects to Salesforce. Access tokens are obtained automatically when you save valid credentials.',
                false, '', 'y', null,
                ['y' => 'salesforce-client-credentials', 'n' => 'salesforce-password'],
                ['y' => 'Client Credentials', 'n' => 'Username-Password (legacy)'],
                'n'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_TEXT,
                self::SETTING_MY_DOMAIN,
                'My Domain URL',
                'Your full HTTPS Salesforce My Domain URL, such as https://example.my.salesforce.com. For a sandbox, use its sandbox My Domain URL. Leave blank to use salesforce_my_domain from config.php.',
                false, '', '', 'salesforce-client-credentials'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_TEXT,
                self::SETTING_CLIENT_CREDENTIALS_ID,
                'Consumer Key',
                'The Consumer Key for the app with Client Credentials enabled. Leave blank to use salesforce_client_credentials_id from config.php.',
                false, '', '', 'salesforce-client-credentials'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_PASSWORD,
                self::SETTING_CLIENT_CREDENTIALS_SECRET,
                'Consumer Secret',
                'Enter the app Consumer Secret. Leave blank to use salesforce_client_credentials_secret from config.php. Clear a saved value to remove its override.',
                false, '', '', 'salesforce-client-credentials'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_TEXT,
                self::SETTING_CLIENT_ID,
                'Consumer Key',
                'The Consumer Key for your legacy Connected App. Leave blank to use salesforce_client_id from config.php.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_PASSWORD,
                self::SETTING_CLIENT_SECRET,
                'Consumer Secret',
                'Enter the app Consumer Secret. Leave blank to use salesforce_client_secret from config.php. Clear a saved value to remove its override.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_TEXT,
                self::SETTING_USER_LOGIN,
                'Username',
                'Your Salesforce username. Leave blank to use salesforce_username from config.php.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_PASSWORD,
                self::SETTING_USER_PASSWORD,
                'Password',
                'Your Salesforce password. Leave blank to use salesforce_password from config.php. Clear a saved value to remove its override.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_CONFIG_PASSWORD,
                self::SETTING_SECURITY_TOKEN,
                'Security Token (optional)',
                'If Salesforce requires a security token, enter it here. It is appended to the password automatically. Leave blank if your password already includes the token, or use salesforce_security_token from config.php.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_SANDBOX,
                'Sandbox mode',
                'For Username-Password authentication, connects to test.salesforce.com instead of login.salesforce.com.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_CUSTOM_URL,
                'Using custom URL?',
                'Enable this if your legacy Salesforce account uses a custom URL to login.',
                false, '', '', 'salesforce-password'
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_LEAD_OWNER,
                'Assign lead owner?',
                'Enabling this will have leads assigned to a lead owner based on lead owner assignment rules in Salesforce.',
                false
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_INTERNAL,
                self::SETTING_INSTANCE,
                'Instance',
                'Fetched automatically when credentials are authorized.',
                false
            ),
        ];
    }

    /**
     * A method that initiates the authentication
     */
    public function initiateAuthentication(): void
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
        $this->setAccessTokenUpdated(false);
        $client = $this->createClient();

        $clientId     = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        if (!$clientId || !$clientSecret) {
            throw new IntegrationException('A Salesforce Consumer Key and Consumer Secret are required. Enter them in the integration or configure them in config.php.');
        }

        $payload = [
            'grant_type'    => $this->usesClientCredentials() ? 'client_credentials' : 'password',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ];

        if (!$this->usesClientCredentials()) {
            $username = $this->getUsername();
            $password = $this->getPassword();
            if (!$username || !$password) {
                throw new IntegrationException('A Salesforce username and password are required for Username-Password authentication.');
            }

            $payload['username'] = $username;
            $payload['password'] = $password;
        }

        $tokenUrl = $this->getAccessTokenUrl();
        $body = http_build_query($payload);

        try {
            $response = $client->post($tokenUrl, [
                'allow_redirects' => !$this->usesClientCredentials(),
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $body,
            ]);

            $json = json_decode($response->getBody());

            if (!isset($json->access_token) || !is_string($json->access_token) || $json->access_token === '') {
                throw new IntegrationException(
                    $this->getTranslator()->translate(
                        'No \'access_token\' present in auth response for {serviceProvider}',
                        ['serviceProvider' => $this->getServiceProvider()]
                    )
                );
            }

            $this->onAfterFetchAccessToken($json);

            $this->setAccessToken($json->access_token);
            $this->setAccessTokenUpdated(true);

        } catch (GuzzleException $e) {
            // Do not include the request, credentials, or response body in errors.
            $message = 'Salesforce authentication failed. Check your authentication method, credentials, My Domain URL, and Salesforce app policies.';
            $this->getLogger()->error($message, self::LOG_CATEGORY);
            throw new IntegrationException($message);
        }

        return $this->getAccessToken();
    }

    /**
     * Perform anything necessary before this integration is saved
     *
     * @param IntegrationStorageInterface $model
     */
    public function onBeforeSave(IntegrationStorageInterface $model): void
    {
        // Preserve incomplete legacy configurations; validate Client Credentials before saving.
        if (!$this->usesClientCredentials() && (
            !$this->getClientId() || !$this->getClientSecret() || !$this->getUsername() || !$this->getPassword()
        )) {
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
    public function checkConnection(): bool
    {
        if (!$this->getAccessToken() || !$this->getSetting(self::SETTING_INSTANCE)) {
            return false;
        }

        $client   = $this->createClient();
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
     * @throws Exception
     */
    public function pushObject(array $keyValueList, ?array $formFields = NULL): bool
    {
        $client   = $this->createClient();
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

        try {
            $response = $client->get($this->getEndpoint('/sobjects/Lead/describe'));
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
    public function refreshToken(): bool
    {
        try {
            return (bool) $this->fetchAccessToken();
        } catch (IntegrationException $e) {
            return false;
        }
    }

    /**
     * @param FieldObject $fieldObject
     * @param mixed|null  $value
     *
     * @return bool|string
     */
    public function convertCustomFieldValue(FieldObject $fieldObject, mixed $value = null): bool|string
    {
        if ($fieldObject->getType() === FieldObject::TYPE_ARRAY) {
            return is_array($value) ? implode(';', $value) : $value;
        }

        return parent::convertCustomFieldValue($fieldObject, $value);
    }

    /**
     * @throws CRMIntegrationNotFoundException
     */
    protected function onAfterFetchAccessToken(stdClass $responseData)
    {
        if (!isset($responseData->instance_url) || !is_string($responseData->instance_url) || $responseData->instance_url === '') {
            throw new CRMIntegrationNotFoundException('Salesforce response data doesn\'t contain the instance URL');
        }

        $this->setSetting(self::SETTING_INSTANCE, $responseData->instance_url);
    }

    /**
     * URL pointing to the OAuth2 authorization endpoint
     *
     * @return string
     */
    protected function getAuthorizeUrl(): string
    {
        return 'https://' . $this->getLoginUrl() . '.salesforce.com/services/oauth2/authorize';
    }

    /**
     * URL pointing to the OAuth2 access token endpoint
     *
     * @return string
     */
    protected function getAccessTokenUrl(): string
    {
        if ($this->usesClientCredentials()) {
            $url = rtrim(trim((string) $this->getSetting(self::SETTING_MY_DOMAIN)), '/');
            // Only accept a Salesforce My Domain origin, never a token endpoint or arbitrary host.
            if (!preg_match('~^https://[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.sandbox)?\.my\.salesforce\.com$~i', $url)) {
                throw new IntegrationException('Client Credentials requires a Salesforce HTTPS My Domain URL, such as https://example.my.salesforce.com. Enter it below or set salesforce_my_domain in config.php.');
            }

            return $url . '/services/oauth2/token';
        }

        return 'https://' . $this->getLoginUrl() . '.salesforce.com/services/oauth2/token';
    }

    private function usesClientCredentials(): bool
    {
        $value = $this->getSetting(self::SETTING_CLIENT_CREDENTIALS);

        return $value === null ? !$this->getId() : ($value === true || $value === 'y');
    }

    /**
     * @return string
     */
    protected function getApiRootUrl(): string
    {
        $instance        = $this->getSetting(self::SETTING_INSTANCE);
        $usingCustomUrls = $this->getSetting(self::SETTING_CUSTOM_URL);

        if ($instance && !str_starts_with($instance, 'https://')) {
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
    private function getLoginUrl(): string
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
        return $this->getSetting($this->usesClientCredentials() ? self::SETTING_CLIENT_CREDENTIALS_ID : self::SETTING_CLIENT_ID);
    }

    /**
     * @return mixed|null
     */
    private function getClientSecret()
    {
        return $this->getSetting($this->usesClientCredentials() ? self::SETTING_CLIENT_CREDENTIALS_SECRET : self::SETTING_CLIENT_SECRET);
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
        return (string) $this->getSetting(self::SETTING_USER_PASSWORD) . (string) $this->getSetting(self::SETTING_SECURITY_TOKEN);
    }
}
