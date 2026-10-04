<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Page;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Row;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FieldTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FormTransformer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Stringy\Stringy;

class FormToTagDataTransformer
{
    public const PATTERN_FIELD_RENDER           = '/{field:([a-zA-Z0-9\-_]+):(render(?:_?[a-zA-Z]+)?)?\s+([^}]+)}/i';
    public const PATTERN_FIELD_RENDER_VARIABLES = '/\b([a-zA-Z0-9_\-:]+)=(?:\'|")([^"\']+)(?:\'|")/';

    /**
     * FormToTagDataTransformer constructor.
     *
     * @param string $content
     * @param bool   $skipHelperFields
     */
    public function __construct(private Form $form, private $content, private $skipHelperFields = false)
    {
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        if ($this->form->shouldDisplaySuccessMessage()) {
            return $this->form->renderSuccessBanner();
        }

        $output = $this->form->renderTag()
            . $this->getOutputWithoutWrappingFormTags()
            . $this->form->renderClosingTag();

        return $output;
    }

    /**
     * @return string
     */
    public function getOutputWithoutWrappingFormTags(): string|array|null
    {
        if ($this->form->shouldDisplaySuccessMessage()) {
            return $this->form->renderSuccessBanner();
        }

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
        $formTransformer = new FormTransformer();

        $data = [
            'rows'  => $this->rowData(),
            'pages' => $this->pages(),
        ];

        $submissionCount = FormRepository::getInstance()->getFormSubmissionCount([$this->form->getId()]);
        if (!empty($submissionCount)) {
            $submissionCount = reset($submissionCount);
        } else {
            $submissionCount = 0;
        }

        $spamCount = FormRepository::getInstance()->getFormSpamCount([$this->form->getId()]);
        if (!empty($spamCount)) {
            $spamCount = reset($spamCount);
        } else {
            $spamCount = 0;
        }

        $data = array_merge($data, $this->pageData($this->form->getCurrentPage(), 'current_page:'));
        $data = array_merge(
            $data,
            $formTransformer->transformForm($this->form, $submissionCount, $spamCount, $this->skipHelperFields)
        );
        $data = array_merge($data, $this->getFields());

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
    private function parseFieldTags($content): string|array|null
    {
        if (preg_match_all('/##FFN:([a-zA-Z_\-0-9]+):FFN##{field:render/', $content, $matches)) {
            [$matchedStrings, $hashes] = $matches;
            foreach ($hashes as $index => $hash) {
                $content = str_replace($matchedStrings[$index], '{field:' . $hash . ':render', $content);
            }
        }

        if (preg_match_all(self::PATTERN_FIELD_RENDER, $content, $fieldMatches)) {

            /**
             * @var array $strings
             * @var array $handles
             */
            [$strings, $handles, $actions] = $fieldMatches;

            foreach ($handles as $index => $handle) {
                $field = $this->form->get($handle);
                if (!$field) {
                    try {
                        $field = $this->form->getLayout()->getFieldByHash($handle);
                    } catch (FreeformException) {
                        continue;
                    }
                }

                $string     = $strings[$index];
                $attributes = [];

                if ($field && preg_match_all(self::PATTERN_FIELD_RENDER_VARIABLES, $string, $varMatches)) {
                    [$_, $keys, $values] = $varMatches;

                    foreach ($values as $varIndex => $value) {
                        $key    = $keys[$varIndex];
                        $subKey = null;

                        if (str_contains($key, ':')) {
                            [$key, $subKey] = explode(':', $key);
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
                $replacement = match ($action) {
                    'render_label' => $field->renderLabel(),
                    'render_instructions' => $field->renderInstructions(),
                    'render_errors' => $field->renderErrors(),
                    'render_input' => $field->renderInput(),
                    default => $field->render(),
                };

                $content = str_replace($string, $replacement, $content);
            }
        }

        $content = preg_replace('/##FFN:([a-zA-Z_\-0-9]+):FFN##/', '', $content);

        return $content;
    }

    /**
     * @return array
     */
    private function rowData(): array
    {
        $form = $this->form;

        $data = [];
        /** @var Row $row */
        foreach ($form->getCurrentPage()->getRows() as $row) {
            $columns = [
                'fields' => [],
            ];

            $columnCount = count($row);
            $columnIndex = 0;

            /** @var AbstractField $field */
            foreach ($row as $field) {
                if ($this->skipHelperFields) {
                    if ($field instanceof NoStorageInterface || $field instanceof FileUploadInterface) {
                        continue;
                    }
                }

                $column = $this->getFieldData($field, 'field:', $columnIndex++, $columnCount);

                $columns['fields'][] = $column;
            }

            if (empty($columns['fields'])) {
                continue;
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
     * @param int|null      $columnIndex
     * @param int|null      $columnCount
     * @return array
     */
    private function getFieldData(AbstractField $field, string $prefix = 'field:', ?int $columnIndex = null, ?int $columnCount = null)
    {
        static $transformer;

        if (null === $transformer) {
            $transformer = new FieldTransformer();
        }

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

        return $transformer->transformField($field, $value, $prefix, $columnIndex, $columnCount);
    }

    /**
     * @return array
     */
    private function pages(): array
    {
        $form = $this->form;

        $data = [];
        foreach ($form->getPages() as $page) {
            $data[] = $this->pageData($page);
        }

        return $data;
    }

    /**
     * @return array
     */
    private function pageData(Page $page, string $prefix = 'page:'): array
    {
        return [
            $prefix . 'label' => $page->getLabel(),
            $prefix . 'index' => $page->getIndex(),
        ];
    }
}
