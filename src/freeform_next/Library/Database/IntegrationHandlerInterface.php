<?php

namespace Solspace\Addons\FreeformNext\Library\Database;

use Psr\Http\Message\ResponseInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;

interface IntegrationHandlerInterface
{
    public function onAfterResponse(AbstractIntegration $integration, ResponseInterface $response);
}
