<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Integrations\CRM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class SharpSpring extends AbstractCRMIntegration {

	const SETTING_SECRET_KEY = 'secret_key';
	const SETTING_ACCOUNT_ID = 'account_id';
	const TITLE = 'SharpSpring';
	const LOG_CATEGORY = 'SharpSpring';

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
				self::SETTING_ACCOUNT_ID,
				'Account ID',
				'Enter your Account ID here.',
				true
			),
			new SettingBlueprint(
				SettingBlueprint::TYPE_TEXT,
				self::SETTING_SECRET_KEY,
				'Secret Key',
				'Enter your Secret Key here.',
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
	 * @throws IntegrationException
	 */
	public function pushObject(array $keyValueList)
	{
		$client = $this->getAuthorizedClient();

		$contactProps = [];

		foreach($keyValueList as $key => $value)
		{
			preg_match('/^(\w+)___(.+)$/', $key, $matches);

			list ($all, $target, $propName) = $matches;

			switch ($target)
			{
				case 'contact':
					$contactProps[$propName] = $value;
				break;
			}
		}


		$contactId = null;
		if($contactProps)
		{
			try
			{
				$payload  = $this->generatePayload('createLeads', ['objects' => [$contactProps]]);
				$request  = $client->post(null, null, $payload);
				$response = $request->send();

				$data = json_decode($response->getBody(true), true);

				$this->getLogger()->log(LoggerInterface::LEVEL_INFO, $response->getBody(true), self::LOG_CATEGORY);

				return (isset($data['result']['error']) && (count($data['result']['error']) === 0));
			} catch (BadResponseException $e)
			{
				if($e->getResponse())
				{
					$json = json_decode($e->getResponse()->getBody(true));
					$this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $json, self::LOG_CATEGORY);
					$this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage(), self::LOG_CATEGORY);
				}
			} catch (\Exception $e)
			{
				$this->getLogger()->log(LoggerInterface::LEVEL_WARNING, $e->getMessage(), self::LOG_CATEGORY);
			}
		}

		return false;
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
		$auth   = $this->getAuthorizedClient();
		$payload  = $this->generatePayload('getFields', ['where' => [], 'limit' => 1]);
		$options = array_merge($auth, ['body' => $payload]);
		$response  = $client->post($this->getApiRootUrl(), $options);
		$json = json_decode($response->getBody(true), true);

		return isset($json['result']['field']);
	}

	/**
	 * Fetch the custom fields from the integration
	 *
	 * @return FieldObject[]
	 * @throws IntegrationException
	 */
	public function fetchFields()
	{
		$client = new Client();
		$auth   = $this->getAuthorizedClient();
		$payload  = $this->generatePayload('getFields');
		$options = array_merge($auth, ['body' => $payload]);
		try {
			$response  = $client->post($this->getApiRootUrl(), $options);
			$data = json_decode($response->getBody(true), true);
		} catch (ClientException $e)
		{
			$data = json_decode($e->getMessage(), true);
		}

		$fields = [];
		if(isset($data['result']['field']))
		{
			$fields = $data['result']['field'];
		}

		$fieldList = [
			new FieldObject('contact___emailAddress', 'Email', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___firstName', 'First Name', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___lastName', 'Last Name', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___website', 'Website', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___phoneNumber', 'Phone Number', FieldObject::TYPE_NUMERIC, false),
			new FieldObject('contact___phoneNumberExtension', 'Phone Number Extension', FieldObject::TYPE_NUMERIC, false),
			new FieldObject('contact___faxNumber', 'Fax Number', FieldObject::TYPE_NUMERIC, false),
			new FieldObject('contact___mobilePhoneNumber', 'Mobile Phone Number', FieldObject::TYPE_NUMERIC, false),
			new FieldObject('contact___street', 'Street Address', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___city', 'City', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___state', 'State', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___zipcode', 'Zip', FieldObject::TYPE_NUMERIC, false),
			new FieldObject('contact___companyName', 'Company Name', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___industry', 'Industry', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___description', 'Description', FieldObject::TYPE_STRING, false),
			new FieldObject('contact___title', 'Title', FieldObject::TYPE_STRING, false),
		];

		foreach($fields as $field)
		{
			if(! $field || ! is_object($field) || $field->readOnlyValue || $field->hidden || $field->calculated)
			{
				continue;
			}

			$type = null;
			switch ($field->dataType)
			{
				case 'text':
				case 'string':
				case 'picklist':
				case 'phone':
				case 'url':
				case 'textarea':
				case 'country':
				case 'checkbox':
				case 'date':
				case 'bit':
				case 'hidden':
				case 'state':
				case 'radio':
				case 'datetime':
					$type = FieldObject::TYPE_STRING;
				break;

				case 'int':
					$type = FieldObject::TYPE_NUMERIC;
				break;

				case 'boolean':
					$type = FieldObject::TYPE_BOOLEAN;
				break;
			}

			if(null === $type)
			{
				continue;
			}

			$fieldObject = new FieldObject(
				$field->systemName,
				$field->label,
				$type,
				false
			);

			$fieldList[] = $fieldObject;
		}

		return $fieldList;
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
	 *
	 * @throws IntegrationException
	 */
	public function onBeforeSave(IntegrationStorageInterface $model)
	{
		$accountId = $this->getAccountID();
		$secretKey = $this->getSecretKey();

		// If one of these isn't present, we just return void
		if(! $accountId || ! $secretKey)
		{
			return;
		}

		$model->updateSettings($this->getSettings());
	}

	/**
	 * Gets the API secret for SharpSpring from settings config
	 *
	 * @return mixed|null
	 * @throws IntegrationException
	 */
	private function getSecretKey()
	{
		return $this->getSetting(self::SETTING_SECRET_KEY);
	}

	/**
	 * Gets the account ID for SharpSpring from settings config
	 *
	 * @return mixed|null
	 * @throws IntegrationException
	 */
	private function getAccountID()
	{
		return $this->getSetting(self::SETTING_ACCOUNT_ID);
	}

	/**
	 * Get the base SharpSpring API URL
	 *
	 * @return string
	 */
	protected function getApiRootUrl()
	{
		return 'https://api.sharpspring.com/pubapi/v1.2/';
	}

	/**
	 * Generate a properly formatted payload for SharpSpring API
	 *
	 * @param        $method
	 * @param array  $params
	 * @param string $id
	 *
	 * @return string
	 */
	private function generatePayload($method, array $params = ['where' => []], $id = 'freeform')
	{
		return json_encode(
			[
				'method' => $method,
				'params' => $params,
				'id'     => $id,
			]
		);
	}

	/**
	 * Authorizes the application
	 * Returns the access_token
	 *
	 * @return void
	 */
	public function fetchAccessToken()
	{
	}

	/**
	 * Sets presets for authorization on a Guzzle Client
	 *
	 * @return Client
	 * @throws IntegrationException
	 */
	private function getAuthorizedClient()
	{
		return [
			'query' => [
				'accountID' => $this->getAccountID(),
				'secretKey' => $this->getSecretKey(),
			],
		];
		// $request = new Request('POST', $this->getApiRootUrl(), [
		// 	'query' => [
		// 		'accountID' => $this->getAccountID(),
		// 		'secretKey' => $this->getSecretKey(),
		// 	],
		// ]);
		// $client->setBaseUrl($this->getApiRootUrl());
		// $client->setDefaultOption(
		// 	'query',
		// 	[
		// 		'accountID' => $this->getAccountID(),
		// 		'secretKey' => $this->getSecretKey(),
		// 	]
		// );
		//
		// return $client;
	}
}
