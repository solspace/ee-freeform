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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait InitialValueTrait
{
    /** @var string */
    protected $initialValue;

    /**
     * @return string
     */
    public function getInitialValue()
    {
        return $this->initialValue;
    }
}
