<?php

// Run with `php scripts/check-cp-escaping.php`; no EE installation required.
error_reporting(E_ALL);
set_error_handler(static function ($severity, $message) {
    throw new ErrorException($message, 0, $severity);
});

define('CSRF_TOKEN', 'token"<script>');
define('URL_THIRD_THEMES', '/themes/');
define('APP_VER', '7.0.0');

function lang($key): string
{
    return (string) $key;
}

class FakeUrl
{
    public function __toString(): string { return '/cp/?q="</script><script>alert(1)</script>'; }
    public function make($path): string { return '/cp/' . $path; }
    public function getCurrentUrl(): self { return $this; }
    public function addQueryStringVariables($variables): self { return $this; }
    public function compile(): string { return '/cp/list?perpage=25&page=1'; }
}

class FakeAlert
{
    public function getAllInlines(): string { return ''; }
}

function ee($service, ...$args): object
{
    return $service === 'CP/URL' ? new FakeUrl() : new FakeAlert();
}

class FakeForm
{
    public function __construct(private string $name) {}
    public function getId(): int { return 1; }
    public function getName(): string { return $this->name; }
}

class FakeView
{
    public function extend($layout): void {}
    public function embed($template, $data): void {}
    public function startBlock($name): void { ob_start(); }
    public function endBlock(): void { ob_end_clean(); }
    public function render(string $viewFile, array $data): string
    {
        extract($data);
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
}

function check(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException($label);
    }
    echo "PASS: $label\n";
}

$view = new FakeView();
$root = dirname(__DIR__) . '/src/freeform_next/View/';
$hostile = '"<img src=x onerror=alert(1)>';
$form = new FakeForm($hostile);
$data = [
    'form' => $form,
    'forms' => [$form],
    'settings' => [['form' => $form, 'fields' => ['field' => ['label' => $hostile, 'checked' => true]]]],
    'selectedFormId' => 1,
    'sessionToken' => $hostile,
    'currentFormLabel' => $hostile,
    'formSwitches' => ['one' => ['url' => '/cp/?q=" onmouseover="alert(1)', 'label' => $hostile]],
    'currentSearchStatus' => $hostile,
    'currentSearchStatusId' => $hostile,
    'formStatuses' => [1 => ['url' => '/cp/status', 'label' => $hostile]],
    'currentDateRange' => '',
    'formDateRanges' => [],
    'currentDateRangeStart' => '',
    'currentDateRangeEnd' => '',
    'currentKeyword' => $hostile,
    'currentSearchOnField' => 'field',
    'columnLabels' => ['field' => $hostile],
    'visibleColumns' => ['field'],
    'mainUrl' => '/cp/list',
    'layout' => [],
    'perpage' => 25,
    'table' => [],
    'exportLink' => '/cp/export',
];

foreach (['submissions', 'spam'] as $section) {
    $html = $view->render($root . "$section/listing.php", $data);
    check(!str_contains($html, '<img src=x') && !str_contains($html, 'onmouseover="alert(1)'), "$section values cannot create markup or attributes");
    check(str_contains($html, '&lt;img src=x onerror=alert(1)&gt;'), "$section labels remain visible as text");
    check(str_contains($html, 'name="keywords"') && str_contains($html, 'value="&quot;&lt;img'), "$section keyword remains in the input");
}

$html = $view->render($root . '_layouts/table_form_wrapper.php', [
    'cp_page_title' => $hostile,
    'form_right_links' => [['title' => $hostile, 'link' => '/cp/?q=" onmouseover="alert(1)']],
    'child_view' => '<p>Intentional child markup</p>',
]);
check(!str_contains($html, '<img src=x') && !str_contains($html, 'onmouseover="alert(1)'), 'shared CP header escapes dynamic title and link');
check(str_contains($html, '<p>Intentional child markup</p>'), 'shared CP header preserves child view markup');

$html = $view->render($root . 'logs/log.php', [
    'content' => [['date' => new DateTime('2026-01-01'), 'level' => 'ERROR', 'category' => 'submission', 'message' => $hostile]],
]);
check(!str_contains($html, '<img src=x') && str_contains($html, '&lt;img src=x'), 'log messages render as text');

class FakeFreeformHelper
{
    public static function isExpressEdition(): bool { return false; }
}
class_alias(FakeFreeformHelper::class, 'Solspace\\Addons\\FreeformNext\\Library\\Helpers\\FreeformHelper');

$builderForm = new class {
    public function getId(): int { return 1; }
    public function getComposer(): object
    {
        return new class {
            public function getComposerStateJSON(int $flags): string
            {
                return json_encode(['label' => '</script><script>alert(1)</script>'], $flags);
            }
        };
    }
};
$html = $view->render($root . 'form/edit.php', [
    'form' => $builderForm,
    'fields' => [['name' => '</script><script>alert(1)</script>']],
    'fieldTypeList' => [], 'mailingLists' => [], 'crmIntegrations' => [],
    'notifications' => [], 'solspaceFormTemplates' => [], 'formTemplates' => [],
    'statuses' => [], 'assetSources' => [], 'fileKinds' => [], 'sourceTargets' => [],
    'generatedOptions' => [], 'channelFields' => [], 'categoryFields' => [], 'memberFields' => [],
    'showTutorial' => false, 'defaultTemplates' => false, 'isRecaptchaEnabled' => false,
    'isRecaptchaV3' => false, 'isDbEmailTemplateStorage' => false, 'isWidgetsInstalled' => false,
]);
check(substr_count($html, '</script>') === 2 && str_contains($html, '\u003C'), 'builder values cannot close their script block');

$field = new class($hostile) {
    public array $options;
    public string $value = '';
    public function __construct(string $hostile) { $this->options = [['label' => $hostile, 'value' => $hostile]]; }
    public function hasCustomOptionValues(): bool { return true; }
};
foreach (['custom_values', 'dynamic_recipients'] as $template) {
    $html = $view->render(dirname($root) . "/Templates/fields/$template.php", [
        'model' => $field, 'type' => 'select', 'singleValue' => true,
    ]);
    check(!str_contains($html, '<img src=x') && str_contains($html, 'value="&quot;&lt;img'), "$template options stay inside their inputs");
}

$html = $view->render(dirname($root) . '/Templates/notifications/listing.php', [
    'path' => $hostile, 'url' => '/cp/new', 'files' => [$hostile],
]);
check(!str_contains($html, '<img src=x') && str_contains($html, '&lt;img src=x'), 'template paths and filenames render as text');
