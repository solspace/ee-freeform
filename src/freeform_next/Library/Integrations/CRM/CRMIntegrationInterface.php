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
    public function pushObject(array $keyValueList, $formFields = null);
}
