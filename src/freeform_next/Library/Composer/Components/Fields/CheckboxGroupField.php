<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;

class CheckboxGroupField extends AbstractExternalOptionsField implements MultipleValueInterface
{
    use MultipleValueTrait;

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

        foreach ($this->options as $index => $option) {
            $isSelected = in_array($option->getValue(), $this->getValue(), false);

            $output .= '<label>';

            $output .= '<input '
                . $this->getAttributeString("name", $this->getHandle() . "[]")
                . $this->getAttributeString("type", "checkbox")
                . $this->getAttributeString("class", $attributes->getClass())
                . $this->getAttributeString("id", $this->getIdAttribute($index + 1))
                . $this->getAttributeString("value", $option->getValue(), true)
                . $this->getParameterString('checked', $isSelected)
                . $attributes->getInputAttributesAsString()
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
