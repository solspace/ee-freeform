<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFieldAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Page;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Row;
use Stringy\Stringy;

class FormToTagDataTransformer
{
    const PATTERN_FIELD_RENDER           = '/{field:([a-zA-Z0-9\-_]+):(render(?:_?[a-zA-Z]+)?)?\s+([^}]+)}/i';
    const PATTERN_FIELD_RENDER_VARIABLES = '/\b([a-zA-Z0-9_\-:]+)=(?:\'|")([^"\']+)(?:\'|")/';

    /** @var Form */
    private $form;

    /** @var string */
    private $content;

    /**
     * FormToTagDataTransformer constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form, $content)
    {
        $this->form    = $form;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $output = $this->form->renderTag() . $this->getOutputWithoutWrappingFormTags() . $this->form->renderClosingTag(
            );

        return $output;
    }

    /**
     * @return string
     */
    public function getOutputWithoutWrappingFormTags()
    {
        $output = $this->content;

        $output = $this->markFieldTags($output);
        $output = ee()->TMPL->parse_variables($output, [$this->transform()]);
        $output = $this->parseFieldTags($output);

        return $output;
    }

    /**
     * @return array
     */
    private function transform()
    {
        $form = $this->form;

        $data = [
            'form:id'           => $form->getId(),
            'form:name'         => $form->getName(),
            'form:handle'       => $form->getHandle(),
            'form:description'  => $form->getDescription(),
            'form:return_url'   => $form->getReturnUrl(),
            'form:action'       => $form->getCustomAttributes()->getAction(),
            'form:method'       => $form->getCustomAttributes()->getMethod(),
            'form:class'        => $form->getCustomAttributes()->getClass(),
            'form:page_count'   => count($form->getPages()),
            'form:has_errors'   => $form->hasErrors(),
            'form:row_class'    => $form->getCustomAttributes()->getRowClass(),
            'form:column_class' => $form->getCustomAttributes()->getColumnClass(),
            'rows'              => $this->rowData(),
            'pages'             => $this->pages(),
        ];

        $data = array_merge($data, $this->getFields());
        $data = array_merge($data, $this->pageData($form->getCurrentPage(), 'current_page:'));

        return $data;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function markFieldTags($content)
    {
        if (!preg_match_all('/\{field:render(?:_[a-zA-Z]+)?\s+[^}]*\}/i', $content, $matches)) {
            return $content;
        }

        foreach ($matches[0] as $match) {
            $content = str_replace($match, '{field:marker:open}' . $match, $content);
        }

        return $content;
    }

    /**
     * @param $content
     *
     * @return string
     */
    private function parseFieldTags($content)
    {
        if (preg_match_all('/##FFN:([a-zA-Z_\-0-9]+):FFN##{field:render/', $content, $matches)) {
            list ($matchedStrings, $hashes) = $matches;
            foreach ($hashes as $index => $hash) {
                $content = str_replace($matchedStrings[$index], '{field:' . $hash . ':render', $content);
            }
        }

        if (preg_match_all(self::PATTERN_FIELD_RENDER, $content, $fieldMatches)) {

            /**
             * @var array $strings
             * @var array $handles
             */
            list ($strings, $handles, $actions) = $fieldMatches;

            foreach ($handles as $index => $handle) {
                $field = $this->form->get($handle);
                if (!$field) {
                    $field = $this->form->getLayout()->getFieldByHash($handle);
                }

                $string     = $strings[$index];
                $attributes = [];

                if ($field && preg_match_all(self::PATTERN_FIELD_RENDER_VARIABLES, $string, $varMatches)) {
                    list ($_, $keys, $values) = $varMatches;

                    foreach ($values as $varIndex => $value) {
                        $key    = $keys[$varIndex];
                        $subKey = null;

                        if (strpos($key, ':') !== false) {
                            list($key, $subKey) = explode(':', $key);
                        }

                        $key = (string) Stringy::create($key)->camelize();

                        if (null !== $subKey) {
                            if (!isset($attributes[$key])) {
                                $attributes[$key] = [];
                            }
                            $attributes[$key][$subKey] = $value;
                        } else {
                            $attributes[$key] = $value;
                        }
                    }
                }

                $field->setAttributes($attributes);

                $action = $actions[$index];
                switch ($action) {
                    case 'render_label':
                        $replacement = $field->renderLabel();
                        break;

                    case 'render_instructions':
                        $replacement = $field->renderInstructions();
                        break;

                    case 'render_errors':
                        $replacement = $field->renderErrors();
                        break;

                    case 'render_input':
                        $replacement = $field->renderInput();
                        break;

                    case 'render':
                    default:
                        $replacement = $field->render();
                        break;
                }

                $content = str_replace($string, $replacement, $content);
            }
        }

        $content = preg_replace('/##FFN:([a-zA-Z_\-0-9]+):FFN##/', '', $content);

        return $content;
    }

    /**
     * @return array
     */
    private function rowData()
    {
        $form = $this->form;

        $data = [];
        /** @var Row $row */
        foreach ($form as $row) {
            $columns = [
                'columns' => [],
            ];

            $columnCount = count($row);
            $columnIndex = 0;

            /** @var AbstractField $field */
            foreach ($row as $field) {
                $column = $this->getFieldData($field, 'field:', $columnIndex++, $columnCount);

                $columns['columns'][] = $column;
            }

            $data[] = $columns;
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getFields()
    {
        $form = $this->form;

        $data = [];
        foreach ($form->getLayout()->getFields() as $field) {
            $fieldData = $this->getFieldData($field, 'field:' . $field->getHandle() . ':');

            $data = array_merge($data, $fieldData);
        }

        return $data;
    }

    /**
     * @param AbstractField $field
     * @param string        $prefix
     * @param int|null      $columnIndex
     * @param int|null      $columnCount
     *
     * @return array
     */
    private function getFieldData(AbstractField $field, $prefix = 'field:', $columnIndex = null, $columnCount = null)
    {
        return [
            $prefix . 'id'                  => $field->getId(),
            $prefix . 'handle'              => $field->getHandle(),
            $prefix . 'hash'                => $field->getHash(),
            $prefix . 'type'                => $field->getType(),
            $prefix . 'label'               => $field->getLabel(),
            $prefix . 'value'               => $field->getValueAsString(),
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
            $prefix . 'page_index'          => $field->getPageIndex(),
            $prefix . 'has_errors'          => $field->hasErrors(),
            $prefix . 'errors'              => $field->getErrors(),
            $prefix . 'position'            => $field instanceof SubmitField ? $field->getPosition() : '',
            $prefix . 'marker:open'         => '##FFN:' . $field->getHash() . ':FFN##',
            $prefix . 'options'             => $this->getOptions($field),
            $prefix . 'show_as_radio'       => $field instanceof DynamicRecipientField ? $field->isShowAsRadio(
            ) : false,
            'column:index'                  => $columnIndex,
            'column:count'                  => $columnCount,
            'column:grid_width'             => 12 / ($columnCount ?: 1),
        ];
    }

    /**
     * @return array
     */
    private function pages()
    {
        $form = $this->form;

        $data = [];
        foreach ($form->getPages() as $page) {
            $data[] = $this->pageData($page);
        }

        return $data;
    }

    /**
     * @param Page   $page
     * @param string $prefix
     *
     * @return array
     */
    private function pageData(Page $page, $prefix = 'page:')
    {
        return [
            $prefix . 'label' => $page->getLabel(),
            $prefix . 'index' => $page->getIndex(),
        ];
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

        $options = [];
        foreach ($field->getOptions() as $option) {
            if ($field instanceof MultipleValueInterface) {
                $isChecked = in_array($option->getValue(), $field->getValue(), false);
            } else {
                $isChecked = $option->getValue() == $field->getValue();
            }

            $options[] = [
                'option:label'   => $option->getLabel(),
                'option:value'   => $option->getValue(),
                'option:checked' => $isChecked,
            ];
        }

        return $options;
    }
}
