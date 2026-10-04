<?php

// Run with `php scripts/check-permissions.php`; no EE installation required.
namespace Solspace\Addons\FreeformNext\Repositories {
    class PermissionsRepository
    {
        public static object $settings;
        public static function getInstance(): self { return new self(); }
        public function getOrCreate(): object { return self::$settings; }
    }
}

namespace Solspace\Addons\FreeformNext\Utilities {
    class ControlPanelView
    {
        protected function getLink(string $path): string { return $path; }
        protected function renderView($view): array { return ['view' => $view]; }
    }
}

namespace {
    error_reporting(E_ALL);
    set_error_handler(static function ($severity, $message, $file, $line) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });

    class FakeMember
    {
        public int $roleLoads = 0;
        public function __construct(private array $roles, private bool $admin = false) {}
        public function isSuperAdmin(): bool { return $this->admin; }
        public function getAllRoles(): array
        {
            ++$this->roleLoads;
            return array_map(static fn ($id) => (object) ['role_id' => $id], $this->roles);
        }
    }

    class FakeSession
    {
        public function __construct(public ?FakeMember $member) {}
        public function getMember(): ?FakeMember { return $this->member; }
    }

    $GLOBALS['session'] = new FakeSession(null);
    function ee(): object { return (object) ['session' => $GLOBALS['session']]; }

    require dirname(__DIR__) . '/src/freeform_next/Services/PermissionsService.php';
    require dirname(__DIR__) . '/src/freeform_next/Controllers/Controller.php';
    require dirname(__DIR__) . '/src/freeform_next/Utilities/ControlPanel/View.php';
    require dirname(__DIR__) . '/src/freeform_next/Utilities/ControlPanel/AjaxView.php';
    require dirname(__DIR__) . '/src/freeform_next/Controllers/ApiController.php';
    require dirname(__DIR__) . '/src/freeform_next/mcp.freeform_next.php';

    use Solspace\Addons\FreeformNext\Repositories\PermissionsRepository;
    use Solspace\Addons\FreeformNext\Services\PermissionsService;
    use Solspace\Addons\FreeformNext\Controllers\ApiController;
    use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;

    class TestApiController extends ApiController
    {
        protected function getPermissionsService(): PermissionsService { return new PermissionsService(); }
        public function fields(): AjaxView { return (new AjaxView())->addVariable('success', true); }
    }

    function check(bool $condition, string $label): void
    {
        if (!$condition) {
            throw new \RuntimeException($label);
        }
        echo "PASS: $label\n";
    }

    PermissionsRepository::$settings = (object) [
        'formsPermissions' => ['7'],
        'submissionsPermissions' => ['2'],
        'manageSubmissionsPermissions' => [9],
        'fieldsPermissions' => [],
        'exportPermissions' => [7],
    ];

    $member = new FakeMember([2, 7, 9]); // Primary, additional and role-group roles.
    $GLOBALS['session'] = new FakeSession($member);
    $permissions = new PermissionsService();
    check($permissions->canManageForms(), 'additional role grants access to forms');
    check($permissions->canManageSubmissions(), 'different assigned roles jointly grant submission management');
    check(!$permissions->canAccessFields(), 'unselected section remains denied');
    check(!$permissions->canUserAccessSection('unknown_section'), 'unknown sections fail closed');
    check($permissions->canUserSeeSectionInNavigation('export_profiles'), 'export profile navigation maps to export permission');
    check($permissions->canUserAccessSection('export_profiles'), 'export profile route maps to export permission');
    check($member->roleLoads === 1, 'effective roles are loaded once per service instance');

    $GLOBALS['session'] = new FakeSession(new FakeMember([2]));
    $permissions = new PermissionsService();
    check(!$permissions->canManageForms(), 'primary role alone does not inherit an additional role grant');
    check(!$permissions->canManageSubmissions(), 'manage submissions also requires the manage grant');
    check(!$permissions->canUserAccessSection('export_profiles'), 'export route denies a role without export permission');

    $GLOBALS['session'] = new FakeSession(new FakeMember([], true));
    $permissions = new PermissionsService();
    check($permissions->canAccessFields(), 'superadmin can access restricted sections');

    $GLOBALS['session'] = new FakeSession(null);
    $permissions = new PermissionsService();
    check(!$permissions->canManageForms() && !$permissions->canUserSeeSectionInNavigation('forms'), 'missing member cannot pass permission checks');

    $api = new TestApiController();
    check($api->handle('fields')->hasErrors(), 'guest cannot read API fields');
    check($api->handle('submission_export')->hasErrors(), 'guest cannot export submissions');

    $GLOBALS['session'] = new FakeSession(new FakeMember([7]));
    check(!$api->handle('fields')->hasErrors(), 'form editor can read fields for the builder');
    $_POST = ['label' => 'New field'];
    check($api->handle('fields')->hasErrors(), 'form editor cannot create fields without field permission');
    $_POST = [];
    check($api->handle('submission_export')->hasErrors(), 'export requires submission access too');

    $GLOBALS['session'] = new FakeSession(new FakeMember([9]));
    $cp = new \Freeform_next_mcp();
    check($cp->fields()['view'] instanceof \Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView, 'CP fields route denies a role without field access');
    check($cp->submissions()['view'] instanceof \Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView, 'CP submissions route denies before loading a form');
    check($cp->spam()['view'] instanceof \Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView, 'CP spam route denies before loading a form');

    check($cp->export_profiles()['view'] instanceof \Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView, 'CP export profile route denies a role without export access');
}
