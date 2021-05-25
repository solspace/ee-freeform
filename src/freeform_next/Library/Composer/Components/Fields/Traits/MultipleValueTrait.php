<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultiDimensionalValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;

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
        } else if (!$this instanceof MultiDimensionalValueInterface) {
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
            if (null === $value) {
                $value = [];
            } else {
                $value = [$value];
            }
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
