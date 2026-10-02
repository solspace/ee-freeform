<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

use InvalidArgumentException;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use stdClass;

class FieldTypeHelper
{
    // Keep storage shape and field behavior compatible. Email and Dynamic
    // Recipients contain arrays with notification semantics; files, tables,
    // confirmation, and numeric/date fields need separate conversion support.
    private const GROUPS = [
        [FieldInterface::TYPE_TEXT, FieldInterface::TYPE_TEXTAREA, FieldInterface::TYPE_PHONE, FieldInterface::TYPE_WEBSITE, FieldInterface::TYPE_REGEX],
        [FieldInterface::TYPE_SELECT, FieldInterface::TYPE_RADIO_GROUP],
        [FieldInterface::TYPE_MULTIPLE_SELECT, FieldInterface::TYPE_CHECKBOX_GROUP],
    ];

    public static function getCompatibleTypes(string $type): array
    {
        foreach (self::GROUPS as $group) {
            if (in_array($type, $group, true)) {
                return $group;
            }
        }

        return [$type];
    }

    /** Preserve instance overrides, options, data feeders, hashes, and mappings. */
    public static function convertProperties(array $properties, array $defaults): array
    {
        $sourceType = $properties['type'] ?? '';
        $targetType = $defaults['type'];
        if (!in_array($targetType, self::getCompatibleTypes($sourceType), true)) {
            throw new InvalidArgumentException('This field type conversion is not supported.');
        }

        if ($sourceType === $targetType) {
            return $properties;
        }

        // Phone masks and regex patterns are different syntaxes. Neither can
        // carry over to the other type, including when returning through Text.
        unset($properties['pattern'], $properties['message'], $properties['useJsMask']);
        $properties['type'] = $targetType;

        return array_replace($defaults, $properties);
    }

    /** Update every instance without reserializing unrelated composer settings. */
    public static function convertLayout(string $json, int $fieldId, array $defaults): ?string
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

            $converted = self::convertProperties((array) $property, $defaults);
            if ($converted !== (array) $property) {
                $properties->$hash = (object) $converted;
                $changed = true;
            }
        }

        return $changed ? json_encode($state, JSON_THROW_ON_ERROR) : null;
    }
}
