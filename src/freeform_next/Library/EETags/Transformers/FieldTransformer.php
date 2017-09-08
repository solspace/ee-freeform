<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\PlaceholderInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;

class FieldTransformer
{
    /**
     * @param AbstractField $field
     * @param string        $prefix
     * @param null          $columnIndex
     * @param null          $columnCount
     *
     * @return array
     */
    public function transformField(AbstractField $field, $prefix = 'field:', $columnIndex = null, $columnCount = null)
    {
        if ($field instanceof DynamicRecipientField) {
            $value = $field->getValue();
        } else if ($field instanceof StaticValueInterface) {
            if ($field instanceof CheckboxField) {
                $value = $field->isChecked() ? $field->getStaticValue() : '';
            } else {
                $value = $field->getStaticValue();
            }
        } else {
            $value = $field->getValueAsString();
        }

        $data = [
            $prefix . 'id'                  => $field->getId(),
            $prefix . 'handle'              => $field->getHandle(),
            $prefix . 'hash'                => $field->getHash(),
            $prefix . 'type'                => $field->getType(),
            $prefix . 'label'               => $field->getLabel(),
            $prefix . 'value'               => $value,
            $prefix . 'placeholder'         => $field instanceof PlaceholderInterface ? $field->getPlaceholder() : null,
            $prefix . 'instructions'        => $field->getInstructions(),
            $prefix . 'errors'              => $field->getErrors(),
            $prefix . 'render_input'        => $field->renderInput(),
            $prefix . 'render_label'        => $field->renderLabel(),
            $prefix . 'render_instructions' => $field->renderInstructions(),
            $prefix . 'render_errors'       => $field->renderErrors(),
            $prefix . 'render'              => $field->render(),
            $prefix . 'id_attribute'        => $field->getIdAttribute(),
            $prefix . 'required'            => $field->isRequired(),
            $prefix . 'input_only'          => $field->isInputOnly(),
            $prefix . 'can_store_values'    => $field->canStoreValues(),
            $prefix . 'page_index'          => $field->getPageIndex(),
            $prefix . 'has_errors'          => $field->hasErrors(),
            $prefix . 'errors'              => $field->getErrors(),
            $prefix . 'position'            => $field instanceof SubmitField ? $field->getPosition() : '',
            $prefix . 'marker:open'         => '##FFN:' . $field->getHash() . ':FFN##',
            $prefix . 'options'             => $this->getOptions($field),
            $prefix . 'show_as_radio'       => $field instanceof DynamicRecipientField
                ? $field->isShowAsRadio() : false,
        ];

        if (null !== $columnCount && null !== $columnIndex) {
            $data['column:index']           = $columnIndex;
            $data['column:count']           = $columnCount;
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
            if ($field instanceof MultipleValueInterface) {
                $isChecked = in_array($option->getValue(), $field->getValue(), false);
            } else {
                $isChecked = $option->getValue() == $field->getValue();
            }

            $options[] = [
                'option:index'   => $index++,
                'option:label'   => $option->getLabel(),
                'option:value'   => $option->getValue(),
                'option:checked' => $isChecked,
            ];
        }

        return $options;
    }
}
