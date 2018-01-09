<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait StaticValueTrait
{
    /** @var mixed */
    protected $staticValue;

    /**
     * @return mixed
     */
    public function getStaticValue()
    {
        return $this->staticValue;
    }
}
