<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFieldAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Page;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Row;
use Stringy\Stringy;

class FormToTagDataTransformer
{
    const PATTERN_FIELD_RENDER = '/{field:([a-zA-Z0-9\-_]+):render([^}]+)}/i';
    const PATTERN_FIELD_RENDER_VARIABLES = '/\b([a-zA-Z0-9_\-:]+)=(?:\'|")([^"\']+)(?:\'|")/';

    /** @var Form */
    private $form;

    /**
     * FormToTagDataTransformer constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return array
     */
    public function transform()
    {
        $form = $this->form;

        $data = [
            'form:id'          => $form->getId(),
            'form:name'        => $form->getName(),
            'form:handle'      => $form->getHandle(),
            'form:description' => $form->getDescription(),
            'form:return_url'  => $form->getReturnUrl(),
            'form:action'      => $form->getCustomAttributes()->getAction(),
            'form:method'      => $form->getCustomAttributes()->getMethod(),
            'form:class'       => $form->getCustomAttributes()->getClass(),
            'rows'             => $this->rowData(),
            'pages'            => $this->pages(),
        ];

        $data = array_merge($data, $this->getFields());
        $data = array_merge($data, $this->pageData($form->getCurrentPage(), 'current_page:'));

        return $data;
    }

    public function parseFieldTags($content)
    {
        if (preg_match_all(self::PATTERN_FIELD_RENDER, $content, $fieldMatches)) {

            /**
             * @var array $strings
             * @var array $handles
             */
            list ($strings, $handles) = $fieldMatches;

            foreach ($handles as $index => $handle) {
                $field = $this->form->get($handle);
                $string = $strings[$index];
                $attributes = [];

                if ($field && preg_match_all(self::PATTERN_FIELD_RENDER_VARIABLES, $string, $varMatches)) {
                    list ($_, $keys, $values) = $varMatches;

                    foreach ($values as $varIndex => $value) {
                        $key = $keys[$varIndex];
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

                $content = str_replace($string, $field->render(), $content);
            }
        }

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

            /** @var AbstractField $field */
            foreach ($row as $field) {
                $column = $this->getFieldData($field);

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
     *
     * @return array
     */
    private function getFieldData(AbstractField $field, $prefix = 'field:')
    {
        return [
            $prefix . 'id'                  => $field->getId(),
            $prefix . 'handle'              => $field->getHandle(),
            $prefix . 'label'               => $field->getLabel(),
            $prefix . 'instructions'        => $field->getInstructions(),
            $prefix . 'errors'              => $field->getErrors(),
            $prefix . 'render_input'        => $field->renderInput(),
            $prefix . 'render_label'        => $field->renderLabel(),
            $prefix . 'render_instructions' => $field->renderInstructions(),
            $prefix . 'render_errors'       => $field->renderErrors(),
            $prefix . 'render'              => $field->render(),
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
}
