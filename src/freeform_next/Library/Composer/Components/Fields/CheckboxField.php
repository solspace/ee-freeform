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
        return $this->checked;
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
            . $this->getAttributeString("name", $this->getHandle())
            . $this->getAttributeString("type", FieldInterface::TYPE_HIDDEN)
            . $this->getAttributeString("value", "", false)
            . $attributes->getInputAttributesAsString()
            . '/>';

        $output .= '<input '
            . $this->getAttributeString("name", $this->getHandle())
            . $this->getAttributeString("type", $this->getType())
            . $this->getAttributeString("id", $this->getIdAttribute())
            . $this->getAttributeString("class", $attributes->getClass())
            . $this->getAttributeString("value", 1, false)
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . ($this->isChecked() ? 'checked ' : '')
            . '/>';

        return $output;
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
            . $this->getAttributeString("class", $attributes->getLabelClass())
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
