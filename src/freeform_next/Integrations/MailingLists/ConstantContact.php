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

class ConstantContact extends AbstractMailingListIntegration
{
    const TITLE        = 'Constant Contact';
    const LOG_CATEGORY = 'ConstantContact';

    const SETTING_API_KEY      = 'api_key';
    const SETTING_ACCESS_TOKEN = 'access_token';

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
                'Enter your App API key here.',
                true
            ),
            new SettingBlueprint(
                SettingBlueprint::TYPE_TEXT,
                self::SETTING_ACCESS_TOKEN,
                'Access Token',
                'Enter your access token here.',
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
        return 'Constant Contact';
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     * @throws IntegrationException
     */
    public function checkConnection()
    {
        $client = new Client();
        $client->setDefaultOption('query', ['api_key' => $this->getApiKey()]);

        $endpoint = $this->getEndpoint('/account/info');

        try {
            $request = $client->get($endpoint);
            $request->setHeader('Authorization', 'Bearer ' . $this->getAccessToken());
            $response = $request->send();

            $body = $response->getBody();
            $json = json_decode($body);

            return isset($json->email);

        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->error($responseBody, self::LOG_CATEGORY);
            $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);

            return false;
        }
    }

    public function initiateAuthentication()
    {
    }

    /**
     * @return string
     * @throws IntegrationException
     */
    public function fetchAccessToken()
    {
        return $this->getSetting(self::SETTING_ACCESS_TOKEN);
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
        $model->updateAccessToken($this->getSetting(self::SETTING_ACCESS_TOKEN));
        $model->updateSettings($this->getSettings());
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
        $client = new Client();
        $client->setDefaultOption('query', ['api_key' => $this->getApiKey()]);

        try {
            $emailAddresses = [];
            foreach ($emails as $email) {
                $emailAddresses[] = ['email_address' => $email];
            }

            $data = array_merge(
                [
                    'email_addresses' => $emailAddresses,
                    'lists'           => [['id' => $mailingList->getId()]],
                ],
                $mappedValues
            );

            $request = $client->post($this->getEndpoint('/contacts'));
            $request->setHeader('Authorization', 'Bearer ' . $this->getAccessToken());
            $request->setHeader('Content-Type', 'application/json');
            $request->setBody(json_encode($data));
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
        if ($status !== 201) {
            $this->getLogger()->error('Could not add contacts to list', self::LOG_CATEGORY);

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not add emails to lists')
            );
        }

        return $status === 200;
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
        $client->setDefaultOption('query', ['api_key' => $this->getApiKey()]);

        $endpoint = $this->getEndpoint('/lists');

        try {
            $request = $client->get($endpoint);
            $request->setHeader('Authorization', 'Bearer ' . $this->getAccessToken());
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
            $this->getLogger()->error('Could not fetch ConstantContact lists', self::LOG_CATEGORY);

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
                    $list->contact_count
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
     * @throws IntegrationException
     */
    protected function fetchFields($listId)
    {
        return [
            new FieldObject('first_name', 'First Name', FieldObject::TYPE_STRING, false),
            new FieldObject('last_name', 'Last Name', FieldObject::TYPE_STRING, false),
            new FieldObject('job_title', 'Job Title', FieldObject::TYPE_STRING, false),
            new FieldObject('company_name', 'Company Name', FieldObject::TYPE_STRING, false),
            new FieldObject('cell_phone', 'Cell Phone', FieldObject::TYPE_STRING, false),
            new FieldObject('home_phone', 'Home Phone', FieldObject::TYPE_STRING, false),
            new FieldObject('fax', 'Fax', FieldObject::TYPE_STRING, false),
        ];
    }

    /**
     * Returns the API root url without endpoints specified
     *
     * @return string
     * @throws IntegrationException
     */
    protected function getApiRootUrl()
    {
        return 'https://api.constantcontact.com/v2/';
    }

    /**
     * @return string
     * @throws IntegrationException
     */
    protected function getApiKey()
    {
        return $this->getSetting(self::SETTING_API_KEY);
    }
}
