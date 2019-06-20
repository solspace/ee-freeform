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
