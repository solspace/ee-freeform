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
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\SingleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\StaticValueTrait;

class CheckboxField extends AbstractField implements SingleValueInterface, InputOnlyInterface, StaticValueInterface
{
    use SingleValueTrait;
    use StaticValueTrait;

    /** @var bool */
    protected $checked;

    /** @var bool */
    protected $checkedByPost;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_CHECKBOX;
    }

    /**
     * @return boolean
     */
    public function isChecked()
    {
        if (null !== $this->checkedByPost) {
            return $this->checkedByPost;
        }

        return $this->checked;
    }

    /**
     * @param bool $isChecked
     *
     * @return $this
     */
    public function setIsChecked($isChecked)
    {
        $this->checked = (bool) $isChecked;

        return $this;
    }

    /**
     * @param bool $isChecked
     *
     * @return $this
     */
    public function setIsCheckedByPost($isChecked)
    {
        $this->checkedByPost = (bool) $isChecked;

        return $this;
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

        $output .= '<input '
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', FieldInterface::TYPE_HIDDEN)
            . $this->getAttributeString('value', '', false)
            . $attributes->getInputAttributesAsString()
            . '/>';

        $output .= '<input '
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', $this->getType())
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('class', $attributes->getClass())
            . $this->getAttributeString('value', 1, false)
            . $this->getParameterString('checked', $this->isChecked())
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '/>';

        return $output;
    }

    /**
     * @param bool $optionsAsValues
     *
     * @return string
     */
    public function getValueAsString($optionsAsValues = true)
    {
        if ($optionsAsValues) {
            $value = (int) $this->getValue() === 1 ? $this->getStaticValue() : $this->getValue();

            return (string) $value;
        }

        return (string) $this->getValue();
    }

    /**
     * Output something before an input HTML is output
     *
     * @return string
     */
    protected function onBeforeInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        return '<label'
            . $this->getAttributeString('class', $attributes->getLabelClass())
            . '>';
    }

    /**
     * Output something after an input HTML is output
     *
     * @return string
     */
    protected function onAfterInputHtml()
    {
        $output = $this->getLabel();
        $output .= '</label>';

        return $output;
    }
}
