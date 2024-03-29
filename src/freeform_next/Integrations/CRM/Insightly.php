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
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class Insightly extends AbstractCRMIntegration
{
    const SETTING_API_KEY = 'api_key';

    const TITLE        = 'Insightly';
    const LOG_CATEGORY = 'Insightly';

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
                'Enter your Insightly API key here.',
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
    public function pushObject(array $keyValueList, $formFields = NULL)
    {
        $client = new Client();
		$response = $client->get($this->getEndpoint('/Leads'), [
			'headers' => [
				'Content-Type' => 'application/json'
			],
			'form_params' => json_encode($keyValueList),
			'auth' => [null, $this->getAccessToken()]
		]);
        // $request->getCurlOptions()->set(CURLOPT_USERPWD, $this->getAccessToken());
        // $request->getCurlOptions()->set(CURLOPT_POSTFIELDS, json_encode($keyValueList));

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

		$response = $client->get($this->getEndpoint('/Leads'), [
			'headers' => [
				'Content-Type' => 'application/json'
			],
			'auth' => [null, $this->getAccessToken()]
		]);
        // $request = $client->get($this->getEndpoint('/Leads'));
        // $request->setHeader('Content-Type', 'application/json');
        // $request->getCurlOptions()->set(CURLOPT_USERPWD, $this->getAccessToken());

        // $response = $request->send();

        return $response->getStatusCode() === 200;
    }

    /**
     * Fetch the custom fields from the integration
     *
     * @return FieldObject[]
     */
    public function fetchFields()
    {
        $fieldList = [
            new FieldObject('SALUTATION', 'Salutation', FieldObject::TYPE_STRING),
            new FieldObject('FIRST_NAME', 'First Name', FieldObject::TYPE_STRING),
            new FieldObject('LAST_NAME', 'Last Name', FieldObject::TYPE_STRING),
            new FieldObject('TITLE', 'Title', FieldObject::TYPE_STRING),
            new FieldObject('EMAIL', 'Email', FieldObject::TYPE_STRING),
            new FieldObject('PHONE', 'Phone', FieldObject::TYPE_STRING),
            new FieldObject('MOBILE', 'Mobile', FieldObject::TYPE_STRING),
            new FieldObject('FAX', 'Fax', FieldObject::TYPE_STRING),
            new FieldObject('WEBSITE', 'Website', FieldObject::TYPE_STRING),
            new FieldObject('ORGANISATION_NAME', 'Organisation Name', FieldObject::TYPE_STRING),
            new FieldObject('INDUSTRY', 'Industry', FieldObject::TYPE_STRING),
            new FieldObject('EMPLOYEE_COUNT', 'Employee Count', FieldObject::TYPE_STRING),
            new FieldObject('IMAGE_URL', 'Image URL', FieldObject::TYPE_STRING),
            new FieldObject('ADDRESS_STREET', 'Address - Street', FieldObject::TYPE_STRING),
            new FieldObject('ADDRESS_CITY', 'Address - City', FieldObject::TYPE_STRING),
            new FieldObject('ADDRESS_STATE', 'Address - State', FieldObject::TYPE_STRING),
            new FieldObject('ADDRESS_POSTCODE', 'Address - Postcode', FieldObject::TYPE_STRING),
            new FieldObject('ADDRESS_COUNTRY', 'Address - Country', FieldObject::TYPE_STRING),
            new FieldObject('LEAD_DESCRIPTION', 'Lead Description', FieldObject::TYPE_STRING),
            new FieldObject('LEAD_RATING', 'Lead Rating', FieldObject::TYPE_STRING),
        ];


        $client = new Client();
        $request = $client->get($this->getEndpoint('/CustomFields/Leads'));
        $request->setHeader('Content-Type', 'application/json');
        $request->getCurlOptions()->set(CURLOPT_USERPWD, $this->getAccessToken());

        $response = $request->send();

        $data = json_decode($response->getBody(true), false);
        foreach ($data as $field) {
            if (!$field->EDITABLE) {
                continue;
            }

            $type = null;

            switch ($field->FIELD_TYPE) {
                case 'TEXT':
                case 'DROPDOWN':
                case 'URL':
                case 'MULTILINETEXT':
                case 'DATE':
                    $type = FieldObject::TYPE_STRING;
                    break;

                case 'BIT':
                    $type = FieldObject::TYPE_BOOLEAN;
                    break;

                case 'NUMERIC':
                    $type = FieldObject::TYPE_NUMERIC;
                    break;
            }

            if (null === $type) {
                continue;
            }

            $fieldObject = new FieldObject(
                $field->FIELD_NAME,
                $field->FIELD_LABEL,
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
        return 'https://api.insightly.com/v3.0/';
    }
}
