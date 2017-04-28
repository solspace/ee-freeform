<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Integrations\CRM;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class HubSpot extends AbstractCRMIntegration
{
    const TITLE           = 'HubSpot';
    const SETTING_API_KEY = 'api_key';
    const LOG_CATEGORY    = 'HubSpot';

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
                'Enter your HubSpot API key here.',
                true
            ),
        ];
    }

    /**
     * Push objects to the CRM
     *
     * @param array $keyValueList
     *
     * @return bool
     */
    public function pushObject(array $keyValueList)
    {
        $client = new Client();
        $client->setDefaultOption('query', ['hapikey' => $this->getAccessToken()]);

        $endpoint = $this->getEndpoint('/deals/v1/deal/');

        $dealProps    = [];
        $contactProps = [];
        $companyProps = [];

        foreach ($keyValueList as $key => $value) {
            preg_match('/^(\w+)___(.+)$/', $key, $matches);

            list ($all, $target, $propName) = $matches;

            switch ($target) {
                case 'contact':
                    $contactProps[] = ['value' => $value, 'property' => $propName];
                    break;

                case 'company':
                    $companyProps[] = ['value' => $value, 'name' => $propName];
                    break;

                case 'deal':
                    $dealProps[] = ['value' => $value, 'name' => $propName];
                    break;
            }
        }

        $contactId = null;
        if ($contactProps) {
            try {
                $request = $client->post($this->getEndpoint('/contacts/v1/contact'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(json_encode(['properties' => $contactProps]));
                $response = $request->send();

                $json = json_decode($response->getBody(true));
                if (isset($json->vid)) {
                    $contactId = $json->vid;
                }
            } catch (BadResponseException $e) {
                if ($e->getResponse()) {
                    $json = json_decode($e->getResponse()->getBody(true));
                    if (isset($json->error, $json->identityProfile) && $json->error === 'CONTACT_EXISTS') {
                        $contactId = $json->identityProfile->vid;
                    } else {
                        $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);
                    }
                }
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);
            }
        }

        $companyId = null;
        if ($companyProps) {
            try {
                $request = $client->post($this->getEndpoint('companies/v2/companies'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(json_encode(['properties' => $companyProps]));
                $response = $request->send();

                $json = json_decode($response->getBody());
                if (isset($json->companyId)) {
                    $companyId = $json->companyId;
                }
            } catch (BadResponseException $e) {
                $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage(), self::LOG_CATEGORY);
            }
        }

        $deal = [
            'properties' => $dealProps,
        ];

        if ($companyId || $contactId) {
            $deal['associations'] = [];

            if ($companyId) {
                $deal['associations']['associatedCompanyIds'] = [$companyId];
            }

            if ($contactId) {
                $deal['associations']['associatedVids'] = [$contactId];
            }
        }

        $request = $client->post($endpoint);
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(json_encode($deal));
        $response = $request->send();

        return $response->getStatusCode() === 200;
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    public function checkConnection()
    {
        $client = new Client();
        $client->setDefaultOption('query', ['hapikey' => $this->getAccessToken()]);

        $endpoint = $this->getEndpoint('/contacts/v1/lists/all/contacts/all');

        $request  = $client->get($endpoint);
        $response = $request->send();

        $json = json_decode($response->getBody(true), true);

        return isset($json['contacts']);
    }

    /**
     * Fetch the custom fields from the integration
     *
     * @return FieldObject[]
     */
    public function fetchFields()
    {
        $client = new Client();
        $client->setDefaultOption('query', ['hapikey' => $this->getAccessToken()]);

        $request  = $client->get($this->getEndpoint('/properties/v1/deals/properties/'));
        $response = $request->send();

        $data = json_decode($response->getBody(true));

        $fieldList = [
            new FieldObject('contact___email', 'Email', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___firstname', 'First Name', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___lastname', 'Last Name', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___website', 'Website', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___phone', 'Phone', FieldObject::TYPE_NUMERIC, false),
            new FieldObject('contact___address', 'Address', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___city', 'City', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___state', 'State', FieldObject::TYPE_STRING, false),
            new FieldObject('contact___zip', 'Zip', FieldObject::TYPE_NUMERIC, false),
            new FieldObject('company___name', 'Company Name', FieldObject::TYPE_STRING, false),
            new FieldObject('company___description', 'Company Description', FieldObject::TYPE_STRING, false),
        ];
        foreach ($data as $field) {
            if ($field->readOnlyValue || $field->hidden || $field->calculated) {
                continue;
            }

            $type = null;
            switch ($field->type) {
                case 'string':
                    $type = FieldObject::TYPE_STRING;
                    break;

                case 'number':
                    $type = FieldObject::TYPE_NUMERIC;
                    break;
            }

            if (null === $type) {
                continue;
            }

            $fieldObject = new FieldObject(
                'deal___' . $field->name,
                $field->label,
                $type,
                false
            );

            $fieldList[] = $fieldObject;
        }

        return $fieldList;
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
     * A method that initiates the authentication
     */
    public function initiateAuthentication()
    {
    }

    /**
     * Perform anything necessary before this integration is saved
     *
     * @param IntegrationStorageInterface $model
     */
    public function onBeforeSave(IntegrationStorageInterface $model)
    {
        $model->updateAccessToken($this->getSetting(self::SETTING_API_KEY));
    }

    /**
     * @return string
     */
    protected function getApiRootUrl()
    {
        return 'https://api.hubapi.com/';
    }
}
