<?php
/**
 * Created by PhpStorm.
 * User: gustavs
 * Date: 30/08/2017
 * Time: 17:29.
 */

namespace Solspace\Addons\FreeformNext\Services;

use Psr\Http\Message\ResponseInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;

abstract class AbstractIntegrationService
{
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_FETCH_TYPES = 'fetchTypes';
    const EVENT_BEFORE_PUSH = 'beforePush';
    const EVENT_AFTER_PUSH = 'afterPush';
    const EVENT_AFTER_RESPONSE = 'afterResponse';



    /**
     * {@inheritDoc}
     */
    public function onAfterResponse(AbstractIntegration $integration, ResponseInterface $response)
    {
    }

    /**
     * Perform necessary actions after the integration has been saved.
     */
    protected function afterSaveHandler(IntegrationModel $model)
    {
    }
}
