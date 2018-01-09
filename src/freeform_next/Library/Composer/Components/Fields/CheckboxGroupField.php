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
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;

class CheckboxGroupField extends AbstractField implements MultipleValueInterface, OptionsInterface
{
    use MultipleValueTrait;
    use OptionsTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_CHECKBOX_GROUP;
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

        foreach ($this->options as $option) {
            $isSelected = in_array($option->getValue(), $this->getValue());

            $output .= '<label>';

            $output .= '<input '
                . $this->getAttributeString("name", $this->getHandle() . "[]")
                . $this->getAttributeString("type", "checkbox")
                . $this->getAttributeString("id", $this->getIdAttribute())
                . $this->getAttributeString("class", $attributes->getClass())
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
            return implode(', ', $this->getValue());
        }

        $labels = [];
        foreach ($this->getOptions() as $option) {
            if ($option->isChecked()) {
                $labels[] = $option->getLabel();
            }
        }

        return implode(', ', $labels);
    }
}
