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
    'email' => Fields\EmailField::class,
    'hidden' => Fields\HiddenField::class,
    'phone' => ProFields\PhoneField::class,
    'website' => ProFields\WebsiteField::class,
    'regex' => ProFields\RegexField::class,
    'datetime' => ProFields\DatetimeField::class,
    'number' => ProFields\NumberField::class,
    'select' => Fields\SelectField::class,
    'radio_group' => Fields\RadioGroupField::class,
    'multiple_select' => Fields\MultipleSelectField::class,
    'checkbox_group' => Fields\CheckboxGroupField::class,
    'dynamic_recipients' => Fields\DynamicRecipientField::class,
];
$textTypes = ['text', 'textarea', 'email', 'hidden', 'regex', 'datetime', 'number', 'phone', 'website'];
$choices = ['select', 'radio_group', 'multiple_select', 'checkbox_group', 'dynamic_recipients'];
foreach ($classes as $sourceType => $class) {
    $source = (new ReflectionClass($class))->newInstanceWithoutConstructor();
    check($source->isArrayValue() === FieldTypeHelper::isArrayType($sourceType), "$sourceType storage matches the runtime field");
    $expected = in_array($sourceType, $textTypes, true) ? $textTypes
        : (in_array($sourceType, ['select', 'radio_group'], true) ? $choices
        : (in_array($sourceType, ['multiple_select', 'checkbox_group'], true) ? ['multiple_select', 'checkbox_group'] : [$sourceType]));
    $actual = FieldTypeHelper::getCompatibleTypes($sourceType);
    sort($actual); sort($expected);
    check($actual === $expected, "$sourceType allows exactly the requested directions");
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
    check($result['options'] === $options && ($source === 'select' ? $result['value'] === '0' : $result['values'] === ['0', '002']), "$source -> $target keeps options and defaults");
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

foreach ([['checkbox_group', 'radio_group'], ['multiple_select', 'select'], ['checkbox_group', 'dynamic_recipients'], ['dynamic_recipients', 'select'], ['file', 'text'], ['table', 'textarea'], ['confirmation', 'text'], ['rating', 'number'], ['checkbox', 'text']] as [$source, $target]) {
    try {
        FieldTypeHelper::convertProperties(['type' => $source], ['type' => $target]);
        throw new RuntimeException("$source -> $target must be blocked");
    } catch (InvalidArgumentException) {
        check(true, "$source -> $target is blocked");
    }
}

foreach (['text', 'textarea', 'hidden', 'regex', 'datetime', 'number', 'phone', 'website'] as $source) {
    foreach ([null, '', '0', "00123\nsecond line", '["literal JSON"]', 'José'] as $value) {
        $encoded = FieldTypeHelper::convertStoredValue($value, $source, 'email');
        check($encoded === null || json_decode($encoded, true) === ($value === '' ? [] : [$value]), "$source -> Email preserves a stored value");
        check(FieldTypeHelper::convertStoredValue($encoded, 'email', $source) === $value, "Email -> $source round trips the stored value");
    }
}
check(FieldTypeHelper::convertStoredValue('["a@example.com","b@example.com"]', 'email', 'text') === "a@example.com\nb@example.com", 'Email -> Text retains every address');
foreach (['select', 'radio_group'] as $source) {
    foreach (['multiple_select', 'checkbox_group'] as $target) {
        check(FieldTypeHelper::convertStoredValue('0', $source, $target) === '["0"]', "$source -> $target retains a zero-valued selection");
        $converted = FieldTypeHelper::convertProperties(['type' => $source, 'value' => '0', 'options' => $options], ['type' => $target]);
        check($converted['values'] === ['0'] && !isset($converted['value']), "$source -> $target converts the instance default");
    }
    $recipientOptions = [['value' => '1', 'label' => 'First'], ['value' => '0', 'label' => 'Second']];
    check(FieldTypeHelper::convertStoredValue('0', $source, 'dynamic_recipients', $recipientOptions) === '[1]', "$source -> Dynamic Recipients matches numeric option values exactly");
    $recipient = FieldTypeHelper::convertProperties(['type' => $source, 'value' => '0', 'options' => $recipientOptions, 'notificationId' => 99], ['type' => 'dynamic_recipients']);
    check($recipient['values'] === [1] && $recipient['notificationId'] === 0, 'recipient default indexes are correct and notifications start disabled');
}
foreach (['["a@example.com",{}]', '"not a list"', '{broken'] as $malformed) {
    try {
        FieldTypeHelper::convertStoredValue($malformed, 'email', 'text');
        throw new RuntimeException('Malformed Email data must block conversion');
    } catch (InvalidArgumentException | JsonException) { check(true, 'malformed Email data blocks conversion'); }
}
try {
    FieldTypeHelper::convertStoredValue('removed', 'select', 'dynamic_recipients', $options);
    throw new RuntimeException('Missing recipient selection must block conversion');
} catch (InvalidArgumentException) { check(true, 'missing historical option blocks recipient conversion'); }
$recipient = (new ReflectionClass(Fields\DynamicRecipientField::class))->newInstanceWithoutConstructor();
$reflection = new ReflectionObject($recipient);
$reflection->getProperty('customAttributes')->setValue($recipient, new \Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFieldAttributes($recipient));
$reflection->getProperty('options')->setValue($recipient, [new Fields\DataContainers\Option('First', '1'), new Fields\DataContainers\Option('Second', '0')]);
$reflection->getProperty('values')->setValue($recipient, [1]);
check($recipient->getValue() === [1], 'numeric recipient indexes never also match another option value');
$reflection->getProperty('options')->setValue($recipient, [new Fields\DataContainers\Option('First', 'a@example.com'), new Fields\DataContainers\Option('Second', 'b@example.com')]);
$reflection->getProperty('values')->setValue($recipient, ['b@example.com']);
check($recipient->getValue() === [1], 'legacy email option values still resolve to indexes');

$service = new FieldsService();
check(count($service->getCompatibleFieldTypes('select')) === 5, 'choice picker offers all five approved targets');
check(count($service->getCompatibleFieldTypes('email')) === 9, 'Email picker offers the nine approved text types');
echo 'Runtime: ' . PHP_VERSION . "\n";
