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

        public function dbprefix(string $name): string { return $this->dbprefix . $name; }
        public function platform(): string { return 'sqlite'; }
        public function table_exists(string $name): bool { return isset($this->tables[$name]); }
        public function field_exists(string $name, string $table): bool { return isset($this->columns[$table][$name]); }
        public function select(string $field): self { return $this; }
        public function where(array|string $key, mixed $value = null): self
        {
            $this->criteria = \is_array($key) ? $key : [$key => $value];
            return $this;
        }
        public function get(string $table): self
        {
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
            foreach ($this->rows[$table] as &$row) {
                if ($this->matches($row)) {
                    $row = array_replace($row, $data);
                }
            }
            $this->criteria = [];
            return true;
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

    class TestFreeformUpdater extends \Freeform_next_upd
    {
        protected function getAddonInfo(): object
        {
            return new class {
                public function getModuleName(): string { return 'Freeform_next'; }
                public function getLowerName(): string { return 'freeform_next'; }
                public function getVersion(): string { return '4.0.0-alpha.1'; }
            };
        }
    }

    // A clean install creates the current schema, defaults, actions and hooks.
    $updater = new TestFreeformUpdater();
    $db = $GLOBALS['ee']->db;
    check($updater->install(), 'fresh install completes');
    check(\count($db->tables) === 17 && isset($db->tables['exp_freeform_next_permissions'], $db->tables['exp_freeform_next_submissions']), 'fresh install creates expected tables');
    check(\count($GLOBALS['seededFields']) === 12 && \count($db->rows['freeform_next_statuses']) === 3, 'fresh install seeds fields and statuses');
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
    check(!$db->queries && $content === array_intersect_key($db->rows, $content), '3.3.10 upgrade preserves content without schema writes');
    $hook = $db->rows['extensions'][0];
    check($hook['enabled'] === 'n' && $hook['settings'] === 'custom' && $hook['priority'] === 13 && $hook['version'] === '4.0.0-alpha.1', 'upgrade preserves disabled hook and its settings');
    check($db->rows['actions'][0]['csrf_exempt'] === false, 'upgrade enables EE CSRF validation for existing submission action');
    check($updater->update('3.3.10') && \count($db->rows['actions']) === 1 && \count($db->rows['extensions']) === 12, 'repeated upgrade does not duplicate registrations');

    // Older 3.3.x sites still need the 3.3.5 spam-folder column migration.
    $db->columns['exp_freeform_next_settings'] = [];
    check($updater->update('3.3.0') && $db->field_exists('spamFolderEnabled', 'exp_freeform_next_settings'), '3.3.0 receives missing spam-folder column');
    check($content === array_intersect_key($db->rows, $content), 'older 3.3.x upgrade also preserves content');

    class FailingUpdater extends TestFreeformUpdater
    {
        public function runMigrations(?string $previousVersion = null): bool { return false; }
    }
    $before = $db->rows;
    check(!(new FailingUpdater())->update('3.3.10') && $db->rows === $before, 'failed migration stops registration updates');
}
