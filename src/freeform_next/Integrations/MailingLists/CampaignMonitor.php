<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
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
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class CampaignMonitor extends AbstractMailingListIntegration
{
    const TITLE        = 'Campaign Monitor';
    const LOG_CATEGORY = 'CampaignMonitor';

    const SETTING_API_KEY   = 'api_key';
    const SETTING_CLIENT_ID = 'client_id';

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
                self::SETTING_API_KEY,
                'API Key',
                'Enter your Campaign Monitor API key here.',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_TEXT,
                self::SETTING_CLIENT_ID,
                'Client ID',
                'Enter your Campaign Monitor Client ID here.',
                true
            ),
        ];
    }

    /**
     * Returns the MailingList service provider short name
     * i.e. - MailChimp, Constant Contact, etc...
     *
     * @return string
     */
    public function getServiceProvider()
    {
        return 'Campaign Monitor';
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
            $request = $client->get($this->getEndpoint('/clients/' . $this->getClientID() . '.json'));
            $request->setAuth($this->getAccessToken(), 'freeform');
            $response = $request->send();

            $json = json_decode($response->getBody(true));

            return isset($json->ApiKey) && !empty($json->ApiKey);
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

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
        $endpoint = $this->getEndpoint("/subscribers/{$mailingList->getId()}.json");

        try {
            $customFields = [];
            foreach ($mappedValues as $key => $value) {
                if ($key === 'Name') {
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $customFields[] = [
                            'Key'   => $key,
                            'Value' => $subValue,
                        ];
                    }
                } else {
                    $customFields[] = [
                        'Key'   => $key,
                        'Value' => $value,
                    ];
                }
            }

            foreach ($emails as $email) {
                $data = [
                    'EmailAddress'                           => $email,
                    'Name'                                   => isset($mappedValues['Name']) ? $mappedValues['Name'] : '',
                    'CustomFields'                           => $customFields,
                    'Resubscribe'                            => true,
                    'RestartSubscriptionBasedAutoresponders' => true,
                ];

                $request = $client->post($endpoint);
                $request->setAuth($this->getAccessToken(), 'freeform');
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
        return $this->getSetting(self::SETTING_API_KEY);
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
        $model->updateAccessToken($this->getSetting(self::SETTING_API_KEY));
    }

    /**
     * Makes an API call that fetches mailing lists
     * Builds ListObject objects based on the results
     * And returns them
     *
     * @return ListObject[]
     * @throws IntegrationException
     */
    protected function fetchLists()
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint('/clients/' . $this->getClientID() . '/lists.json');

        try {
            $request = $client->get($endpoint);
            $request->setAuth($this->getAccessToken(), 'freeform');
            $response = $request->send();
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

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
        if (is_array($json)) {
            foreach ($json as $list) {
                if (isset($list->ListID) && isset($list->Name)) {
                    $lists[] = new ListObject(
                        $this,
                        $list->ListID,
                        $list->Name,
                        $this->fetchFields($list->ListID),
                        0
                    );
                }
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
     * @throws IntegrationException
     */
    protected function fetchFields($listId)
    {
        $client   = new Client();
        $endpoint = $this->getEndpoint("/lists/$listId/customfields.json");

        try {
            $request = $client->get($endpoint);
            $request->setAuth($this->getAccessToken(), 'freeform');
            $response = $request->send();
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        $json = json_decode($response->getBody(true));

        $fieldList = [
            new FieldObject('Name', 'Name', FieldObject::TYPE_STRING, false),
        ];

        if (is_array($json)) {
            foreach ($json as $field) {
                switch ($field->DataType) {
                    case 'Text':
                    case 'MultiSelectOne':
                        $type = FieldObject::TYPE_STRING;
                        break;

                    case 'Number':
                        $type = FieldObject::TYPE_NUMERIC;
                        break;

                    case 'MultiSelectMany':
                        $type = FieldObject::TYPE_ARRAY;
                        break;

                    default:
                        $type = null;
                        break;
                }

                if (null === $type) {
                    continue;
                }

                $fieldList[] = new FieldObject(
                    str_replace(['[', ']'], '', $field->Key),
                    $field->FieldName,
                    $type,
                    false
                );
            }
        }

        return $fieldList;
    }

    /**
     * Returns the API root url without endpoints specified
     *
     * @return string
     * @throws IntegrationException
     */
    protected function getApiRootUrl()
    {
        return 'https://api.createsend.com/api/v3.1/';
    }

    /**
     * @return string
     */
    private function getClientID()
    {
        return $this->getSetting(self::SETTING_CLIENT_ID);
    }
}
