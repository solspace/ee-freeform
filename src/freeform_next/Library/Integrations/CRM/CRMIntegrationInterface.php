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

namespace Solspace\Addons\FreeformNext\Library\Integrations\CRM;

use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;

interface CRMIntegrationInterface
{
    /**
     * Get a list of all fields that can be filled by the form
     *
     * @return FieldObject[]
     */
    public function getFields();

    /**
     * Push objects to the CRM
     *
     * @param array $keyValueList
     *
     * @return bool
     */
    public function pushObject(array $keyValueList);
}
