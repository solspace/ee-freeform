<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;

class RadioGroupField extends AbstractField implements SingleValueInterface, OptionsInterface
{
    use SingleValueTrait;
    use OptionsTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_RADIO_GROUP;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();
        $output     = '';

        foreach ($this->options as $index => $option) {
            $isSelected = $option->getValue() === $this->getValue();

            $output .= '<label>';

            $output .= '<input '
                . $this->getAttributeString("name", $this->getHandle())
                . $this->getAttributeString("type", "radio")
                . $this->getAttributeString("class", $attributes->getClass())
                . $this->getAttributeString("id", $this->getIdAttribute() . '-' . ($index + 1))
                . $this->getAttributeString("value", $option->getValue(), false)
                . $attributes->getInputAttributesAsString()
                . ($isSelected ? 'checked ' : '')
                . '/>';
            $output .= $this->translate($option->getLabel());
            $output .= '</label>';
        }

        return $output;
    }

    /**
     * @param bool $optionsAsValues
     *
     * @return string
     */
    public function getValueAsString($optionsAsValues = true)
    {
        if (!$optionsAsValues) {
            return $this->getValue();
        }

        foreach ($this->getOptions() as $option) {
            if ($option->isChecked()) {
                return $option->getLabel();
            }
        }

        return "";
    }
}
