<?php

namespace Solspace\Addons\FreeformNext\Integrations\CRM\Salesforce;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;

abstract class AbstractSalesforceIntegration extends AbstractCRMIntegration
{
    abstract protected function getAuthorizationCheckUrl(): string;

    protected function generateAuthorizedClient(bool $refreshTokenIfExpired = true): Client
    {
        $client = new Client([
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
                    $client = new Client([
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
