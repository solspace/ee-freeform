<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
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

class Pipedrive extends AbstractCRMIntegration
{
    const SETTING_API_TOKEN = 'api_token';
    const TITLE             = 'Pipedrive';
    const LOG_CATEGORY      = 'Pipedrive';

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
                self::SETTING_API_TOKEN,
                'API Token',
                'Enter your Pipedrive API token here.',
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
        $client->setDefaultOption('query', ['api_token' => $this->getAccessToken()]);

        $organizationFields = $personFields = $dealFields = $notesFields = [];
        foreach ($keyValueList as $key => $value) {
            $matches = null;
            if (preg_match('/^org___(.*)$/', $key, $matches)) {
                $organizationFields[$matches[1]] = $value;
            } else if (preg_match('/^prsn___(.*)$/', $key, $matches)) {
                $personFields[$matches[1]] = $value;
            } else if (preg_match('/^deal___(.*)$/', $key, $matches)) {
                $dealFields[$matches[1]] = $value;
            } else if (preg_match('/^note___(deal|org|prsn)$/', $key, $matches)) {
                $notesFields[$matches[1]] = $value;
            }
        }

        $organizationId = null;
        if ($organizationFields) {
            try {
                $request = $client->post($this->getEndpoint('/v1/organizations'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(json_encode($organizationFields));
                $response = $request->send();

                $json = json_decode($response->getBody(true));
                if (isset($json->data->id)) {
                    $organizationId = $json->data->id;
                }
            } catch (BadResponseException $e) {
                $responseBody = $e->getResponse()->getBody(true);

                $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $responseBody, self::LOG_CATEGORY);
                $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage(), self::LOG_CATEGORY);
            } catch (\Exception $e) {
                $this->getLogger()->log(LoggerInterface::LEVEL_WARNING, $e->getMessage(), self::LOG_CATEGORY);
            }
        }

        $personId = null;
        if ($personFields) {
            try {
                if ($organizationId) {
                    $personFields['org_id'] = $organizationId;
                }

                $request = $client->post($this->getEndpoint('/v1/persons'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(json_encode($personFields));
                $response = $request->send();

                $json = json_decode($response->getBody(true));
                if (isset($json->data->id)) {
                    $personId = $json->data->id;
                }
            } catch (BadResponseException $e) {
                $responseBody = $e->getResponse()->getBody(true);

                $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $responseBody, self::LOG_CATEGORY);
                $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage(), self::LOG_CATEGORY);
            } catch (\Exception $e) {
                $this->getLogger()->log(LoggerInterface::LEVEL_WARNING, $e->getMessage(), self::LOG_CATEGORY);
            }
        }

        $dealId = null;
        try {
            if ($personId) {
                $dealFields['person_id'] = $personId;
            }

            if ($organizationId) {
                $dealFields['org_id'] = $organizationId;
            }

            $request = $client->post($this->getEndpoint('/v1/deals'));
            $request->setHeader('Content-Type', 'application/json');
            $request->setBody(json_encode($dealFields));
            $response = $request->send();

            $json   = json_decode($response->getBody(true), false);
            $dealId = $json->data->id;
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $responseBody, self::LOG_CATEGORY);
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage(), self::LOG_CATEGORY);
        } catch (\Exception $e) {
            $this->getLogger()->log(LoggerInterface::LEVEL_WARNING, $e->getMessage(), self::LOG_CATEGORY);
        }

        try {
            if ($dealId && !empty($notesFields['deal'])) {
                $request = $client->post($this->getEndpoint('/v1/notes'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(
                    json_encode(
                        [
                            'content'             => $notesFields['deal'],
                            'deal_id'             => $dealId,
                            'pinned_to_deal_flag' => '1',
                        ]
                    )
                );
                $request->send();
            }

            if ($organizationId && !empty($notesFields['org'])) {
                $request = $client->post($this->getEndpoint('/v1/notes'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(
                    json_encode(
                        [
                            'content'                     => $notesFields['org'],
                            'org_id'                      => $organizationId,
                            'pinned_to_organization_flag' => '1',
                        ]
                    )
                );
                $request->send();
            }

            if ($personId && !empty($notesFields['prsn'])) {
                $request = $client->post($this->getEndpoint('/v1/notes'));
                $request->setHeader('Content-Type', 'application/json');
                $request->setBody(
                    json_encode(
                        [
                            'content'               => $notesFields['prsn'],
                            'person_id'             => $personId,
                            'pinned_to_person_flag' => '1',
                        ]
                    )
                );
                $request->send();
            }
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody(true);

            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $responseBody, self::LOG_CATEGORY);
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage(), self::LOG_CATEGORY);
        } catch (\Exception $e) {
            $this->getLogger()->log(LoggerInterface::LEVEL_WARNING, $e->getMessage(), self::LOG_CATEGORY);
        }

        return (bool) $dealId;
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    public function checkConnection()
    {
        try {
            $response = $this->getResponse(
                $this->getEndpoint('/v1/deals'),
                ['query' => ['limit' => 1]]
            );

            $json = json_decode($response->getBody(true), false);

            return isset($json->success) && $json->success === true;
        } catch (BadResponseException $exception) {
            throw new IntegrationException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * Fetch the custom fields from the integration
     *
     * @return FieldObject[]
     */
    public function fetchFields()
    {
        $endpoints = [
            'prsn' => 'personFields',
            'org'  => 'organizationFields',
            'deal' => 'dealFields',
        ];

        $allowedFields = [
            'name',
            'phone',
            'email',
            'title',
            'value',
            'currency',
            'stage_id',
            'status',
            'probability',
        ];

        $requredFields = [
            'name',
            'title',
        ];

        $fieldList = [];
        foreach ($endpoints as $category => $endpoint) {
            $response = $this->getResponse(
                $this->getEndpoint('/v1/' . $endpoint),
                ['query' => ['limit' => 999]]
            );

            $json = json_decode($response->getBody(true), false);

            if (!isset($json->success) || !$json->success) {
                throw new IntegrationException("Could not fetch fields for {$category}");
            }

            foreach ($json->data as $fieldInfo) {
                switch ($fieldInfo->field_type) {
                    case 'varchar':
                    case 'varchar_auto':
                    case 'text':
                    case 'date':
                    case 'enum':
                    case 'time':
                    case 'timerange':
                    case 'daterange':
                        $type = FieldObject::TYPE_STRING;
                        break;

                    case 'set':
                    case 'phone':
                        $type = FieldObject::TYPE_ARRAY;
                        break;

                    case 'int':
                    case 'double':
                    case 'monetary':
                    case 'user':
                    case 'org':
                    case 'people':
                        $type = FieldObject::TYPE_NUMERIC;
                        break;

                    default:
                        continue 2;
                }

                if (
                    preg_match('/[a-z0-9]{40}/i', $fieldInfo->key)
                    || \in_array($fieldInfo->key, $allowedFields, true)
                ) {
                    $fieldList[] = new FieldObject(
                        "{$category}___{$fieldInfo->key}",
                        "($category) {$fieldInfo->name}",
                        $type,
                        \in_array($fieldInfo->key, $requredFields, true)
                    );
                }
            }

            $fieldList[] = new FieldObject(
                "note___{$category}",
                "({$category}) Note",
                FieldObject::TYPE_STRING,
                false
            );
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
        return $this->getSetting(self::SETTING_API_TOKEN);
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
        $model->updateAccessToken($this->getSetting(self::SETTING_API_TOKEN));
    }

    /**
     * @param       $endpoint
     * @param array $queryOptions
     *
     * @return \Guzzle\Http\Message\Response
     */
    private function getResponse($endpoint, array $queryOptions = [])
    {
        $client = new Client();
        $client->setDefaultOption(
            'query',
            array_merge(
                ['api_token' => $this->getAccessToken()],
                $queryOptions
            )
        );

        $request = $client->get($endpoint);
        $request->setHeader('Accept', 'application/json');

        return $request->send();
    }

    /**
     * @return string
     */
    protected function getApiRootUrl()
    {
        return 'https://api.pipedrive.com/';
    }
}

