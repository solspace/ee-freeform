<?php

namespace Solspace\Addons\FreeformNext\Integrations\CRM\Salesforce;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;

abstract class AbstractSalesforceIntegration extends AbstractCRMIntegration
{
    abstract protected function getAuthorizationCheckUrl(): string;

    protected function createClient(array $config = []): Client
    {
        return new Client($config);
    }

    protected function generateAuthorizedClient(bool $refreshTokenIfExpired = true): Client
    {
        $client = $this->createClient([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($refreshTokenIfExpired) {
            try {
                $endpoint = $this->getAuthorizationCheckUrl();
                $client->get($endpoint);
            } catch (RequestException $e) {
                if (401 === $e->getCode()) {
                    $client = $this->createClient([
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->fetchAccessToken(),
                            'Content-Type' => 'application/json',
                        ],
                    ]);
                }
            }
        }

        return $client;
    }
}
