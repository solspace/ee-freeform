<?php

// Run with `php scripts/check-formatting-accessibility.php`; no EE installation required.
error_reporting(E_ALL);
set_error_handler(static function ($severity, $message) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity);
    }
    return false;
});

require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\EmailField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\RadioGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RatingField;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;

define('PATH_THIRD_THEMES', dirname(__DIR__) . '/src/themes/');

class AccessibleTestForm extends Form
{
    private CustomFormAttributes $attributes;

    public function __construct()
    {
        $this->attributes = new CustomFormAttributes();
    }

    public function getCustomAttributes(): CustomFormAttributes
    {
        return $this->attributes;
    }

    public function getTranslator(): TranslatorInterface
    {
        return new class implements TranslatorInterface {
            public function translate($string, array $variables = []): string
            {
                foreach ($variables as $name => $value) {
                    $string = str_replace('{' . $name . '}', $value, $string);
                }
                return $string ?? '';
            }
        };
    }

    public function getHash(): string
    {
        return 'accessibility-fixture';
    }
}

function checkA11y(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: $message\n";
}

function fixture(string $class, array $properties): AbstractField
{
    $field = new $class(new AccessibleTestForm());
    foreach ($properties as $name => $value) {
        (new ReflectionProperty($field, $name))->setValue($field, $value);
    }
    return $field;
}

function inspect(string $html): DOMXPath
{
    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML('<!doctype html><html><body>' . $html . '</body></html>');
    libxml_clear_errors();
    return new DOMXPath($document);
}

$options = [new Option('First', 'first'), new Option('Second', 'second')];
$shared = ['handle' => 'choice', 'label' => 'Choose one', 'instructions' => 'Pick an option', 'required' => true, 'options' => $options];

foreach ([CheckboxGroupField::class, RadioGroupField::class] as $class) {
    $field = fixture($class, $shared);
    $field->addErrors(['Choose an option']);
    $xpath = inspect($field->render(['labelClass' => 'ff-label', 'errorClass' => 'ff-errors']));
    checkA11y($xpath->query('//fieldset/legend[text()="Choose one"]')->length === 1, "$class has a named fieldset");
    checkA11y($xpath->query('//fieldset[@aria-required="true" and @aria-describedby="form-input-choice-instructions form-input-choice-errors"]')->length === 1, "$class describes its required group and error");
    checkA11y($xpath->query('//fieldset//label[@class="ff-option"]/input')->length === 2, "$class labels both choices");
    checkA11y($xpath->query('//fieldset//ul[@id="form-input-choice-errors"]')->length === 1, "$class exposes a linked error");
}

$checkbox = fixture(CheckboxField::class, ['handle' => 'consent', 'label' => 'I agree', 'instructions' => 'Required for signup', 'required' => true]);
$xpath = inspect($checkbox->render());
checkA11y($xpath->query('//label[input[@type="checkbox" and @aria-required="true" and @aria-describedby="form-input-consent-instructions"]][contains(.,"I agree")]')->length === 1, 'single checkbox has a clickable text label and linked instructions');

$text = fixture(TextField::class, ['handle' => 'name', 'label' => 'Your name', 'instructions' => 'Use your full name']);
$text->addErrors(['Name is required']);
$xpath = inspect($text->render());
checkA11y($xpath->query('//label[@for="form-input-name"]')->length === 1, 'text label targets its input');
checkA11y($xpath->query('//input[@id="form-input-name" and @aria-invalid="true" and @aria-describedby="form-input-name-instructions form-input-name-errors"]')->length === 1, 'text field exposes descriptions and invalid state');

$recipients = fixture(DynamicRecipientField::class, $shared + ['showAsRadio' => true]);
$xpath = inspect($recipients->render());
checkA11y($xpath->query('//fieldset/legend[text()="Choose one"]')->length === 1, 'dynamic recipient radios are grouped');
checkA11y($xpath->query('//fieldset//input[@type="radio"]')->length === 2, 'dynamic recipient options remain usable');

$email = fixture(EmailField::class, ['handle' => 'email', 'label' => 'Email', 'values' => ['a@example.com', 'b@example.com']]);
$xpath = inspect($email->render());
checkA11y($xpath->query('//input[@id="form-input-email"]')->length === 1, 'first email input has the explicit label target');
checkA11y($xpath->query('//input[@id="form-input-email-2" and @aria-label="Email 2"]')->length === 1, 'additional email inputs have distinct IDs and names');

$rating = fixture(RatingField::class, ['handle' => 'rating', 'label' => 'Rate the visit', 'maxValue' => 5, 'colorIdle' => '#767676', 'colorHover' => '#555555', 'colorSelected' => '#333333']);
$xpath = inspect($rating->render());
checkA11y($xpath->query('//fieldset/legend[text()="Rate the visit"]')->length === 1, 'rating radios share a named fieldset');
checkA11y($xpath->query('//input[@type="radio" and @id="form-input-rating-5"]/following-sibling::label[1][contains(.,"5 stars")]')->length === 1, 'rating stars have spoken labels');

$table = fixture(TableField::class, ['handle' => 'items', 'label' => 'Items', 'layout' => [
    ['type' => 'string', 'label' => 'Name'],
    ['type' => 'select', 'label' => 'Size', 'value' => 'Small;Large'],
    ['type' => 'checkbox', 'label' => 'Gift'],
]]);
$xpath = inspect($table->render());
checkA11y($xpath->query('//th[@scope="col" and @id="form-input-items-column-0"]')->length === 1, 'table columns expose header IDs');
checkA11y($xpath->query('//input[@type="text" and @aria-labelledby="form-input-items-column-0"]')->length === 1, 'table text cell names its column');
checkA11y($xpath->query('//select[@aria-labelledby="form-input-items-column-1"]')->length === 1, 'table select cell names its column');
checkA11y($xpath->query('//input[@type="checkbox" and @aria-labelledby="form-input-items-column-2"]')->length === 1, 'table checkbox cell names its column');
checkA11y($xpath->query('//button[@aria-label="Add row"]')->length === 1, 'table add action has a clear name');

$expected = ['tailwind-4-light', 'tailwind-4-dark', 'bootstrap-5-light', 'bootstrap-5-dark', 'bootstrap-5-floating-labels', 'flexbox', 'grid', 'basic-light', 'basic-dark', 'basic-floating-labels'];
$directory = dirname(__DIR__) . '/src/freeform_next/Templates/form/';
checkA11y(count(glob($directory . '*.html')) === count($expected), 'only ten templates appear in the chooser');
foreach ($expected as $name) {
    $sample = file_get_contents($directory . $name . '.html');
    checkA11y(str_contains($sample, '<nav aria-label="Form progress">') && str_contains($sample, 'aria-current="step"') && str_contains($sample, 'role="alert"') && str_contains($sample, '.ff-fieldset'), "$name includes progress, errors, and group styles");
}
