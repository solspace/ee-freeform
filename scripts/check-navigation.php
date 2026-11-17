<?php

// Exercise fresh-install menu placement without modifying a site's database.
error_reporting(E_ALL);
set_error_handler(static function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

class MenuRecord
{
    public function __construct(public string $model, public array $attributes = []) {}
    public function __get(string $name): mixed
    {
        if ($name === 'Items') {
            return ee('Model')->get('MenuItem')->filter('set_id', $this->getId())->all();
        }
        return $this->attributes[$name] ?? null;
    }
    public function __set(string $name, mixed $value): void { $this->attributes[$name] = $value; }
    public function getId(): int { return $this->attributes['id']; }
    public function save(): void
    {
        $this->attributes['id'] ??= count($GLOBALS['menus'][$this->model]) + 1;
        $GLOBALS['menus'][$this->model][$this->getId()] = $this;
    }
}

class MenuQuery
{
    private array $filters = [];
    public function __construct(private string $model, ?int $id = null)
    {
        if ($id !== null) { $this->filter('id', $id); }
    }
    public function filter(string $key, mixed $operator, mixed $value = null): self
    {
        $this->filters[] = [$key, $value === null ? '=' : $operator, $value ?? $operator];
        return $this;
    }
    public function all(): array
    {
        return array_values(array_filter($GLOBALS['menus'][$this->model], function ($row) {
            foreach ($this->filters as [$key, $operator, $value]) {
                if ($operator === 'IN' ? !in_array($row->$key, $value) : $row->$key != $value) {
                    return false;
                }
            }
            return true;
        }));
    }
    public function first(): ?MenuRecord { return $this->all()[0] ?? null; }
    public function count(): int { return count($this->all()); }
}

function ee(?string $service = null): object
{
    if ($service === null) { return $GLOBALS['ee']; }
    return new class {
        public function get(string $model, ?int $id = null): MenuQuery { return new MenuQuery($model, $id); }
        public function make(string $model, array $data): MenuRecord { return new MenuRecord($model, $data); }
    };
}
function check(bool $condition, string $label): void
{
    if (!$condition) { throw new RuntimeException($label); }
    echo "PASS: $label\n";
}
function resetMenus(int $role = 1): void
{
    $GLOBALS['menus'] = ['MenuSet' => [], 'MenuItem' => [], 'RoleSetting' => []];
    $GLOBALS['ee'] = (object) ['session' => new class($role) {
        public function __construct(private int $role) {}
        public function userdata(string $key): int { return $this->role; }
    }];
    foreach ([1, 5, 9] as $id) {
        ee('Model')->make('RoleSetting', ['role_id' => $id, 'menu_set_id' => 0])->save();
    }
}

require dirname(__DIR__) . '/src/freeform_next/Utilities/ControlPanel/Navigation/NavigationInstaller.php';
$installer = new Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationInstaller();

resetMenus();
$installer->install();
check(count($GLOBALS['menus']['MenuSet']) === 1 && count($GLOBALS['menus']['MenuItem']) === 1, 'empty installation gets a menu and Freeform shortcut');
$settings = array_values($GLOBALS['menus']['RoleSetting']);
check($settings[0]->menu_set_id === 1 && $settings[1]->menu_set_id === 0, 'default menu is assigned to Super Admins without changing other roles');
$installer->install();
check(count($GLOBALS['menus']['MenuItem']) === 1, 'repeated installation does not duplicate the shortcut');

resetMenus(5);
$set = ee('Model')->make('MenuSet', ['name' => 'Editorial']); $set->save();
$settings = array_values($GLOBALS['menus']['RoleSetting']);
$settings[0]->menu_set_id = $settings[1]->menu_set_id = $set->getId();
ee('Model')->make('MenuItem', ['set_id' => $set->getId(), 'type' => 'link', 'name' => 'Calendar', 'data' => 'addons/settings/calendar', 'sort' => 4])->save();
$installer->install();
$items = $set->Items;
check(count($items) === 2 && $items[0]->name === 'Calendar' && $items[1]->sort === 5, 'existing menu entries and ordering are preserved');
check(count($GLOBALS['menus']['MenuSet']) === 1, 'shared administrator menus get one shortcut');
check($items[1]->type === 'addon' && $items[1]->data === 'Freeform_next_ext', 'shortcut uses the permission-aware Freeform submenu');

foreach (['Freeform_next', 'Freeform_next_ext', 'addons/settings/freeform_next/forms'] as $destination) {
    resetMenus();
    $set = ee('Model')->make('MenuSet', ['name' => 'Existing']); $set->save();
    array_values($GLOBALS['menus']['RoleSetting'])[0]->menu_set_id = $set->getId();
    ee('Model')->make('MenuItem', ['set_id' => $set->getId(), 'type' => str_contains($destination, '/') ? 'link' : 'addon', 'data' => $destination, 'sort' => 1])->save();
    $installer->install();
    check(count($set->Items) === 1, "existing {$destination} shortcut is not duplicated");
}

resetMenus(5);
ee('Model')->make('MenuSet', ['name' => 'Freeform'])->save();
$installer->install();
check(count($GLOBALS['menus']['MenuSet']) === 2 && array_values($GLOBALS['menus']['MenuSet'])[1]->name === 'Freeform 2', 'unassigned administrator roles receive a uniquely named menu');
check(count($GLOBALS['menus']['MenuItem']) === 1 && array_values($GLOBALS['menus']['RoleSetting'])[1]->menu_set_id === 2, 'installing administrator also receives the shortcut');

resetMenus();
unset($GLOBALS['ee']->session);
$installer->install();
check(count($GLOBALS['menus']['MenuItem']) === 1, 'command-line installation defaults to Super Admins');
