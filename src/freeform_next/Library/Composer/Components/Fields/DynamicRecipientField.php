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
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\RecipientInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\OptionsTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\RecipientTrait;

class DynamicRecipientField extends AbstractField implements RecipientInterface, ObscureValueInterface, MultipleValueInterface, OptionsInterface
{
    use RecipientTrait;
    use MultipleValueTrait;
    use OptionsTrait;

    /** @var bool */
    protected $showAsRadio;

    /** @var bool */
    protected $showAsCheckboxes;

    /**
     * @return string
     */
    public static function getFieldType()
    {
        return FieldInterface::TYPE_DYNAMIC_RECIPIENTS;
    }

    /**
     * @return bool
     */
    public function isShowAsRadio()
    {
        return (bool) $this->showAsRadio;
    }

    /**
     * @return bool
     */
    public function isShowAsCheckboxes()
    {
        return (bool) $this->showAsCheckboxes;
    }

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return FieldInterface::TYPE_DYNAMIC_RECIPIENTS;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        if ($this->isShowAsRadio()) {
            return $this->renderAsRadios();
        }

        if ($this->isShowAsCheckboxes()) {
            return $this->renderAsCheckboxes();
        }

        return $this->renderAsSelect();
    }

    /**
     * @param bool $optionsAsValues
     *
     * @return string
     */
    public function getValueAsString($optionsAsValues = true)
    {
        if (!$optionsAsValues) {
            return $this->getActualValue($this->getValue());
        }

        $areIndexValues = true;
        foreach ($this->getValue() as $value) {
            if (!\is_numeric($value)) {
                $areIndexValues = false;
            }
        }

        $returnValues = [];
        foreach ($this->getOptions() as $index => $option) {
            $lookup = $areIndexValues ? $index : $option->getValue();
            if (\in_array($lookup, $this->getValue(), false)) {
                $returnValues[] = $option->getLabel();
            }
        }

        return implode(', ', $returnValues);
    }

    /**
     * Returns an array value of all possible recipient Email addresses
     *
     * Either returns an ["email", "email"] array
     * Or an array with keys as recipient names, like ["Jon Doe" => "email", ..]
     *
     * @return array
     */
    public function getRecipients()
    {
        /** @var Option[] $options */
        $options = $this->getOptions();
        $value   = $this->getValue();
        $recipients = [];

        if (null !== $value) {
            foreach ($options as $index => $option) {
                if (\in_array($index, $value, false)) {
                    $emails = explode(',', $option->getValue());
                    foreach ($emails as $email) {
                        $recipients[] = trim($email);
                    }
                }
            }
        }

        return $recipients;
    }

    /**
     * Return the real value of this field
     * Instead of the obscured one
     *
     * @param mixed $obscureValue
     *
     * @return mixed
     */
    public function getActualValue($obscureValue)
    {
        $options = $this->getOptions();

        if (\is_array($obscureValue)) {
            $list = [];
            foreach ($obscureValue as $value) {
                if (isset($options[$value])) {
                    $list[] = $options[$value]->getValue();
                }
            }

            return $list;
        }

        if (isset($options[$obscureValue])) {
            return $options[$obscureValue]->getValue();
        }

        return null;
    }

    /**
     * @return string
     */
    private function renderAsSelect()
    {
        $attributes = $this->getCustomAttributes();

        $output = '<select '
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', $this->getType())
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('class', $attributes->getClass())
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '>';

        foreach ($this->getOptions() as $index => $option) {
            $output .= '<option value="' . $index . '"' . ($option->isChecked() ? ' selected' : '') . '>';
            $output .= $this->getForm()->getTranslator()->translate($option->getLabel());
            $output .= '</option>';
        }

        $output .= '</select>';

        return $output;
    }

    /**
     * @return string
     */
    private function renderAsRadios()
    {
        $attributes = $this->getCustomAttributes();
        $output     = '';

        foreach ($this->getOptions() as $index => $option) {
            $output .= '<label>';

            $output .= '<input '
                . $this->getAttributeString('name', $this->getHandle())
                . $this->getAttributeString('type', 'radio')
                . $this->getAttributeString('id', $this->getIdAttribute() . "-$index")
                . $this->getAttributeString('class', $attributes->getClass())
                . $this->getAttributeString('value', $index)
                . $this->getParameterString('checked', $option->isChecked())
                . $attributes->getInputAttributesAsString()
                . '/>';
            $output .= $this->translate($option->getLabel());
            $output .= '</label>';
        }

        return $output;
    }

    /**
     * @return string
     */
    private function renderAsCheckboxes()
    {
        $attributes = $this->getCustomAttributes();
        $output     = '';

        foreach ($this->options as $index => $option) {
            $output .= '<label>';

            $output .= '<input '
                . $this->getAttributeString('name', $this->getHandle() . '[]')
                . $this->getAttributeString('type', 'checkbox')
                . $this->getAttributeString('id', $this->getIdAttribute() . "-$index")
                . $this->getAttributeString('class', $attributes->getClass())
                . $this->getAttributeString('value', $index)
                . $this->getParameterString('checked', $option->isChecked())
                . $attributes->getInputAttributesAsString()
                . '/>';
            $output .= $this->translate($option->getLabel());
            $output .= '</label>';
        }

        return $output;
    }
}
