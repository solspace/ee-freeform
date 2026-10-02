<?php

// Run with `php scripts/check-field-types.php`; no EE installation required.
error_reporting(E_ALL);
set_error_handler(static function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;
use Solspace\Addons\FreeformNext\Library\Helpers\FieldTypeHelper;
use Solspace\Addons\FreeformNext\Library\Pro\Fields as ProFields;
use Solspace\Addons\FreeformNext\Services\FieldsService;

function check(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException($label);
    }
    echo "PASS: $label\n";
}

$classes = [
    'text' => Fields\TextField::class,
    'textarea' => Fields\TextareaField::class,
    'phone' => ProFields\PhoneField::class,
    'website' => ProFields\WebsiteField::class,
    'regex' => ProFields\RegexField::class,
    'select' => Fields\SelectField::class,
    'radio_group' => Fields\RadioGroupField::class,
    'multiple_select' => Fields\MultipleSelectField::class,
    'checkbox_group' => Fields\CheckboxGroupField::class,
];

foreach ($classes as $sourceType => $class) {
    $source = (new ReflectionClass($class))->newInstanceWithoutConstructor();
    foreach (FieldTypeHelper::getCompatibleTypes($sourceType) as $targetType) {
        $target = (new ReflectionClass($classes[$targetType]))->newInstanceWithoutConstructor();
        check($source->isArrayValue() === $target->isArrayValue(), "$sourceType -> $targetType keeps submission storage shape");
    }
}

$properties = [
    'id' => 42, 'hash' => 'instance-a', 'type' => 'phone',
    'handle' => 'contact', 'label' => 'Custom label', 'required' => true,
    'value' => "00123\nsecond line", 'placeholder' => 'Custom placeholder',
    'pattern' => '(xxx) xxx-xxxx', 'useJsMask' => true,
    'customAttributes' => ['input' => ['class' => 'custom']],
];
$defaults = ['id' => 42, 'hash' => 'default', 'type' => 'regex', 'label' => 'Default label', 'pattern' => '^.*$', 'message' => 'Invalid'];
$converted = FieldTypeHelper::convertProperties($properties, $defaults);
check($converted['pattern'] === '^.*$' && !isset($converted['useJsMask']), 'phone pattern is replaced with regex configuration');
check($converted['hash'] === 'instance-a' && $converted['label'] === 'Custom label' && $converted['required'], 'instance identity and overrides survive');
check($converted['value'] === $properties['value'] && $converted['customAttributes'] === $properties['customAttributes'], 'newlines, leading zeroes, and custom attributes survive');
$text = FieldTypeHelper::convertProperties($converted, ['type' => 'text']);
check(!isset($text['pattern'], $text['message']), 'obsolete validation settings do not carry through Text');
$textarea = FieldTypeHelper::convertProperties($text, ['type' => 'textarea', 'rows' => 2]);
check($textarea['rows'] === 2 && $textarea['value'] === $properties['value'], 'Textarea gets defaults without changing content');
$textAgain = FieldTypeHelper::convertProperties($textarea, ['type' => 'text']);
check($textAgain['value'] === $properties['value'], 'Textarea -> Text preserves multiline content');

$options = [['label' => 'Zero', 'value' => '0'], ['label' => 'Two', 'value' => '002']];
foreach ([['select', 'radio_group'], ['multiple_select', 'checkbox_group']] as [$source, $target]) {
    $original = ['id' => 42, 'type' => $source, 'options' => $options, 'value' => '0', 'values' => ['0', '002'], 'source' => 'entries', 'target' => '7', 'configuration' => ['labelField' => 'title']];
    $result = FieldTypeHelper::convertProperties($original, ['type' => $target, 'options' => [], 'values' => []]);
    check($result['options'] === $options && $result['value'] === '0' && $result['values'] === ['0', '002'], "$source -> $target keeps options and defaults");
    check($result['source'] === 'entries' && $result['configuration'] === $original['configuration'], "$source -> $target keeps data feeders");
}

$layout = (object) [
    'composer' => (object) [
        'properties' => (object) [
            'instance-a' => (object) $properties,
            'instance-b' => (object) array_replace($properties, ['hash' => 'instance-b', 'label' => 'Second instance']),
            'form' => (object) ['type' => 'form', 'crmMapping' => (object) ['contact' => 'instance-a']],
            'untouched' => (object) ['id' => 99, 'type' => 'email'],
        ],
        'layout' => [['instance-a'], ['instance-b']],
    ],
    'context' => (object) ['metadata' => (object) []],
];
$json = json_encode($layout, JSON_THROW_ON_ERROR);
$changed = json_decode(FieldTypeHelper::convertLayout($json, 42, $defaults), false, 512, JSON_THROW_ON_ERROR);
check($changed->composer->properties->{'instance-a'}->type === 'regex' && $changed->composer->properties->{'instance-b'}->type === 'regex', 'every saved instance changes');
check($changed->composer->layout === $layout->composer->layout && $changed->composer->properties->form == $layout->composer->properties->form, 'layout and integration mappings survive');
check($changed->composer->properties->untouched == $layout->composer->properties->untouched && $changed->context == $layout->context, 'unrelated fields and empty objects survive');
check(FieldTypeHelper::convertLayout($json, 999, $defaults) === null, 'unrelated forms need no write');

foreach ([['text', 'email'], ['email', 'text'], ['select', 'multiple_select'], ['checkbox_group', 'radio_group'], ['file', 'text'], ['table', 'textarea'], ['confirmation', 'text'], ['number', 'text']] as [$source, $target]) {
    try {
        FieldTypeHelper::convertProperties(['type' => $source], ['type' => $target]);
        throw new RuntimeException("$source -> $target must be blocked");
    } catch (InvalidArgumentException) {
        check(true, "$source -> $target is blocked");
    }
}

$service = new FieldsService();
check(array_keys($service->getCompatibleFieldTypes('select')) === ['select', 'radio_group'], 'picker only offers compatible types');
check(array_keys($service->getCompatibleFieldTypes('email')) === ['email'], 'notification fields retain their current type');
echo 'Runtime: ' . PHP_VERSION . "\n";
