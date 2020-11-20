<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Integrations\MailingLists;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;

class Dotmailer extends AbstractMailingListIntegration
{
    const TITLE        = 'Dotmailer';
    const LOG_CATEGORY = 'Dotmailer';

    const SETTING_USER_EMAIL    = 'user_email';
    const SETTING_USER_PASS     = 'user_pass';
    const SETTING_DOUBLE_OPT_IN = 'double_opt_in';
    const SETTING_ENDPOINT      = 'endpoint';

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
                SettingBlueprint::TYPE_TEXT,
                self::SETTING_USER_EMAIL,
                'API User Email',
                'Enter your Dotmailer API user email.',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_PASSWORD,
                self::SETTING_USER_PASS,
                'Password',
                'Enter your Dotmailer API user password',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_BOOL,
                self::SETTING_DOUBLE_OPT_IN,
                'Use double opt-in?',
                '',
                false
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_INTERNAL,
                self::SETTING_ENDPOINT,
                'Endpoint',
                '',
                false
            ),
        ];
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    public function checkConnection()
    {
        $client = new Client();

        try {
            $request = $client->get($this->getEndpoint('/account-info'));
            $request->setAuth($this->getUsername(), $this->getPassword());
            $response = $request->send();

            $json = json_decode($response->getBody(true));

            return isset($json->id) && !empty($json->id);
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->warn($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->warn($e->getMessage(), self::LOG_CATEGORY);

            return false;
        }
    }

    /**
     * Push emails to a specific mailing list for the service provider
     *
     * @param ListObject $mailingList
     * @param array      $emails
     * @param array      $mappedValues
     *
     * @return bool
     * @throws IntegrationException
     */
    public function pushEmails(ListObject $mailingList, array $emails, array $mappedValues)
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint('/address-books/' . $mailingList->getId() . '/contacts');

        try {
            foreach ($emails as $email) {
                $data = [
                    'email'     => $email,
                    'optInType' => $this->getSetting(self::SETTING_DOUBLE_OPT_IN) ? 'verifiedDouble' : 'single',
                ];

                if ($mappedValues) {
                    $data['dataFields'] = [];
                    foreach ($mappedValues as $key => $value) {
                        $data['dataFields'][] = [
                            'key'   => $key,
                            'value' => $value,
                        ];
                    }
                }

                $request = $client->post($endpoint);
                $request->setAuth($this->getUsername(), $this->getPassword());
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(json_encode($data));
                $request->send();
            }
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        return true;
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
        return $this->getSetting(self::SETTING_USER_EMAIL);
    }

    /**
     * Perform anything necessary before this integration is saved
     *
     * @param IntegrationStorageInterface $model
     *
     * @throws IntegrationException
     */
    public function onBeforeSave(IntegrationStorageInterface $model)
    {
        $client = new Client();
        $request = $client->get('https://api.dotmailer.com/v2/account-info');
        $request->setAuth($this->getUsername(), $this->getPassword());

        try {
            $response = $request->send();
            $json = json_decode($response->getBody(true));

            if (isset($json->properties)) {
                foreach ($json->properties as $property) {
                    if ($property->name === 'ApiEndpoint') {
                        $this->setSetting(self::SETTING_ENDPOINT, $property->value);
                        $model->updateSettings($this->getSettings());

                        return;
                    }
                }
            }
        } catch (BadResponseException $e) {
        }

        throw new IntegrationException('Could not get an API endpoint');
    }

    /**
     * Makes an API call that fetches mailing lists
     * Builds ListObject objects based on the results
     * And returns them
     *
     * @return \Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject[]
     * @throws IntegrationException
     */
    protected function fetchLists()
    {
        $client = new Client();
        $client->setDefaultOption('query', ['select' => 1000]);
        $endpoint = $this->getEndpoint('/address-books');

        try {
            $request = $client->get($endpoint);
            $request->setAuth($this->getUsername(), $this->getPassword());
            $response = $request->send();
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->warn($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->warn($e->getMessage(), self::LOG_CATEGORY);

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            throw new IntegrationException(
                $this->getTranslator()->translate(
                    'Could not fetch {serviceProvider} lists',
                    ['serviceProvider' => $this->getServiceProvider()]
                )
            );
        }

        $json = json_decode($response->getBody(true));

        $lists = [];
        foreach ($json as $list) {
            if (isset($list->id, $list->name)) {
                $lists[] = new ListObject(
                    $this,
                    $list->id,
                    $list->name,
                    $this->fetchFields($list->id),
                    $list->contacts
                );
            }
        }

        return $lists;
    }

    /**
     * Fetch all custom fields for each list
     *
     * @param string $listId
     *
     * @return FieldObject[]
     */
    protected function fetchFields($listId)
    {
        $client = new Client();

        $endpoint = $this->getEndpoint('/data-fields');

        try {
            $request = $client->get($endpoint);
            $request->setAuth($this->getUsername(), $this->getPassword());
            $response = $request->send();
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->warn($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->warn($e->getMessage(), self::LOG_CATEGORY);

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        $json = json_decode($response->getBody(true));

        if ($json) {
            $fieldList = [];
            foreach ($json as $field) {
                switch ($field->type) {
                    case 'String':
                    case 'Date':
                        $type = FieldObject::TYPE_STRING;
                        break;

                    case 'Boolean':
                        $type = FieldObject::TYPE_BOOLEAN;
                        break;

                    case 'Numeric':
                        $type = FieldObject::TYPE_NUMERIC;
                        break;

                    default:
                        $type = null;
                        break;
                }

                if (null === $type) {
                    continue;
                }

                $fieldList[] = new FieldObject(
                    $field->name,
                    $field->name,
                    $type,
                    false
                );
            }

            return $fieldList;
        }

        return [];
    }

    /**
     * Returns the API root url without endpoints specified
     *
     * @return string
     */
    protected function getApiRootUrl()
    {
        return rtrim($this->getSetting(self::SETTING_ENDPOINT), '/') . '/v2/';
    }

    /**
     * @return string
     * @throws IntegrationException
     */
    private function getUsername()
    {
        return $this->getSetting(self::SETTING_USER_EMAIL);
    }

    /**
     * @return string
     * @throws IntegrationException
     */
    private function getPassword()
    {
        return $this->getSetting(self::SETTING_USER_PASS);
    }
}
