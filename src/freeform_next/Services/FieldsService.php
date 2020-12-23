<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ExternalOptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Configuration\ExternalOptionsConfiguration;
use Solspace\Addons\FreeformNext\Library\Database\FieldHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Factories\PredefinedOptionsFactory;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FieldsService implements FieldHandlerInterface
{
    /** @var array */
    private static $optionsCache = array();

    /**
     * @return array
     */
    public function getFieldTypes()
    {
        $fieldTypes = [
            FieldInterface::TYPE_TEXT               => 'Text',
            FieldInterface::TYPE_TEXTAREA           => 'Textarea',
            FieldInterface::TYPE_EMAIL              => 'Email',
            FieldInterface::TYPE_HIDDEN             => 'Hidden',
            FieldInterface::TYPE_SELECT             => 'Select',
            FieldInterface::TYPE_MULTIPLE_SELECT    => 'Multiple Select',
            FieldInterface::TYPE_CHECKBOX           => 'Checkbox',
            FieldInterface::TYPE_CHECKBOX_GROUP     => 'Checkbox Group',
            FieldInterface::TYPE_RADIO_GROUP        => 'Radio Group',
            FieldInterface::TYPE_FILE               => 'File',
            FieldInterface::TYPE_DYNAMIC_RECIPIENTS => 'Dynamic Recipients',
        ];

        $finder             = new Finder();
        $path               = __DIR__ . '/../Library/Pro/Fields';
        $interface          = 'Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface';
        $noStorageInterface = 'Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface';
        $baseNamespace      = 'Solspace\Addons\FreeformNext\Library\Pro\Fields';

        if (file_exists($path) && is_dir($path)) {
            /** @var SplFileInfo[] $files */
            $files = $finder->files()->in($path)->name('*.php');
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $baseName = substr(
                    $fileName,
                    0,
                    strpos($fileName, '.')
                );

                /** @var AbstractField $className */
                $className = $baseNamespace . "\\" . $baseName;

                $reflectionClass = new \ReflectionClass($className);
                if ($reflectionClass->implementsInterface($interface) && !$reflectionClass->implementsInterface($noStorageInterface)) {
                    $name = $className::getFieldTypeName();
                    $type = $className::getFieldType();

                    $fieldTypes[$type] = $name;
                }
            }
        }

        return $fieldTypes;
    }

    /**
     * @param FieldModel $model
     *
     * @throws \Exception
     */
    public function deleteFieldFromForms(FieldModel $model)
    {
        $forms = FormRepository::getInstance()->getAllForms();

        foreach ($forms as $form) {
            try {
                $composer = $form->getComposer();
                $composer->removeFieldById($model->id);
                $form->layoutJson = $composer->getComposerStateJSON();
                $form->save();
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @param AbstractField $field
     * @param Form          $form
     */
    public function beforeValidate(AbstractField $field, Form $form)
    {
        ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_BEFORE_VALIDATE, $field, $form);
    }

    /**
     * @param AbstractField $field
     * @param Form          $form
     */
    public function afterValidate(AbstractField $field, Form $form)
    {
        ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_AFTER_VALIDATE, $field, $form);
    }

    /**
     * @inheritDoc
     */
    public function getOptionsFromSource($source, $target, array $configuration = [], $selectedValues = [])
    {
        $cacheHash = sha1($source . $target . serialize($configuration) . serialize($selectedValues));

        if (array_key_exists($cacheHash, self::$optionsCache)) {
            return self::$optionsCache[$cacheHash];
        }

        $config     = new ExternalOptionsConfiguration($configuration);
        $labelField = $config->getLabelField();
        $valueField = $config->getValueField();
        $options    = [];

        if (!\is_array($selectedValues)) {
            $selectedValues = [$selectedValues];
        }

        switch ($source) {
            case ExternalOptionsInterface::SOURCE_ENTRIES:
                $labelField = $labelField ?: 'title';
                $valueField = $valueField ?: 'entry_id';

                $builder = ee('Model')
                    ->get('ChannelEntry')
                    ->filter('site_id', ee()->config->item('site_id'));

                if ($target) {
                    $builder->filter('channel_id', $target);
                }

                $items = $builder->all();

                $labelField = $this->getCustomChannelFieldId($labelField);
                $valueField = $this->getCustomChannelFieldId($valueField);

                foreach ($items as $item) {
                    $label     = $item->$labelField ?: $item->channel_title;
                    $value     = $item->$valueField ?: $item->channel_id;
                    $options[] = new Option($label, $value, \in_array($value, $selectedValues, false));
                }

                break;

            case ExternalOptionsInterface::SOURCE_CATEGORIES:
                $labelField = $labelField ?: 'cat_name';
                $valueField = $valueField ?: 'cat_id';

                $builder = ee('Model')
                    ->get('Category')
                    ->filter('site_id', ee()->config->item('site_id'));

                if ($target) {
                    $builder->filter('group_id', $target);
                }

                $items = $builder->all();

                $labelField = $this->getCustomCategoryFieldId($labelField);
                $valueField = $this->getCustomCategoryFieldId($valueField);

                foreach ($items as $item) {
                    $label     = $item->$labelField ?: $item->cat_name;
                    $value     = $item->$valueField ?: $item->cat_id;
                    $options[] = new Option($label, $value, \in_array($value, $selectedValues, false));
                }

                break;

            case ExternalOptionsInterface::SOURCE_MEMBERS:
                $labelField = $labelField ?: 'username';
                $valueField = $valueField ?: 'member_id';

                $builder = ee('Model')->get('Member');

                if ($target) {
                    $builder->filter('role_id', $target);
                }

                $items = $builder->all();

                $labelField = $this->getCustomMemberFieldId($labelField);
                $valueField = $this->getCustomMemberFieldId($valueField);

                foreach ($items as $item) {
                    $label     = $item->$labelField ?: $item->username;
                    $value     = $item->$valueField ?: $item->member_id;
                    $options[] = new Option($label, $value, \in_array($value, $selectedValues, false));
                }

                break;

            case ExternalOptionsInterface::SOURCE_PREDEFINED:
                return PredefinedOptionsFactory::create($target, $config, $selectedValues);
        }

        if ($config->getEmptyOption()) {
            array_unshift(
                $options,
                new Option($config->getEmptyOption(), '', \in_array('', $selectedValues, false))
            );
        }

        self::$optionsCache[$cacheHash] = $options;

        return $options;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getCustomMemberFieldId($value)
    {
        $result = ee()->db
            ->select('m_field_id')
            ->from('member_fields')
            ->where('m_field_name', $value)
            ->limit(1)
            ->get()
            ->row();

        if ($result) {
            return 'm_field_id_' . $result->m_field_id;
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getCustomChannelFieldId($value)
    {
        $result = ee()->db
            ->select('field_id')
            ->from('channel_fields')
            ->where('field_name', $value)
            ->limit(1)
            ->get()
            ->row();

        if ($result) {
            return 'field_id_' . $result->field_id;
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getCustomCategoryFieldId($value)
    {
        $result = ee()->db
            ->select('field_id')
            ->from('category_fields')
            ->where('field_name', $value)
            ->limit(1)
            ->get()
            ->row();

        if ($result) {
            return 'field_id_' . $result->field_id;
        }

        return $value;
    }
}
