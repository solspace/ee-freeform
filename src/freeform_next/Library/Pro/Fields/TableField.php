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

    /** @var string */
    protected $addButtonLabel = 'Add';

    /** @var string */
    protected $addButtonMarkup;

    /** @var string */
    protected $removeButtonLabel = 'Remove';

    /** @var string */
    protected $removeButtonMarkup;

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
     * @return string
     */
    public function getAddButtonLabel()
    {
        $attributes = $this->getCustomAttributes();

        return $attributes->getAddButtonLabel() !== null ? $attributes->getAddButtonLabel() : $this->addButtonLabel;
    }

    /**
     * @param string $addButtonLabel
     *
     * @return TableField
     */
    public function setAddButtonLabel($addButtonLabel)
    {
        $this->addButtonLabel = $addButtonLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddButtonMarkup()
    {
        return $this->addButtonMarkup;
    }

    /**
     * @param string $addButtonMarkup
     *
     * @return TableField
     */
    public function setAddButtonMarkup($addButtonMarkup)
    {
        $this->addButtonMarkup = $addButtonMarkup;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveButtonLabel()
    {
        $attributes = $this->getCustomAttributes();

        return $attributes->getRemoveButtonLabel() !== null ? $attributes->getRemoveButtonLabel() : $this->removeButtonLabel;
    }

    /**
     * @param string $removeButtonLabel
     *
     * @return TableField
     */
    public function setRemoveButtonLabel($removeButtonLabel)
    {
        $this->removeButtonLabel = $removeButtonLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveButtonMarkup()
    {
        return $this->removeButtonMarkup;
    }

    /**
     * @param string $removeButtonMarkup
     *
     * @return TableField
     */
    public function setRemoveButtonMarkup($removeButtonMarkup)
    {
        $this->removeButtonMarkup = $removeButtonMarkup;

        return $this;
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
            $rowValues      = [];
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
                $type = isset($column['type']) ? $column['type'] : self::COLUMN_TYPE_STRING;
                if ($type === self::COLUMN_TYPE_CHECKBOX) {
                    $values[] = null;
                } else {
                    $values[] = isset($column['value']) ? $column['value'] : null;
                }
            }

            $values = [$values];
        }

        $id     = $this->getIdAttribute();
        $output = '<table class="form-table ' . $classString . '" id="' . $id . '">';

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
                        $value  = $row[$index];
                        $output .= '<input type="checkbox"'
                            . " name=\"{$handle}[$rowIndex][$index]\""
                            . ' class="' . $attributes->getTableCheckboxInputClass() . '"'
                            . " value=\"$defaultValue\""
                            . " data-default-value=\"$defaultValue\""
                            . ($value ? ' checked' : '')
                            . ' />';
                        break;

                    case self::COLUMN_TYPE_SELECT:
                        $options = explode(';', $defaultValue);
                        $output  .= '<select'
                            . " name=\"{$handle}[$rowIndex][$index]\""
                            . 'class="' . $attributes->getTableSelectInputClass() . '"'
                            . '>';
                        foreach ($options as $option) {
                            $selected = $option === $value ? ' selected' : '';
                            $output   .= "<option value=\"$option\"$selected>$option</option>";
                        }
                        $output .= '</select>';

                        break;

                    case self::COLUMN_TYPE_STRING:
                    default:
                        $output .= '<input'
                            . ' type="text"'
                            . ' class="' . $attributes->getTableTextInputClass() . '"'
                            . " name=\"{$handle}[$rowIndex][$index]\""
                            . " value=\"$value\""
                            . " data-default-value=\"$defaultValue\""
                            . ' />';

                        break;
                }

                $output .= '</td>';
            }

            $output .= '<td>';
            if ($this->getRemoveButtonMarkup()) {
                $output .= $this->getRemoveButtonMarkup();
            } else {
                $output .= '<button class="form-table-remove-row ' . $attributes->getRemoveButtonClass() . '" type="button">' . $this->getRemoveButtonLabel() . '</button>';
            }
            $output .= '</td>';

            $output .= '</tr>';
        }
        $output .= '</tbody>';

        $output .= '</table>';
        if ($this->getAddButtonMarkup()) {
            $output .= $this->getAddButtonMarkup();
        } else {
            $output .= '<button class="form-table-add-row ' . $attributes->getAddButtonClass() . '" data-target="' . $id . '" type="button">' . $this->getAddButtonLabel() . '</button>';
        }

        return $output;
    }
}
