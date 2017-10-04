<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FieldsService
{
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
            FieldInterface::TYPE_CHECKBOX           => 'Checkbox',
            FieldInterface::TYPE_CHECKBOX_GROUP     => 'Checkbox Group',
            FieldInterface::TYPE_RADIO_GROUP        => 'Radio Group',
            FieldInterface::TYPE_FILE               => 'File',
            FieldInterface::TYPE_DYNAMIC_RECIPIENTS => 'Dynamic Recipients',
        ];

        $finder = new Finder();
        $path   = __DIR__ . '/../Library/Pro/Fields';
        $interface = 'Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface';
        $baseNamespace = 'Solspace\Addons\FreeformNext\Library\Pro\Fields';

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
                if ($reflectionClass->implementsInterface($interface)) {
                    $name = $className::getFieldTypeName();
                    $type = $className::getFieldType();

                    $fieldTypes[$type] = $name;
                }
            }
        }

        return $fieldTypes;
    }
}
