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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\PlaceholderInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\PlaceholderTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;

class TextField extends AbstractField implements SingleValueInterface, PlaceholderInterface
{
    use PlaceholderTrait;
    use SingleValueTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_TEXT;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        return '<input '
            . $this->getAttributeString("name", $this->getHandle())
            . $this->getAttributeString("type", $this->getType())
            . $this->getAttributeString("id", $this->getIdAttribute())
            . $this->getAttributeString("class", $attributes->getClass())
            . $this->getAttributeString(
                "placeholder",
                $this->translate($attributes->getPlaceholder() ?: $this->getPlaceholder())
            )
            . $this->getAttributeString("value", $this->getValue(), false)
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '/>';
    }
}
