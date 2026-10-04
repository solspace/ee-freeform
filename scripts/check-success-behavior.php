<?php

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FormProperties;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

define('URL_THIRD_THEMES', '/themes/third_party/');
require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

function checkSuccess(bool $result, string $message): void
{
    if (!$result) {
        throw new RuntimeException($message);
    }

    echo "PASS: $message\n";
}

$translator = new class implements TranslatorInterface {
    public function translate($string, array $variables = []): string
    {
        return $string;
    }
};

$legacy = new FormProperties([], $translator);
checkSuccess($legacy->getSuccessBehavior() === 'returnUrl', 'existing forms keep redirect behavior');
checkSuccess($legacy->getErrorMessage() === FormProperties::DEFAULT_ERROR_MESSAGE, 'existing forms get a default error heading');

$settings = new FormProperties([
    'successBehavior' => 'message',
    'successMessage' => '<script>alert(1)</script>',
    'errorMessage' => 'Please review this form.',
], $translator);
checkSuccess($settings->getSuccessBehavior() === 'message' && $settings->getErrorMessage() === 'Please review this form.', 'custom feedback settings are accepted');

class SuccessFormFixture extends Form
{
    public function shouldDisplaySuccessMessage(): bool
    {
        return true;
    }

    public function renderTag(?array $attributes = null): string
    {
        return '<form>';
    }

    public function renderClosingTag(): string
    {
        return '</form>';
    }
}

class SuccessTransformerFixture extends FormToTagDataTransformer
{
    protected function parseContent(): string|array|null
    {
        return '<input value="">';
    }
}

$form = (new ReflectionClass(SuccessFormFixture::class))->newInstanceWithoutConstructor();
(new ReflectionProperty(Form::class, 'formTemplate'))->setValue($form, 'basic-dark.html');
(new ReflectionProperty(Form::class, 'successMessage'))->setValue($form, $settings->getSuccessMessage());
$banner = $form->renderSuccessBanner();
checkSuccess(str_contains($banner, 'freeform-success-banner--dark') && str_contains($banner, 'form-feedback.css'), 'dark success banner loads its styles');
checkSuccess(str_contains($banner, '&lt;script&gt;') && !str_contains($banner, '<script>'), 'configured success message is escaped');
checkSuccess(str_contains($form->renderSuccessBanner('basic-light.html'), 'class="freeform-success-banner"'), 'preview template determines banner theme');

$transformer = new SuccessTransformerFixture($form, '{rows}');
checkSuccess($transformer->getOutput() === $banner . '<form><input value=""></form>', 'success banner precedes a fresh form');
checkSuccess($transformer->getOutputWithoutWrappingFormTags() === $banner . '<input value="">', 'success banner works without generated form tags');
$returnUrlTransformer = new SuccessTransformerFixture($form, '/thank-you', false, false);
checkSuccess($returnUrlTransformer->getOutputWithoutWrappingFormTags() === '<input value="">', 'return URL interpolation never includes a feedback banner');
