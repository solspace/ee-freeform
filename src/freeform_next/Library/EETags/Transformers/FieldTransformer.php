<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\PlaceholderInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\DatetimeField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\NumberField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\PhoneField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RatingField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RegexField;

class FieldTransformer
{
    /**
     * @param AbstractField $field
     * @param mixed         $value
     * @param string        $prefix
     * @param null          $columnIndex
     * @param null          $columnCount
     *
     * @return array
     */
    public function transformField(
        AbstractField $field,
        $value = null,
        $prefix = 'field:',
        $columnIndex = null,
        $columnCount = null
    ) {
        $files = [];
        if ($field instanceof FileUploadField && is_array($value)) {
            foreach ($value as $fileId) {
                $files[] = ['file_id' => $fileId];
            }
        }

        if (!is_string($value)) {
            if (is_array($value)) {
                if ($field instanceof FileUploadField) {
                    $value = implode('|', $value);
                } else {
                    $value = implode(', ', $value);
                }
            }

            $value = (string) $value;
        }

        $data = [
            $prefix . 'id'                   => $field->getId(),
            $prefix . 'handle'               => $field->getHandle(),
            $prefix . 'hash'                 => $field->getHash(),
            $prefix . 'type'                 => $field->getType(),
            $prefix . 'label'                => $field->getLabel(),
            $prefix . 'value'                => $value,
            $prefix . 'option_value'         => $this->getOptionValues($field),
            $prefix . 'placeholder'          => $field instanceof PlaceholderInterface ? $field->getPlaceholder(
            ) : null,
            $prefix . 'instructions'         => $field->getInstructions(),
            $prefix . 'errors'               => $field->getErrors(),
            $prefix . 'render_input'         => $field->renderInput(),
            $prefix . 'render_label'         => $field->renderLabel(),
            $prefix . 'render_instructions'  => $field->renderInstructions(),
            $prefix . 'render_errors'        => $field->renderErrors(),
            $prefix . 'render'               => $field->render(),
            $prefix . 'id_attribute'         => $field->getIdAttribute(),
            $prefix . 'required'             => $field->isRequired(),
            $prefix . 'input_only'           => $field->isInputOnly(),
            $prefix . 'can_store_values'     => $field->canStoreValues(),
            $prefix . 'page_index'           => $field->getPageIndex(),
            $prefix . 'has_errors'           => $field->hasErrors(),
            $prefix . 'errors'               => $field->getErrors(),
            $prefix . 'position'             => $field instanceof SubmitField ? $field->getPosition() : '',
            $prefix . 'marker:open'          => '##FFN:' . $field->getHash() . ':FFN##',
            $prefix . 'options'              => $this->getOptions($field),
            $prefix . 'files'                => $files,
            $prefix . 'show_as_radio'        => $field instanceof DynamicRecipientField
                ? $field->isShowAsRadio() : false,
            $prefix . 'checked'              => $field instanceof CheckboxField ? $field->isChecked() : false,
            $prefix . 'date_time_type'       => $field instanceof DatetimeField ? $field->getDateTimeType() : null,
            $prefix . 'generate_placeholder' => $field instanceof DatetimeField ? $field->isGeneratePlaceholder(
            ) : null,
            $prefix . 'date_order'           => $field instanceof DatetimeField ? $field->getDateOrder() : null,
            $prefix . 'date_4_digit_year'    => $field instanceof DatetimeField ? $field->isDate4DigitYear() : null,
            $prefix . 'date_leading_zero'    => $field instanceof DatetimeField ? $field->isDateLeadingZero() : null,
            $prefix . 'date_separator'       => $field instanceof DatetimeField ? $field->getDateSeparator() : null,
            $prefix . 'clock_24h'            => $field instanceof DatetimeField ? $field->isClock24h() : null,
            $prefix . 'lowercase_ampm'       => $field instanceof DatetimeField ? $field->isLowercaseAMPM() : null,
            $prefix . 'use_datepicker'       => $field instanceof DatetimeField ? $field->isUseDatepicker() : null,
            $prefix . 'clock_separator'      => $field instanceof DatetimeField ? $field->getClockSeparator() : null,
            $prefix . 'clock_am_pm_separate' => $field instanceof DatetimeField ? $field->isClockAMPMSeparate() : null,
            $prefix . 'pattern'              => $field instanceof RegexField || $field instanceof PhoneField ? $field->getPattern(
            ) : null,
            $prefix . 'min_length'           => $field instanceof NumberField ? $field->getMinLength() : null,
            $prefix . 'max_length'           => $field instanceof NumberField ? $field->getMaxLength() : null,
            $prefix . 'min_value'            => $field instanceof NumberField ? $field->getMinValue() : null,
            $prefix . 'max_value'            => $field instanceof NumberField || $field instanceof RatingField ? $field->getMaxValue(
            ) : null,
            $prefix . 'decimal_count'        => $field instanceof NumberField ? $field->getDecimalCount() : null,
            $prefix . 'decimal_separator'    => $field instanceof NumberField ? $field->getDecimalSeparator() : null,
            $prefix . 'thousands_separator'  => $field instanceof NumberField ? $field->getThousandsSeparator() : null,
            $prefix . 'allow_negative'       => $field instanceof NumberField ? $field->isAllowNegative() : null,
            $prefix . 'color_idle'           => $field instanceof RatingField ? $field->getColorIdle() : null,
            $prefix . 'color_hover'          => $field instanceof RatingField ? $field->getColorHover() : null,
            $prefix . 'color_selected'       => $field instanceof RatingField ? $field->getColorSelected() : null,
            $prefix . 'label_next'           => $field instanceof SubmitField ? $field->getLabelNext() : null,
            $prefix . 'label_prev'           => $field instanceof SubmitField ? $field->getLabelPrev() : null,
            $prefix . 'disable_prev'         => $field instanceof SubmitField ? $field->isDisablePrev() : null,
        ];

        if (null !== $columnCount && null !== $columnIndex) {
            $data['column:index']      = $columnIndex;
            $data['column:count']      = $columnCount;
            $data['column:grid_width'] = 12 / ($columnCount ?: 1);
        }

        return $data;
    }

    /**
     * @param AbstractField $field
     *
     * @return array|null
     */
    private function getOptions(AbstractField $field)
    {
        if (!$field instanceof OptionsInterface) {
            return null;
        }

        $index   = 0;
        $options = [];
        foreach ($field->getOptions() as $option) {
            $options[] = [
                'option:index'   => $index++,
                'option:label'   => lang($option->getLabel()),
                'option:value'   => $option->getValue(),
                'option:checked' => $option->isChecked(),
            ];
        }

        return $options;
    }

    /**
     * @param AbstractField $field
     *
     * @return string|null
     */
    private function getOptionValues(AbstractField $field)
    {
        if (!$field instanceof OptionsInterface) {
            return null;
        }

        $values = [];
        foreach ($field->getOptions() as $option) {
            if ($option->isChecked()) {
                $values[] = $option->getValue();
            }
        }

        return $values ? implode(', ', $values) : '';
    }
}
