<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;

class MultipleSelectField extends AbstractExternalOptionsField implements MultipleValueInterface
{
    use MultipleValueTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_MULTIPLE_SELECT;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        $output = '<select '
            . $this->getAttributeString('name', $this->getHandle() . '[]')
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('class', $attributes->getClass())
            . $this->getParameterString('multiple', true)
            . $attributes->getInputAttributesAsString()
            . $this->getRequiredAttribute()
            . '>';

        foreach ($this->getOptions() as $option) {
            $output .= '<option value="' . $option->getValue() . '"' . ($option->isChecked() ? ' selected' : '') . '>';
            $output .= $this->translate($option->getLabel());
            $output .= '</option>';
        }

        $output .= '</select>';

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
