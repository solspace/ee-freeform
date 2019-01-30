<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Library\Logging\EELogger;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class NextFieldHelper
{
    const STRICT_MODE = true;

    /** @var array */
    public $errors;

    private $restrictedHandleCounter = 0;

    /** @var ClassicFieldHelper */
    private $classicFieldHelper;

    public function deleteAllFields()
    {
        $fields = FieldRepository::getInstance()->getAllFields();

        foreach ($fields as $field) {
            $field->delete();
        }

        return true;
    }

    public function saveField($classicField)
    {
        $this->classicFieldHelper = $this->getClassicFieldHelper();
        $data = $this->convertData($classicField);

        if (!$data) {
            return false;
        }

        $field = FieldRepository::getInstance()->getOrCreateField(null);
        $isNew = !$field->id;

        $post        = $data;
        $type        = isset($data['type']) ? $data['type'] : $field->type;
        $validValues = $additionalProperties = [];
        foreach ($post as $key => $value) {
            if (property_exists($field, $key)) {
                $validValues[$key] = $value;
            }
        }

        $booleanValues = [
            'required',
            'checked',
            'generatePlaceholder',
            'date4DigitYear',
            'dateLeadingZero',
            'clock24h',
            'lowercaseAMPM',
            'clockAMPMSeparate',
            'allowNegative',
        ];

        $integerValues = [
            'minValue',
            'maxValue',
            'minLength',
            'maxLength',
            'decimalCount',
        ];

        foreach ($validValues as $key => $value) {
            if (in_array($key, $booleanValues, true)) {
                $validValues[$key] = $value === 'y';
            }
        }

        $fieldHasOptions = in_array(
            $type,
            [
                FieldInterface::TYPE_RADIO_GROUP,
                FieldInterface::TYPE_CHECKBOX_GROUP,
                FieldInterface::TYPE_SELECT,
                FieldInterface::TYPE_DYNAMIC_RECIPIENTS,
            ],
            true
        );

        if (isset($post['types'][$type])) {
            $fieldSpecificPost = $post['types'][$type];
            foreach ($fieldSpecificPost as $key => $value) {
                if (in_array($key, ['values', 'options'], true)) {
                    continue;
                }

                if ($key === 'checked') {
                    $value = $value === 'y';
                }

                if (property_exists($field, $key)) {
                    $validValues[$key] = $value;
                } else {
                    if (in_array($key, $booleanValues, true)) {
                        $value = $value === 'y';
                    }

                    $additionalProperties[$key] = $value;
                }
            }

            $hasValues = isset($fieldSpecificPost['values']) && is_array($fieldSpecificPost['values']);
            $forceLabelOnValue = isset($fieldSpecificPost['custom_values']) && $fieldSpecificPost['custom_values'] !== '1';

            if ($fieldHasOptions && $hasValues) {
                $field->setPostValues($fieldSpecificPost, $forceLabelOnValue);
            } else {
                $validValues['values'] = null;
            }
        }

        if ($type === FieldInterface::TYPE_FILE) {
            if (!isset($validValues['maxFileSizeKB']) || empty($validValues['maxFileSizeKB'])) {
                $validValues['maxFileSizeKB'] = 2048;
            }
        }

        $validValues['additionalProperties'] = empty($additionalProperties) ? null : $additionalProperties;
        $validValues['additionalProperties']['legacyId'] = $classicField['field_id'];
        if (isset($classicField['file_count'])) {
            $validValues['additionalProperties']['fileCount'] = $classicField['file_count'];
        }

        $field->set($validValues);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_BEFORE_SAVE, $field, $isNew)) {
            return $field;
        }

        try {
            $field->save();
        } catch (\Exception $e) {
            // There might be already a field with the same name
        }

        ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_AFTER_SAVE, $field, $isNew);

        return true;
    }

    private function convertData($classicField)
    {
        if ($classicField['field_type'] == 'text' && ($this->containsEmail($classicField['field_name']) || $this->containsEmailValidation($classicField))) {
            $classicField['field_type'] = 'email';
        }

        $newHandle = $this->getValidHandle($classicField);

        if (!$newHandle) {
            return false;
        }

        $data = [
            'label' => $this->getNextValueFromClassicValue('label', $classicField),
            'handle' => $newHandle,
            'instructions' => $this->getNextValueFromClassicValue('instructions', $classicField),
            'required' => $this->getNextValueFromClassicValue('required', $classicField),
            'type' => $this->getNextFieldTypeFromClassicFieldType($this->getClassicFieldType($classicField)),
            'types' => $this->setTypes($classicField),
        ];

        return $data;
    }

    private function getValidHandle($classicField)
    {
        $logger = new EELogger();
        $handle = $this->getNextValueFromClassicValue('handle', $classicField);

        if (!$handle) {
            $logger->log(LoggerInterface::LEVEL_ERROR, 'Did not find a handle for classic field | ' . print_r($classicField, true));
            return false;
        }

        if (in_array($handle, $this->getRestrictedHandles())) {
            $handle = $this->generateValidHandleFromRestrictedHandle($handle);
        }

        return $handle;
    }

    private function getRestrictedHandles()
    {
        return [
            'id',
        ];
    }

    private function generateValidHandleFromRestrictedHandle($handle)
    {
        $this->restrictedHandleCounter = $this->restrictedHandleCounter + 1;

        return $handle  . '_' . HashHelper::hash($this->restrictedHandleCounter);
    }

    private function containsEmail($handle)
    {
        if (strpos($handle, 'email') !== false) {
            return true;
        }

        return false;
    }

    private function containsEmailValidation($classicField)
    {
        $settings = $this->classicFieldHelper->getSettings($classicField);

        if (array_key_exists('field_content_type', $settings)) {
            if ($settings['field_content_type'] == 'email') {
                return true;
            }
        }

        return false;
    }

    private function getClassicFieldType($classicField)
    {
        return $classicField['field_type'];
    }

    private function setTypes($classicField)
    {
        $nextTypeName = $this->getNextFieldTypeFromClassicFieldType($this->getClassicFieldType($classicField));

        if (!$nextTypeName) {
            $this->addToErrors('Could not find next field type for classic field type ' . $this->getClassicFieldType($classicField));
        }

        $types = $this->getNextTypesArray();

        if (!array_key_exists($nextTypeName, $types)) {
            $this->addToErrors('Could not map type ' . $nextTypeName);
        }

        $valueFields = $types[$nextTypeName];

        foreach ($valueFields as $valueFieldName => $defaultValue) {
            $types[$nextTypeName][$valueFieldName] = $this->getNextValueFromClassicValue($valueFieldName, $classicField);
        }

        return $types;
    }

    private function getNextFieldTypeFromClassicFieldType($classicType)
    {
        $mapping = $this->getFieldTypeMapping();

        if (array_key_exists($classicType, $mapping)) {
            return $mapping[$classicType];
        }

        return false;
    }

    private function getNextValueFromClassicValue($nextValueField, $classicField)
    {
        $mapping = $this->getNextValueFromClassicValueMapping();

        if ($nextValueField == null) {
            return '';
        }

        if (array_key_exists($nextValueField, $mapping)) {
            $classicValueMappings = $mapping[$nextValueField];

            if (!$classicValueMappings) {
                return false;
            }

            $classicValueMapping = null;

            if (array_key_exists('*', $classicValueMappings)) {
                $classicValueMapping = $classicValueMappings['*'];
            }

            if (array_key_exists($classicField['field_type'], $classicValueMappings)) {
                $classicValueMapping = $classicValueMappings[$classicField['field_type']];
            }

            if (!$classicValueMapping) {
                return false;
            }

            if ($classicValueMapping['fieldLocation'] === 'normal' && array_key_exists($classicValueMapping['field'], $classicField)) {

                $value = $classicField[$classicValueMapping['field']];

                if (!$value) {
                    return '';
                }

                switch ($classicValueMapping['field'])
                {
                    case 'required':
                        $value = $this->formatClassicRequriedValue($value);
                        break;
                }

                return $value;
            }

            if ($classicValueMapping['fieldLocation'] === 'settings') {

                $fieldSettings = $this->classicFieldHelper->getSettings($classicField);

                if (!array_key_exists($classicValueMapping['field'], $fieldSettings)) {
                    return false;
                }

                $value = $fieldSettings[$classicValueMapping['field']];

                if (!$value) {
                    return '';
                }

                return $this->formatSettingsValue($value, $nextValueField, $classicField);
            }

            if ($classicValueMapping['fieldLocation'] === 'callback') {
                return $this->{$classicValueMapping['callbackMethod']}($nextValueField, $classicField);
            }
        }

        return false;
    }

    private function formatSettingsValue($value, $nextValueField, $classicField)
    {
        if (in_array($classicField['field_type'], $this->getSelectTypes())) {
            if ($nextValueField == 'custom_values') {
                $value = $this->getNextSelectCustomValues($classicField);
            }

            if ($nextValueField == 'values') {
                $value = $this->getNextSelectValues($value, $classicField);
            }

            if ($nextValueField == 'labels') {
                $value = $this->getNextSelectLabels($value, $classicField);
            }

            if ($nextValueField == 'checked_by_default') {
                $value = $this->getNextSelectCheckedByDefault($value, $classicField);
            }
        }

        if (in_array($classicField['field_type'], ['checkbox_group'])) {
            if ($nextValueField == 'custom_values') {
                $value = $this->getNextCheckboxGroupCustomValues($classicField);
            }

            if ($nextValueField == 'values') {
                $value = $this->getNextCheckboxGroupValues($value, $classicField);
            }

            if ($nextValueField == 'labels') {
                $value = $this->getNextCheckboxGroupLabels($value, $classicField);
            }

            if ($nextValueField == 'checked_by_default') {
                $value = $this->getNextCheckoutboxGroupCheckedByDefault($value, $classicField);
            }
        }

        if (in_array($classicField['field_type'], ['multiselect'])) {
            if ($nextValueField == 'custom_values') {
                $value = $this->getNextMultiselectCustomValues($classicField);
            }

            if ($nextValueField == 'values') {
                $value = $this->getNextMultiselectValues($value, $classicField);
            }

            if ($nextValueField == 'labels') {
                $value = $this->getNextMultiselectLabels($value, $classicField);
            }

            if ($nextValueField == 'checked_by_default') {
                $value = $this->getNextMultiselectCheckedByDefault($value, $classicField);
            }
        }

        if (in_array($classicField['field_type'], ['radio'])) {
            if ($nextValueField == 'custom_values') {
                $value = $this->getNextRadioCustomValues($classicField);
            }

            if ($nextValueField == 'values') {
                $value = $this->getNextRadioValues($value, $classicField);
            }

            if ($nextValueField == 'labels') {
                $value = $this->getNextRadioLabels($value, $classicField);
            }

            if ($nextValueField == 'checked_by_default') {
                $value = $this->getNextRadioCheckedByDefault($value, $classicField);
            }
        }

        if (in_array($classicField['field_type'], ['file_upload'])) {
            if ($nextValueField == 'fileKinds') {
                $value = $this->getNextFileAllowedTypes($value);
            }
        }

        return $value;
    }

    /* Classic Field Formatting */

    /* Callback Methods */

    private function getEmailType()
    {
        return 'email';
    }

    private function getDefaultValue($nextValueField, $classicField)
    {
        $defaultValue = '';

        if ($classicField['field_type'] == 'checkbox') {
            $defaultValue = 'yes';
        }

        return $defaultValue;
    }

    private function setCustomValuesTrue($nextValueField, $classicField)
    {
        return '1';
    }

    private function getEmptyString($nextValueField, $classicField)
    {
        return '';
    }

    private function getDataValues($data)
    {
        $values = [];

        if (!$data) {
            return $values;
        }

        foreach ($data as $value => $label) {
            $values[] = $value;
        }

        return $values;
    }

    private function getDataLabels($data)
    {
        $labels = [];

        if (!$data) {
            return $labels;
        }

        foreach ($data as $value => $label) {
            $labels[] = $label;
        }

        return $labels;
    }

    private function getEmptyDataArray($data)
    {
        $array = [];

        foreach ($data as $value => $label) {
            $array[] = '0';
        }

        return $array;
    }

    private function getCountriesValues($nextValueField, $classicField)
    {
        $countries = $classicField['countries'];

        return $this->getDataValues($countries);
    }

    private function getCountriesLabels($nextValueField, $classicField)
    {
        $countries = $classicField['countries'];

        return $this->getDataLabels($countries);
    }

    private function getCountriesEmptyValues($nextValueField, $classicField)
    {
        $countries = $classicField['countries'];

        return $this->getEmptyDataArray($countries);
    }

    private function getStatesValues($nextValueField, $classicField)
    {
        $states = $classicField['states'];

        return $this->getDataValues($states);
    }

    private function getStatesLabels($nextValueField, $classicField)
    {
        $states = $classicField['states'];

        return $this->getDataLabels($states);
    }

    private function getStatesEmptyValues($nextValueField, $classicField)
    {
        $states = $classicField['states'];

        return $this->getEmptyDataArray($states);
    }

    private function getProvincesValues($nextValueField, $classicField)
    {
        $states = $classicField['provinces'];

        return $this->getDataValues($states);
    }

    private function getProvincesLabels($nextValueField, $classicField)
    {
        $states = $classicField['provinces'];

        return $this->getDataLabels($states);
    }

    private function getProvincesEmptyValues($nextValueField, $classicField)
    {
        $states = $classicField['provinces'];

        return $this->getEmptyDataArray($states);
    }

    /* Files */

    private function getNextFileAllowedTypes($value)
    {
        $legacyTypes = explode("|", $value);

        if (!$legacyTypes) {
            return [];
        }

        // Old => New
        $mapping = [
            'jpg' => 'image',
            'png' => 'image',
            'gif' => 'gif',
            'pdf' => 'pdf',
        ];

        $newTypes = [];

        foreach ($legacyTypes as $legacyType)
        {
            if (array_key_exists($legacyType, $mapping)) {

                $newTypeName = $mapping[$legacyType];

                if (in_array($newTypeName, $newTypes)) continue;

                $newTypes[] = $newTypeName;
            }
        }

        return $newTypes;
    }


    /* Radio */

    private function getNextRadioCheckedByDefault($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelRadio($classicField);

        if ($channelField) {
            return ['0'];
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaRadio($classicField);

        if ($valuesInTextArea) {
            $value = explode("\n", $value);
        }

        $values = [];

        if (!$value) {
            return $values;
        }

        foreach ($value as $key => $row) {
            $values[] = '0';
        }

        return $values;
    }

    private function getNextRadioLabels($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelRadio($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaRadio($classicField);

        if ($valuesInTextArea) {
            return  explode("\n", $value);
        }

        $labels = [];

        if (!$value) {
            return $labels;
        }

        foreach ($value as $key => $row) {
            $labels[] = $row;
        }

        return $labels;
    }

    private function getNextRadioValues($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelRadio($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaRadio($classicField);

        if ($valuesInTextArea) {
            return $arr = explode("\n", $value);
        }

        $values = [];
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForRadio($classicField);

        if (!$value) {
            return $values;
        }

        if ($customOptionsEnabled) {

            foreach ($value as $key => $row) {
                $values[] = $key;
            }

        } else {
            foreach ($value as $key => $row) {
                $values[] = $row;
            }
        }

        return $values;
    }

    private function getNextRadioCustomValues($classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelRadio($classicField);
        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaRadio($classicField);
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForRadio($classicField);

        if ($customOptionsEnabled && !$valuesInTextArea && !$channelField) {
            return '1';
        }

        return '0';
    }


    /* Multiselect */

    private function getNextMultiselectCheckedByDefault($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelMultiselect($classicField);

        if ($channelField) {
            return ['0'];
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaMultiselect($classicField);

        if ($valuesInTextArea) {
            $value = explode("\n", $value);
        }

        $values = [];

        if (!$value) {
            return $values;
        }

        foreach ($value as $key => $row) {
            $values[] = '0';
        }

        return $values;
    }

    private function getNextMultiselectLabels($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelMultiselect($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaMultiselect($classicField);

        if ($valuesInTextArea) {
            return $arr = explode("\n", $value);
        }

        $labels = [];

        if (!$value) {
            return $labels;
        }

        foreach ($value as $key => $row) {
            $labels[] = $row;
        }

        return $labels;
    }

    private function getNextMultiselectValues($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelMultiselect($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaMultiselect($classicField);

        if ($valuesInTextArea) {
            return $arr = explode("\n", $value);
        }

        $values = [];
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForMultiselect($classicField);

        if (!$value) {
            return $values;
        }

        if ($customOptionsEnabled) {

            foreach ($value as $key => $row) {
                $values[] = $key;
            }

        } else {
            foreach ($value as $key => $row) {
                $values[] = $row;
            }
        }

        return $values;
    }

    private function getNextMultiselectCustomValues($classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelMultiselect($classicField);
        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaMultiselect($classicField);
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForMultiselect($classicField);

        if ($customOptionsEnabled && !$valuesInTextArea && !$channelField) {
            return '1';
        }

        return '0';
    }


    /* Checkbox Group */

    private function getNextCheckoutboxGroupCheckedByDefault($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelCheckboxGroup($classicField);

        if ($channelField) {
            return ['0'];
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaCheckboxGroup($classicField);

        if ($valuesInTextArea) {
            $value = explode("\n", $value);
        }

        $values = [];

        if (!$value) {
            return $values;
        }

        foreach ($value as $key => $row) {
            $values[] = '0';
        }

        return $values;
    }

    private function getNextCheckboxGroupLabels($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelCheckboxGroup($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaCheckboxGroup($classicField);

        if ($valuesInTextArea) {
            return $arr = explode("\n", $value);
        }

        $labels = [];

        if (!$value) {
            return $labels;
        }

        foreach ($value as $key => $row) {
            $labels[] = $row;
        }

        return $labels;
    }

    private function getNextCheckboxGroupValues($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelCheckboxGroup($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaCheckboxGroup($classicField);

        if ($valuesInTextArea) {
            return $arr = explode("\n", $value);
        }

        $values = [];
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForCheckboxGroup($classicField);

        if (!$value) {
            return $values;
        }

        if ($customOptionsEnabled) {

            foreach ($value as $key => $row) {
                $values[] = $key;
            }

        } else {
            foreach ($value as $key => $row) {
                $values[] = $row;
            }
        }

        return $values;
    }

    private function getNextCheckboxGroupCustomValues($classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomValuesChannelCheckboxGroup($classicField);
        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaCheckboxGroup($classicField);
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForCheckboxGroup($classicField);

        if ($customOptionsEnabled && !$valuesInTextArea && !$channelField) {
            return '1';
        }

        return '0';
    }


    /* Labels */

    private function getNextSelectCheckedByDefault($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomChannelForSelect($classicField);

        if ($channelField) {
            return [0];
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaSelect($classicField);

        if ($valuesInTextArea) {
            $value = explode("\n", $value);
        }

        $values = [];

        if (!$value) {
            return $values;
        }

        foreach ($value as $key => $row) {
            $values[] = '0';
        }

        return $values;
    }

    private function getNextSelectLabels($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomChannelForSelect($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaSelect($classicField);

        if ($valuesInTextArea) {
            return  explode("\n", $value);
        }

        $labels = [];

        if (!$value) {
            return $labels;
        }

        foreach ($value as $key => $row) {
            $labels[] = $row;
        }

        return $labels;
    }

    private function getNextSelectValues($value, $classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomChannelForSelect($classicField);

        if ($channelField) {
            return $this->getCannotMapOption();
        }

        $valuesInTextArea = $this->classicFieldHelper->isCustomValuesTextAreaSelect($classicField);

        if ($valuesInTextArea) {
            return explode("\n", $value);
        }

        $values = [];
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForSelect($classicField);

        if (!$value) {
            return $values;
        }

        if ($customOptionsEnabled) {

            foreach ($value as $key => $row) {
                $values[] = $key;
            }

        } else {
            foreach ($value as $key => $row) {
                $values[] = $row;
            }
        }

        return $values;
    }

    private function getNextSelectCustomValues($classicField)
    {
        $channelField = $this->classicFieldHelper->isCustomChannelForSelect($classicField);
        $customOptionsEnabled = $this->classicFieldHelper->isCustomValuesEnabledForSelect($classicField);

        if ($customOptionsEnabled && !$channelField) {
            return '1';
        }

        return '0';
    }

    private function formatClassicRequriedValue($value)
    {
        if ($value === 'y') {
            return true;
        }

        return false;
    }

    private function addToErrors($message)
    {
        $this->errors[] = $message;

        if (self::STRICT_MODE) throw new FreeformException($message);
    }

    private function getClassicFieldHelper()
    {
        $fieldService = 'Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFieldHelper';
        if (class_exists($fieldService)) {
            /** @var \Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFieldHelper $fieldService */
            $fieldService = new $fieldService();

            return $fieldService;
        }

        return false;
    }

    private function getCannotMapOption()
    {
        return [
            'Could not migrate channel options',
        ];
    }

    private function getSelectTypes()
    {
        return [
            'select',
        ];
    }

    /* Classic Field Value Mapping */

    private function getNextValueFromClassicValueMapping()
    {
        // Next Value Field Type => Classic Value Field Type

        $mapping = [
            'label' => [
                '*' => [
                    'fieldLocation' => 'normal',
                    'field' => 'field_label',
                ],
            ],
            'handle' => [
                '*' => [
                    'fieldLocation' => 'normal',
                    'field' => 'field_name',
                ],
            ],
            'instructions' => [
                '*' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getEmptyString',
                ],
            ],
            'required' => [
                '*' => [
                    'fieldLocation' => 'normal',
                    'field' => 'required',
                ],
            ],
            'type' => [
                '*' => [
                    'fieldLocation' => 'normal',
                    'field' => 'field_type',
                ],
                'email' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getEmailType',
                ],
            ],
            'value' => [
                '*' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getDefaultValue',
                ],
                'hidden' => [
                    'fieldLocation' => 'settings',
                    'field' => 'default_data',
                ],
            ],
            'placeholder' => [
                '*' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getEmptyString',
                ],
            ],
            'custom_values' => [
                'select' => [
                    'fieldLocation' => 'settings',
                    'field' => 'select_list_type',
                ],
                'country_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'setCustomValuesTrue',
                ],
                'state_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'setCustomValuesTrue',
                ],
                'province_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'setCustomValuesTrue',
                ],
                'checkbox_group' => [
                    'fieldLocation' => 'settings',
                    'field' => 'checkbox_group_list_type',
                ],
                'multiselect' => [
                    'fieldLocation' => 'settings',
                    'field' => 'multiselect_list_type',
                    ],
                'radio' => [
                    'fieldLocation' => 'settings',
                    'field' => 'radio_list_type',
                ],
            ],
            'checked_by_default' => [
                'select' => [
                    'fieldLocation' => 'settings',
                    'field' => 'select_field_list_items',
                ],
                'country_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getCountriesValues',
                ],
                'state_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getStatesValues',
                ],
                'province_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getProvincesValues',
                ],
                'checkbox_group' => [
                    'fieldLocation' => 'settings',
                    'field' => 'checkbox_group_field_list_items',
                ],
                'multiselect' => [
                    'fieldLocation' => 'settings',
                    'field' => 'multiselect_field_list_items',
                ],
                'radio' => [
                    'fieldLocation' => 'settings',
                    'field' => 'radio_field_list_items',
                ],
            ],
            'values' => [
                'select' => [
                    'fieldLocation' => 'settings',
                    'field' => 'select_field_list_items',
                ],
                'country_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getCountriesValues',
                ],
                'state_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getStatesValues',
                ],
                'province_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getProvincesValues',
                ],
                'checkbox_group' => [
                    'fieldLocation' => 'settings',
                    'field' => 'checkbox_group_field_list_items',
                ],
                'multiselect' => [
                    'fieldLocation' => 'settings',
                    'field' => 'multiselect_field_list_items',
                ],
                'radio' => [
                    'fieldLocation' => 'settings',
                    'field' => 'radio_field_list_items',
                ],
            ],
            'labels' => [
                'select' => [
                    'fieldLocation' => 'settings',
                    'field' => 'select_field_list_items',
                ],
                'country_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getCountriesLabels',
                ],
                'state_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getStatesLabels',
                ],
                'province_select' => [
                    'fieldLocation' => 'callback',
                    'callbackMethod' => 'getProvincesLabels',
                ],
                'checkbox_group' => [
                    'fieldLocation' => 'settings',
                    'field' => 'checkbox_group_field_list_items',
                ],
                'multiselect' => [
                    'fieldLocation' => 'settings',
                    'field' => 'multiselect_field_list_items',
                ],
                'radio' => [
                    'fieldLocation' => 'settings',
                    'field' => 'radio_field_list_items',
                ],
            ],
            'rows' => [
                'textarea' => [
                    'fieldLocation' => 'settings',
                    'field' => 'field_ta_rows',
                ],
            ],
            'fileKinds' => [
                'file_upload' => [
                    'fieldLocation' => 'settings',
                    'field' => 'allowed_file_types',
                ],
            ],
            'assetSourceId' => [
                'file_upload' => [
                    'fieldLocation' => 'settings',
                    'field' => 'file_upload_location',
                ],
            ],
        ];

        return $mapping;
    }

    private function getFieldTypeMapping()
    {
        // Classic Field Type => Next Field Type

        return [
            'text' => FieldInterface::TYPE_TEXT,
            'textarea' => FieldInterface::TYPE_TEXTAREA,
            'email' => FieldInterface::TYPE_EMAIL,
            'hidden' => FieldInterface::TYPE_HIDDEN,
            'checkbox' => FieldInterface::TYPE_CHECKBOX,
            'country_select' => FieldInterface::TYPE_SELECT,
            'select' => FieldInterface::TYPE_SELECT,
            'state_select' => FieldInterface::TYPE_SELECT,
            'province_select' => FieldInterface::TYPE_SELECT,
            'checkbox_group' => FieldInterface::TYPE_CHECKBOX_GROUP,
            'multiselect' => FieldInterface::TYPE_CHECKBOX_GROUP,
            'file' => FieldInterface::TYPE_FILE,
            'file_upload' => FieldInterface::TYPE_FILE,
            'rating' => FieldInterface::TYPE_RATING,
            'datetime' => FieldInterface::TYPE_DATETIME,
            'website' => FieldInterface::TYPE_WEBSITE,
            'number' => FieldInterface::TYPE_NUMBER,
            'phone' => FieldInterface::TYPE_PHONE,
            'confirmation' => FieldInterface::TYPE_CONFIRMATION,
            'regex' => FieldInterface::TYPE_REGEX,
            'radio' => FieldInterface::TYPE_RADIO_GROUP,
        ];
    }

    private function getNextTypesArray()
    {
        return [
            FieldInterface::TYPE_TEXT =>
                [
                    'value' => '',
                    'placeholder' => '',
                ],
            FieldInterface::TYPE_TEXTAREA =>
                [
                    'value' => '',
                    'placeholder' => '',
                    'rows' => '',
                ],
            FieldInterface::TYPE_EMAIL =>
                [
                    'placeholder' => '',
                ],
            FieldInterface::TYPE_HIDDEN =>
                [
                    'value' => '',
                ],
            FieldInterface::TYPE_CHECKBOX =>
                [
                    'value' => '',
                ],
            FieldInterface::TYPE_CHECKBOX_GROUP =>
                [
                    'custom_values' => '0',
                    'checked_by_default' => [],
                    'values' => [],
                    'labels' => [],
                ],
            FieldInterface::TYPE_SELECT =>
                [
                    'custom_values' => '0',
                    'checked_by_default' => [],
                    'values' => [],
                    'labels' => [],
                ],
            FieldInterface::TYPE_FILE =>
                [
                    'fileKinds' => [],
                    'assetSourceId' => '',
                    'maxFileSizeKB' => '',
                ],
            FieldInterface::TYPE_RATING =>
                [
                    'value' => '',
                    'maxValue' => '',
                    'colorIdle' => '',
                    'colorHover' => '',
                    'colorSelected' => '',
                ],
            FieldInterface::TYPE_RADIO_GROUP =>
                [
                    'custom_values' => '0',
                    'checked_by_default' => [],
                    'values' => [],
                    'labels' => [],
                ],
            FieldInterface::TYPE_DATETIME =>
                [
                    'dateTimeType' => '',
                    'initialValue' => '',
                    'placeholder' => '',
                    'dateOrder' => '',
                    'dateSeparator' => '',
                    'clockSeparator' => '',
                ],
            FieldInterface::TYPE_WEBSITE =>
                [
                    'value' => '',
                    'placeholder' => '',
                ],
            FieldInterface::TYPE_NUMBER =>
                [
                    'value' => '',
                    'placeholder' => '',
                    'minValue' => '',
                    'maxValue' => '',
                    'minLength' => '',
                    'maxLength' => '',
                    'decimalCount' => '',
                    'decimalSeparator' => '',
                    'thousandsSeparator' => '',
                ],
            FieldInterface::TYPE_PHONE =>
                [
                    'value' => '',
                    'placeholder' => '',
                    'pattern' => '',
                ],
            FieldInterface::TYPE_CONFIRMATION =>
                [
                    'value' => '',
                    'placeholder' => '',
                ],
            FieldInterface::TYPE_REGEX =>
                [
                    'value' => '',
                    'placeholder' => '',
                    'pattern' => '',
                    'message' => '',
                ],
        ];
    }
}
