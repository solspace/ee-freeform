<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultiDimensionalValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;

class TableField extends AbstractField implements MultipleValueInterface, MultiDimensionalValueInterface
{
    use MultipleValueTrait;

    const COLUMN_TYPE_STRING   = 'string';
    const COLUMN_TYPE_SELECT   = 'select';
    const COLUMN_TYPE_CHECKBOX = 'checkbox';

    /** @var array */
    protected $layout;

    /** @var bool */
    protected $useScript;

    /** @var int */
    protected $maxRows;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_TABLE;
    }

    /**
     * @return array
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return bool
     */
    public function isUseScript()
    {
        return $this->useScript;
    }

    /**
     * @return int
     */
    public function getMaxRows()
    {
        return $this->maxRows;
    }

    /**
     * @param mixed $value
     *
     * @return $this|AbstractField
     */
    public function setValue($value)
    {
        $layout = $this->getLayout();

        $this->values = $values = [];
        if (!is_array($value)) {
            return $this;
        }

        foreach ($value as $rowIndex => $row) {
            if (!is_array($row)) {
                continue;
            }

            $hasSingleValue = false;
            $rowValues = [];
            foreach ($layout as $index => $column) {
                $value = isset($row[$index]) ? $row[$index] : '';
                if ($value) {
                    $hasSingleValue = true;
                }

                $rowValues[$index] = $value;
            }

            if (!$hasSingleValue) {
                continue;
            }

            $values[] = $rowValues;
        }

        $this->values = $values;

        return $this;
    }

    /**
     * @return string
     */
    protected function getInputHtml()
    {
        $layout = $this->getLayout();
        if (!$layout || !is_array($layout)) {
            return '';
        }

        $attributes  = $this->getCustomAttributes();
        $classString = $attributes->getClass() . ' ' . $this->getInputClassString();

        $handle = $this->getHandle();
        $values = $this->getValue();
        if (empty($values)) {
            $values = [];
            foreach ($layout as $column) {
                $values[] = isset($column['value']) ? $column['value'] : null;
            }

            $values = [$values];
        }

        $id = $this->getIdAttribute();
        $output      = '<table class="form-table ' . $classString . '" id="' . $id . '">';

        $output .= '<thead>';
        $output .= '<tr>';
        foreach ($layout as $column) {
            $label = isset($column['label']) ? $column['label'] : '';

            $output .= '<th>' . htmlentities($label) . '</th>';
        }
        $output .= '<th>&nbsp;</th></tr>';
        $output .= '</thead>';

        $output .= '<tbody>';
        foreach ($values as $rowIndex => $row) {
            $output .= '<tr>';

            foreach ($layout as $index => $column) {
                $type         = isset($column['type']) ? $column['type'] : self::COLUMN_TYPE_STRING;
                $defaultValue = isset($column['value']) ? $column['value'] : '';
                $value        = $row[$index] !== null ? $row[$index] : $defaultValue;
                $value        = htmlentities($value);

                $output .= '<td>';

                switch ($type) {
                    case self::COLUMN_TYPE_CHECKBOX:
                        $output .= "<input type=\"checkbox\" name=\"{$handle}[$rowIndex][$index]\" value=\"$defaultValue\" data-default-value=\"$defaultValue\" " . ($value ? 'checked' : '') . " />";
                        break;

                    case self::COLUMN_TYPE_SELECT:
                        $options = explode(';', $defaultValue);
                        $output .= "<select name=\"{$handle}[$rowIndex][$index]\">";
                        foreach ($options as $option) {
                            $selected = $option === $value ? ' selected' : '';
                            $output .= "<option value=\"$option\"$selected>$option</option>";
                        }
                        $output .= '</select>';

                        break;

                    case self::COLUMN_TYPE_STRING:
                    default:
                        $output .= "<input type=\"text\" name=\"{$handle}[$rowIndex][$index]\" value=\"$value\" data-default-value=\"$defaultValue\" />";

                        break;
                }

                $output .= '</td>';
            }

            $output .= '<td>';
            $output .= '<button class="form-table-remove-row" type="button">Remove</button>';
            $output .= '</td>';

            $output .= '</tr>';
        }
        $output .= '</tbody>';

        $output .= '</table>';
        $output .= '<button class="form-table-add-row" data-target="' . $id . '" type="button">Add</button>';

        return $output;
    }
}
