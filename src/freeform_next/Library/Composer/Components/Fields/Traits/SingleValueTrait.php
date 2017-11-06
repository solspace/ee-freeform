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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;

trait SingleValueTrait
{
    /** @var string */
    protected $value;

    /**
     * @return string
     */
    public function getValue()
    {
        if ($this->getValueOverride()) {
            return $this->getValueOverride();
        }

        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        if ($this instanceof OptionsInterface) {
            $updatedOptions = [];

            if ($this instanceof ObscureValueInterface) {
                $objectValue = $this->getActualValue($this->getValue());
            } else {
                $objectValue = $this->getValue();
            }

            foreach ($this->getOptions() as $option) {
                if (is_numeric($option->getValue()) && is_numeric($objectValue)) {
                    $checked = (int) $option->getValue() === (int) $objectValue;
                } else {
                    $checked = $option->getValue() === $objectValue;
                }

                $updatedOptions[] = new Option(
                    $option->getLabel(),
                    $option->getValue(),
                    $checked
                );
            }

            $this->options = $updatedOptions;
        }

        return $this;
    }
}
