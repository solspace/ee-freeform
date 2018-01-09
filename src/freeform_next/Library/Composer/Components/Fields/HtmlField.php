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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\HashAsHandleTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleStaticValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;

class HtmlField extends AbstractField implements SingleValueInterface, InputOnlyInterface, NoStorageInterface
{
    use SingleStaticValueTrait;
    use HashAsHandleTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_HTML;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        return $this->getValue();
    }
}
