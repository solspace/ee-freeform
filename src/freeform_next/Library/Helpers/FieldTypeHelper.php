<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

use InvalidArgumentException;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use stdClass;

class FieldTypeHelper
{
    private const TEXT_TYPES = ['text', 'textarea', 'email', 'hidden', 'regex', 'datetime', 'number', 'phone', 'website'];
    private const CHOICE_TYPES = ['select', 'radio_group'];
    private const MULTIPLE_TYPES = ['multiple_select', 'checkbox_group'];

    public static function getCompatibleTypes(string $type): array
    {
        if (in_array($type, self::TEXT_TYPES, true)) {
            return self::TEXT_TYPES;
        }
        if (in_array($type, self::CHOICE_TYPES, true)) {
            return array_merge(self::CHOICE_TYPES, self::MULTIPLE_TYPES, ['dynamic_recipients']);
        }
        if (in_array($type, self::MULTIPLE_TYPES, true)) {
            return self::MULTIPLE_TYPES;
        }

        return [$type];
    }

    public static function isArrayType(string $type): bool
    {
        return in_array($type, ['email', 'multiple_select', 'checkbox_group', 'dynamic_recipients'], true);
    }

    public static function normalizeOptions(array $options): array
    {
        return array_values(array_map(static function ($option) {
            return $option instanceof \JsonSerializable ? $option->jsonSerialize() : (array) $option;
        }, $options));
    }

    /** Recipient values are option indexes, never the old choice value. */
    public static function recipientIndexes($value, array $options, bool $strict = true): array
    {
        if ($value === null) {
            return [];
        }
        foreach (self::normalizeOptions($options) as $index => $option) {
            if ((string) $option['value'] === (string) $value) {
                return [$index];
            }
        }
        if ($value === '' || !$strict) {
            return [];
        }

        throw new InvalidArgumentException('An existing selection is missing from this field’s options. Restore the missing option before changing to Dynamic Recipients.');
    }

    /** Migrate the stored representation while retaining all submitted content. */
    public static function convertStoredValue(?string $value, string $source, string $target, array $options = []): ?string
    {
        if (!in_array($target, self::getCompatibleTypes($source), true)) {
            throw new InvalidArgumentException('This field type conversion is not supported.');
        }
        if ($value === null) {
            return null;
        }
        if ($target === FieldInterface::TYPE_DYNAMIC_RECIPIENTS && $source !== $target) {
            return json_encode(self::recipientIndexes($value, $options), JSON_THROW_ON_ERROR);
        }
        if (self::isArrayType($source) === self::isArrayType($target)) {
            return $value;
        }
        if (self::isArrayType($target)) {
            return json_encode($value === '' ? [] : [$value], JSON_THROW_ON_ERROR);
        }

        // Email is the only supported array -> scalar conversion. Keep every
        // address rather than silently retaining only the first one.
        $values = $value === '' ? [] : json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($values) || !array_is_list($values)) {
            throw new InvalidArgumentException('An existing Email value is not a valid list. Repair it before changing the field type.');
        }
        foreach ($values as $item) {
            if (!is_scalar($item) && $item !== null) {
                throw new InvalidArgumentException('An existing Email value contains invalid data. Repair it before changing the field type.');
            }
        }

        return implode("\n", $values);
    }

    /** Preserve instance identity and common overrides; reset type-only settings. */
    public static function convertProperties(array $properties, array $defaults): array
    {
        $source = $properties['type'] ?? '';
        $target = $defaults['type'];
        if (!in_array($target, self::getCompatibleTypes($source), true)) {
            throw new InvalidArgumentException('This field type conversion is not supported.');
        }
        if ($source === $target) {
            return $properties;
        }

        $value = self::isArrayType($source)
            ? ($properties['values'] ?? [])
            : ($properties['value'] ?? '');
        if ($source === FieldInterface::TYPE_DATETIME && $value === '') {
            $value = $properties['initialValue'] ?? '';
        }
        // Settings belonging to the old type must not override new defaults.
        foreach (['pattern', 'message', 'useJsMask', 'rows', 'notificationId', 'format',
            'initialValue', 'dateTimeType', 'generatePlaceholder', 'dateOrder', 'date4DigitYear',
            'dateLeadingZero', 'dateSeparator', 'clock24h', 'lowercaseAMPM', 'clockSeparator',
            'clockAMPMSeparate', 'useDatepicker', 'minLength', 'maxLength', 'minValue', 'maxValue',
            'decimalCount', 'decimalSeparator', 'thousandsSeparator', 'allowNegative',
            'showAsRadio', 'showAsCheckboxes', 'value', 'values'] as $key) {
            unset($properties[$key]);
        }
        if ($target === FieldInterface::TYPE_DYNAMIC_RECIPIENTS) {
            $properties['options'] = self::normalizeOptions($properties['options'] ?? []);
            $properties['values'] = self::recipientIndexes($value, $properties['options']);
            $properties['notificationId'] = 0;
            $properties['showAsRadio'] = $source === FieldInterface::TYPE_RADIO_GROUP;
            unset($properties['source'], $properties['target'], $properties['configuration'], $properties['showCustomValues']);
        } elseif (self::isArrayType($target)) {
            $properties['values'] = self::isArrayType($source) ? $value : ($value === '' ? [] : [$value]);
        } else {
            $properties['value'] = self::isArrayType($source) ? implode("\n", $value) : $value;
        }
        $properties['type'] = $target;

        return array_replace($defaults, $properties);
    }

    /** Decode as objects to preserve unrelated empty objects and composer state. */
    public static function convertLayout(string $json, int $fieldId, array $defaults, ?callable $prepare = null): ?string
    {
        $state = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        $properties = $state->composer->properties ?? null;
        if (!$properties instanceof stdClass) {
            throw new InvalidArgumentException('The form layout has invalid field properties.');
        }

        $changed = false;
        foreach ($properties as $hash => $property) {
            if (!$property instanceof stdClass || (int) ($property->id ?? 0) !== $fieldId) {
                continue;
            }
            $original = (array) $property;
            $prepared = $prepare ? $prepare($original) : $original;
            $converted = self::convertProperties($prepared, $defaults);
            if ($converted !== $original) {
                $properties->$hash = (object) $converted;
                $changed = true;
            }
        }

        return $changed ? json_encode($state, JSON_THROW_ON_ERROR) : null;
    }
}
