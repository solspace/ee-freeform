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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;

trait MultipleValueTrait
{
    /** @var array */
    protected $values;

    /**
     * @return array
     */
    public function getValue()
    {
        $valueOverride = $this->getValueOverride();
        if ($valueOverride) {
            if (!is_array($valueOverride)) {
                return [$valueOverride];
            }

            return $valueOverride;
        }

        $values = $this->values;

        if (!\is_array($values) && !empty($values)) {
            $values = [$values];
        }

        if (empty($values)) {
            $values = [];
        } else {
            $values = array_map('strval', $values);
        }

        if ($this instanceof DynamicRecipientField && $values) {
            $areIndexes = true;
            foreach ($values as $value) {
                if (!\is_numeric($value)) {
                    $areIndexes = false;
                }
            }

            $checkedIndexes = [];
            foreach ($this->options as $index => $option) {
                if ($areIndexes && \in_array($index, $values, false)) {
                    $checkedIndexes[] = $index;
                } else if (\in_array($option->getValue(), $values, true)) {
                    $checkedIndexes[] = $index;
                }
            }

            $values = $checkedIndexes;
        }

        return $values;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        if ($this instanceof MultipleValueInterface && !\is_array($value)) {
            $value = [$value];
        }

        $this->values = $value;

        if ($this instanceof OptionsInterface) {
            $updatedOptions = [];
            foreach ($this->getOptions() as $index => $option) {
                if ($this instanceof ObscureValueInterface) {
                    $checked = \in_array($index, $value, false);
                } else {
                    $checked = \in_array($option->getValue(), $value, false);
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
