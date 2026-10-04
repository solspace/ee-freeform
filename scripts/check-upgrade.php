<?php

// Exercise the real Freeform installer/updater without an EE database.
namespace Solspace\Addons\FreeformNext\Repositories {
    class FieldRepository
    {
        public static function getInstance(): self { return new self(); }
        public function getOrCreateField(): object
        {
            return new class {
                public string $label;
                public string $handle;
                public string $type;
                public ?int $rows = null;
                public ?array $options = null;
                public function save(): void { $GLOBALS['seededFields'][] = $this->handle; }
            };
        }
    }
}

namespace {
    // EE's action dispatcher uses this marker to validate AJAX CSRF tokens.
    interface Strict_XID {}

    error_reporting(E_ALL);
    set_error_handler(static function ($severity, $message, $file, $line) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });

    class UpgradeDb
    {
        public string $dbprefix = 'exp_';
        public array $rows = [];
        public array $tables = [];
        public array $columns = [];
        public array $queries = [];
        private array $criteria = [];
        private ?string $selectedColumn = null;

        public function dbprefix(string $name): string { return $this->dbprefix . $name; }
        public function platform(): string { return 'sqlite'; }
        public function table_exists(string $name): bool { return isset($this->tables[$name]); }
        public function field_exists(string $name, string $table): bool { return isset($this->columns[$table][$name]); }
        public function select(string $field): self { $this->selectedColumn = $field; return $this; }
        public function where(array|string $key, mixed $value = null): self
        {
            $this->criteria = \is_array($key) ? $key : [$key => $value];
            return $this;
        }
        public function get(string $table): self
        {
            if ($table === 'exp_freeform_next_settings' && $this->selectedColumn !== null && !$this->field_exists($this->selectedColumn, $table)) {
                throw new \RuntimeException("Missing column {$this->selectedColumn} in {$table}");
            }
            $this->selectedColumn = null;
            $this->selected = array_values(array_filter(
                $this->rows[$table] ?? [],
                fn ($row) => $this->matches($row)
            ));
            $this->criteria = [];
            return $this;
        }
        private array $selected = [];
        public function row(): object|false { return $this->selected ? (object) $this->selected[0] : false; }
        public function insert(string $table, array $data): bool
        {
            $this->checkVersionLength($table, $data);
            if ($table === 'actions' || $table === 'extensions') {
                $column = $table === 'actions' ? 'action_id' : 'extension_id';
                $ids = array_column($this->rows[$table] ?? [], $column);
                $data[$column] = $ids ? max($ids) + 1 : 1;
            }
            $this->rows[$table][] = $data;
            return true;
        }
        public function update(string $table, array $data): bool
        {
            $this->checkVersionLength($table, $data);
            foreach ($this->rows[$table] as &$row) {
                if ($this->matches($row)) {
                    $row = array_replace($row, $data);
                }
            }
            $this->criteria = [];
            return true;
        }
        private function checkVersionLength(string $table, array $data): void
        {
            $column = $table === 'modules' ? 'module_version' : 'version';
            $limit = $table === 'extensions' ? 10 : 12;
            if (in_array($table, ['extensions', 'modules', 'exp_fieldtypes'], true)
                && isset($data[$column]) && strlen($data[$column]) > $limit) {
                throw new \RuntimeException("{$table}.{$column} exceeds EE's {$limit}-character limit");
            }
        }
        private function matches(array $row): bool
        {
            foreach ($this->criteria as $key => $value) {
                if (($row[$key] ?? null) !== $value) {
                    return false;
                }
            }
            return true;
        }
        public function query(string $sql): bool
        {
            $this->queries[] = $sql;
            if (preg_match('/^\s*CREATE TABLE IF NOT EXISTS `([^`]+)`/i', $sql, $match)) {
                $this->tables[$match[1]] = true;
                return true;
            }
            if (preg_match('/^\s*ALTER TABLE `([^`]+)` ADD COLUMN `spamFolderEnabled`/i', $sql, $match)) {
                $this->columns[$match[1]]['spamFolderEnabled'] = true;
                return true;
            }
            if (preg_match('/^\s*ALTER TABLE `([^`]+)` ADD COLUMN `(captchaProvider|turnstileKey|turnstileSecret|hcaptchaKey|hcaptchaSecret)`/i', $sql, $match)) {
                $this->columns[$match[1]][$match[2]] = true;
                return true;
            }
            if (preg_match('/^\s*ALTER TABLE `([^`]+)` ADD COLUMN `(turnstileTheme|turnstileSize|hcaptchaTheme|hcaptchaSize)`/i', $sql, $match)) {
                $this->columns[$match[1]][$match[2]] = true;
                return true;
            }
            if (preg_match('/^\s*ALTER TABLE `([^`]+)` ADD COLUMN `(recaptchaTheme|recaptchaSize)`/i', $sql, $match)) {
                $this->columns[$match[1]][$match[2]] = true;
                return true;
            }
            throw new \RuntimeException('Unexpected SQL: ' . substr($sql, 0, 100));
        }
    }

    $GLOBALS['ee'] = (object) ['db' => new UpgradeDb()];
    $GLOBALS['seededFields'] = [];
    function ee(): object { return $GLOBALS['ee']; }
    function check(bool $condition, string $label): void
    {
        if (!$condition) {
            throw new \RuntimeException($label);
        }
        echo "PASS: $label\n";
    }

    require dirname(__DIR__) . '/src/freeform_next/vendor/autoload.php';

    // EE7 stores extension versions in VARCHAR(10), and module and fieldtype
    // versions in VARCHAR(12). Both add-on setup values must fit the schema.
    $setup = file_get_contents(dirname(__DIR__) . '/src/freeform_next/addon.setup.php');
    preg_match_all("/'version'\\s*=>\\s*'([^']+)'/", $setup, $setupVersions);
    check(count($setupVersions[1]) === 2 && count(array_filter($setupVersions[1], static fn ($version) => strlen($version) > 10)) === 0,
        'EE add-on and fieldtype versions fit extension and fieldtype columns');
    check(version_compare($setupVersions[1][0], '4.0.0-alpha.5', '=='), 'short alpha version retains upgrade ordering');

    class TestFreeformUpdater extends \Freeform_next_upd
    {
        protected function getAddonInfo(): object
        {
            return new class {
                public function getModuleName(): string { return 'Freeform_next'; }
                public function getLowerName(): string { return 'freeform_next'; }
                public function getVersion(): string { return '4.0.0-a5'; }
            };
        }
    }

    // A clean install creates the current schema, defaults, actions and hooks.
    $updater = new TestFreeformUpdater();
    $db = $GLOBALS['ee']->db;
    check($updater->install(), 'fresh install completes');
    check(\count($db->tables) === 17 && isset($db->tables['exp_freeform_next_permissions'], $db->tables['exp_freeform_next_submissions']), 'fresh install creates expected tables');
    check(\count($GLOBALS['seededFields']) === 12 && \count($db->rows['freeform_next_statuses']) === 3, 'fresh install seeds fields and statuses');
    $settingsSql = implode("\n", array_filter($db->queries, static fn ($sql) => str_contains($sql, 'CREATE TABLE IF NOT EXISTS `exp_freeform_next_settings`')));
    foreach (['recaptchaTheme', 'recaptchaSize', 'captchaProvider', 'turnstileKey', 'turnstileSecret', 'turnstileTheme', 'turnstileSize',
        'hcaptchaKey', 'hcaptchaSecret', 'hcaptchaTheme', 'hcaptchaSize'] as $column) {
        check(str_contains($settingsSql, "`{$column}`"), "fresh install includes {$column}");
    }
    check(\count($db->rows['modules']) === 1 && \count($db->rows['actions']) === 1 && \count($db->rows['extensions']) === 12, 'fresh install registers module, action and hooks');
    check($db->rows['actions'][0]['csrf_exempt'] === false, 'submission action requires an EE CSRF token on fresh install');
    check(\in_array(Strict_XID::class, class_implements(\Freeform_Next::class), true), 'submission action also validates AJAX CSRF tokens');

    // Seed an existing 3.3.10 site with content and an administrator-disabled hook.
    $db = new UpgradeDb();
    $db->tables['exp_freeform_next_settings'] = true;
    $db->columns['exp_freeform_next_settings']['spamFolderEnabled'] = true;
    $db->rows = [
        'freeform_next_forms' => [['id' => 31, 'composerState' => '{"pages":["Contact"]}']],
        'freeform_next_submissions' => [['id' => 47, 'formId' => 31, 'field_8' => 'Saved']],
        'freeform_next_notifications' => [['id' => 3, 'name' => 'Admin']],
        'freeform_next_integrations' => [['id' => 9, 'name' => 'CRM']],
        'freeform_next_mailing_lists' => [['id' => 4, 'name' => 'Newsletter']],
        'freeform_next_permissions' => [['id' => 1, 'formsPermissions' => '[2,7]']],
        'actions' => [['action_id' => 1, 'method' => 'submitForm', 'class' => 'Freeform_next', 'csrf_exempt' => true]],
        'extensions' => [[
            'extension_id' => 2, 'class' => 'Freeform_next_ext', 'method' => 'addCpCustomMenu',
            'hook' => 'cp_custom_menu', 'settings' => 'custom', 'priority' => 13,
            'enabled' => 'n', 'version' => '3.3.10',
        ]],
    ];
    $GLOBALS['ee']->db = $db;
    $content = array_intersect_key($db->rows, array_flip([
        'freeform_next_forms', 'freeform_next_submissions', 'freeform_next_notifications',
        'freeform_next_integrations', 'freeform_next_mailing_lists', 'freeform_next_permissions',
    ]));
    check($updater->update('3.3.10'), '3.3.10 upgrades to 4');
    check(\count($db->queries) === 11 && $content === array_intersect_key($db->rows, $content), '3.3.10 upgrade adds CAPTCHA settings without changing content');
    check(\count(array_intersect(['recaptchaTheme', 'recaptchaSize', 'captchaProvider', 'turnstileKey', 'turnstileSecret', 'turnstileTheme', 'turnstileSize',
        'hcaptchaKey', 'hcaptchaSecret', 'hcaptchaTheme', 'hcaptchaSize'], array_keys($db->columns['exp_freeform_next_settings']))) === 11, 'upgrade creates all CAPTCHA provider columns');
    $hook = $db->rows['extensions'][0];
    check($hook['enabled'] === 'n' && $hook['settings'] === 'custom' && $hook['priority'] === 13 && $hook['version'] === '4.0.0-a5', 'upgrade preserves disabled hook and its settings');
    check($db->rows['actions'][0]['csrf_exempt'] === false, 'upgrade enables EE CSRF validation for existing submission action');
    check($updater->update('3.3.10') && \count($db->queries) === 11 && \count($db->rows['actions']) === 1 && \count($db->rows['extensions']) === 12, 'repeated upgrade does not duplicate registrations or columns');

    // Older 3.3.x sites still need the 3.3.5 spam-folder column migration.
    $db->columns['exp_freeform_next_settings'] = [];
    check($updater->update('3.3.0') && $db->field_exists('spamFolderEnabled', 'exp_freeform_next_settings'), '3.3.0 receives missing spam-folder column');
    check($content === array_intersect_key($db->rows, $content), 'older 3.3.x upgrade also preserves content');

    // alpha.2 installations may have the new model but none of its CAPTCHA columns.
    $db = new UpgradeDb();
    $db->tables['exp_freeform_next_settings'] = true;
    $db->columns['exp_freeform_next_settings'] = ['spamFolderEnabled' => true, 'turnstileKey' => true];
    $db->rows['exp_freeform_next_settings'] = [
        ['siteId' => 1, 'spamFolderEnabled' => 0],
        ['siteId' => 2, 'spamFolderEnabled' => 1],
    ];
    $db->rows['freeform_next_forms'] = [['id' => 31, 'name' => 'Contact']];
    $GLOBALS['ee']->db = $db;
    $GLOBALS['ee']->config = new class {
        public int $siteId = 1;
        public function item(string $key): int { return $this->siteId; }
    };
    $menuSettings = \Solspace\Addons\FreeformNext\Repositories\SettingsRepository::getInstance();
    check(!$menuSettings->isSpamFolderEnabledForMenu(), 'CP menu reads an older site setting without the new model columns');
    $GLOBALS['ee']->config->siteId = 2;
    check($menuSettings->isSpamFolderEnabledForMenu(), 'CP menu uses the current site setting');
    $GLOBALS['ee']->config->siteId = 1;
    check($updater->update('4.0.0-alpha.2'), 'alpha.2 upgrades to alpha.5');
    check(count($db->queries) === 10, 'alpha.2 adds its ten missing CAPTCHA columns');
    check(count(array_intersect(['captchaProvider', 'turnstileKey', 'turnstileSecret', 'hcaptchaKey', 'hcaptchaSecret'], array_keys($db->columns['exp_freeform_next_settings']))) === 5, 'alpha.2 receives the full CAPTCHA schema');
    check($db->rows['freeform_next_forms'] === [['id' => 31, 'name' => 'Contact']], 'alpha.2 upgrade preserves forms');
    check($updater->update('4.0.0-alpha.2') && count($db->queries) === 10, 'alpha.2 migration is safe to retry');

    // An installed alpha.3 already has keys; add only appearance settings.
    $db = new UpgradeDb();
    $db->tables['exp_freeform_next_settings'] = true;
    $db->columns['exp_freeform_next_settings'] = array_fill_keys(
        ['captchaProvider', 'turnstileKey', 'turnstileSecret', 'hcaptchaKey', 'hcaptchaSecret'], true
    );
    $db->rows['exp_freeform_next_settings'] = [['siteId' => 1, 'captchaProvider' => 'turnstile', 'turnstileKey' => 'existing-key']];
    $GLOBALS['ee']->db = $db;
    check($updater->update('4.0.0-a3') && count($db->queries) === 6, 'alpha.3 adds six appearance columns');
    check($db->rows['exp_freeform_next_settings'][0]['turnstileKey'] === 'existing-key', 'appearance upgrade preserves saved keys');
    check($updater->update('4.0.0-a3') && count($db->queries) === 6, 'appearance upgrade is safe to retry');

    // Sites already using Turnstile/hCaptcha appearance settings need only the
    // reCAPTCHA v2 columns; keep all existing provider settings intact.
    $db = new UpgradeDb();
    $db->tables['exp_freeform_next_settings'] = true;
    $db->columns['exp_freeform_next_settings'] = array_fill_keys(
        ['captchaProvider', 'turnstileKey', 'turnstileSecret', 'turnstileTheme', 'turnstileSize',
            'hcaptchaKey', 'hcaptchaSecret', 'hcaptchaTheme', 'hcaptchaSize'], true
    );
    $db->rows['exp_freeform_next_settings'] = [['siteId' => 1, 'turnstileTheme' => 'dark']];
    $GLOBALS['ee']->db = $db;
    check($updater->update('4.0.0-a4') && count($db->queries) === 2, 'alpha.4 adds only reCAPTCHA v2 appearance columns');
    check($db->rows['exp_freeform_next_settings'][0]['turnstileTheme'] === 'dark', 'reCAPTCHA upgrade preserves other provider settings');
    check($updater->update('4.0.0-a4') && count($db->queries) === 2, 'alpha.4 migration is safe to retry');

    class FailingUpdater extends TestFreeformUpdater
    {
        public function runMigrations(?string $previousVersion = null): bool { return false; }
    }
    $before = $db->rows;
    check(!(new FailingUpdater())->update('3.3.10') && $db->rows === $before, 'failed migration stops registration updates');
}
