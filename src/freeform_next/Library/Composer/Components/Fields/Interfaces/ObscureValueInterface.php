<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces;

interface ObscureValueInterface
{
    /**
     * Return the real value of this field
     * Instead of the obscured one
     *
     * @param mixed $obscuredValue
     *
     * @return mixed
     */
    public function getActualValue($obscuredValue);
}
