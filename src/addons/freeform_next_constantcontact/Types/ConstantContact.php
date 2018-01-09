<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNextConstantContact\Types;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListOAuthConnector;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class ConstantContact extends MailingListOAuthConnector
{
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
        $client   = new Client();
        $client->setDefaultOption('query', ['api_key' => $this->getClientId()]);

        $endpoint = $this->getEndpoint('/account/info');

        try {
            $request = $client->get($endpoint);
            $request->setHeader('Authorization', 'Bearer ' . $this->getAccessToken());
            $response = $request->send();

            $body = $response->getBody();
            $json = json_decode($body);

            return isset($json->email);

        } catch (BadResponseException $e) {
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());

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
        $client = new Client();
        $client->setDefaultOption('query', ['api_key' => $this->getClientId()]);

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
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        $status = $response->getStatusCode();
        if ($status !== 201) {
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, 'Could not add contacts to list');

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
        $client   = new Client();
        $client->setDefaultOption('query', ['api_key' => $this->getClientId()]);

        $endpoint = $this->getEndpoint('/lists');

        try {
            $request = $client->get($endpoint);
            $request->setHeader('Authorization', 'Bearer ' . $this->getAccessToken());
            $response = $request->send();

        } catch (BadResponseException $e) {
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());

            throw new IntegrationException(
                $this->getTranslator()->translate('Could not connect to API endpoint')
            );
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, 'Could not fetch ConstantContact lists');

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
     * URL pointing to the OAuth2 authorization endpoint
     *
     * @return string
     */
    protected function getAuthorizeUrl()
    {
        return 'https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize';
    }

    /**
     * URL pointing to the OAuth2 access token endpoint
     *
     * @return string
     */
    protected function getAccessTokenUrl()
    {
        return 'https://oauth2.constantcontact.com/oauth2/oauth/token';
    }
}
