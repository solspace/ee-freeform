<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

/**
 * Created by PhpStorm.
 * User: gustavs
 * Date: 17.8.2
 * Time: 17:12
 */

namespace Solspace\Addons\FreeformNext\Services;


use Solspace\Addons\FreeformNext\Library\Composer\Components\Layout;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\IntegrationProperties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\CRMIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;

class CrmService implements CRMHandlerInterface
{
    public function getAllIntegrations()
    {
        // TODO: Implement getAllIntegrations() method.
    }

    public function getIntegrationById($id)
    {
        // TODO: Implement getIntegrationById() method.
    }

    public function updateFields(AbstractCRMIntegration $integration, array $fields)
    {
        // TODO: Implement updateFields() method.
    }

    public function getFields(AbstractCRMIntegration $integration)
    {
        // TODO: Implement getFields() method.
    }

    public function flagIntegrationForUpdating(AbstractCRMIntegration $integration)
    {
        // TODO: Implement flagIntegrationForUpdating() method.
    }

    public function pushObject(IntegrationProperties $properties, Layout $layout)
    {
        // TODO: Implement pushObject() method.
    }
}