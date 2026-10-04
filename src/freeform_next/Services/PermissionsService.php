<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Repositories\PermissionsRepository;

class PermissionsService
{
    private ?array $currentRoleIds = null;
    private bool $isSuperAdmin = false;

    public const PERMISSION__MANAGE_FORMS         = 'forms';
    public const PERMISSION__ACCESS_SUBMISSIONS   = 'submissions';
    public const PERMISSION__MANAGE_SUBMISSIONS   = 'manageSubmissions';
    public const PERMISSION__ACCESS_FIELDS        = 'fields';
    public const PERMISSION__ACCESS_EXPORT        = 'export';
    public const PERMISSION__ACCESS_NOTIFICATIONS = 'notifications';
    public const PERMISSION__ACCESS_SETTINGS      = 'settings';
    public const PERMISSION__ACCESS_INTEGRATIONS  = 'integrations';
    public const PERMISSION__ACCESS_RESOURCES     = 'resources';
    public const PERMISSION__ACCESS_LOGS          = 'logs';

    public const PERMISSION__ACCESS_SETTINGS__LICENSE             = 'settings/license';
    public const PERMISSION__ACCESS_SETTINGS__GENERAL             = 'settings/general';
    public const PERMISSION__ACCESS_SETTINGS__PERMISSIONS         = 'settings/permissions';
    public const PERMISSION__ACCESS_SETTINGS__FORMATING_TEMPLATES = 'settings/formatting_templates';
    public const PERMISSION__ACCESS_SETTINGS__EMAIL_TEMPLATES     = 'settings/email_templates';
    public const PERMISSION__ACCESS_SETTINGS__STATUSES            = 'settings/statuses';
    public const PERMISSION__ACCESS_SETTINGS__DEMO_TEMPLATES      = 'settings/demo_templates';

    /**
     * Check if user is allowed in the section
     *
     * @param string $method NavigationLink's method
     *
     * @return bool
     */
    public function canUserAccessSection(string $method): bool
    {
        $roleIds = $this->getCurrentRoleIds();
        if ($this->isSuperAdmin) {
            return true;
        }

        if (!$roleIds) {
            return false;
        }

        $method = $this->getMethodTransformation()[$method] ?? $method;
        $settings     = PermissionsRepository::getInstance()->getOrCreate();
        $propertyName = $method . 'Permissions';

        if (!property_exists($settings, $propertyName)) {
            return false;
        }

        $permissions = array_map('intval', $settings->{$propertyName} ?: []);

        return (bool) array_intersect($roleIds, $permissions);
    }

    /**
     * @param $method
     * @return bool
     */
    public function canUserSeeSectionInNavigation($method): bool
    {
        $roleIds = $this->getCurrentRoleIds();
        if ($this->isSuperAdmin) {
            return true;
        }

        if (!$roleIds) {
            return false;
        }

        // Some method names have to be translated
        if (array_key_exists($method, $this->getMethodTransformation())) {
            $method = $this->getMethodTransformation()[$method];
        }

        // Only some methods can be hidden in the menu
        if (!in_array($method, $this->getRestrictedNavigationSections(), false)) {
            return true;
        }

        return $this->canUserAccessSection($method);
    }

    /**
     * @return bool
     */
    public function canManageForms(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__MANAGE_FORMS);
    }

    /**
     * @return bool
     */
    public function canAccessSubmissions(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_SUBMISSIONS);
    }

    /**
     * @return bool
     */
    public function canManageSubmissions(): bool
    {
        if (!$this->canAccessSubmissions()) {
            return false;
        }

        return $this->canUserAccessSection(self::PERMISSION__MANAGE_SUBMISSIONS);
    }

    /**
     * @return bool
     */
    public function canAccessFields(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_FIELDS);
    }

    /**
     * @return bool
     */
    public function canAccessExport(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_EXPORT);
    }

    /**
     * @return bool
     */
    public function canAccessNotifications(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_NOTIFICATIONS);
    }

    /**
     * @return bool
     */
    public function canAccessSettings(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_SETTINGS);
    }

    /**
     * @return bool
     */
    public function canAccessIntegrations(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_INTEGRATIONS);
    }

    /**
     * @return bool
     */
    public function canAccessResources(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_RESOURCES);
    }

    /**
     * @return bool
     */
    public function canAccessLogs(): bool
    {
        return $this->canUserAccessSection(self::PERMISSION__ACCESS_LOGS);
    }

    /** @return int[] */
    private function getCurrentRoleIds(): array
    {
        if ($this->currentRoleIds !== null) {
            return $this->currentRoleIds;
        }

        $this->currentRoleIds = [];
        $member = ee()->session->getMember();
        if (!$member) {
            return $this->currentRoleIds;
        }

        $this->isSuperAdmin = $member->isSuperAdmin();
        foreach ($member->getAllRoles() as $role) {
            $roleId = (int) $role->role_id;
            if ($roleId > 0) {
                $this->currentRoleIds[$roleId] = $roleId;
            }
        }

        $this->currentRoleIds = array_values($this->currentRoleIds);

        return $this->currentRoleIds;
    }

    /**
     * @return array
     */
    private function getMethodTransformation(): array
    {
        return [
            'export_profiles' => 'export',
        ];
    }

    /**
     * @return array
     */
    private function getRestrictedNavigationSections(): array
    {
        return [
            self::PERMISSION__ACCESS_SUBMISSIONS,
            self::PERMISSION__ACCESS_FIELDS,
            self::PERMISSION__ACCESS_EXPORT,
            self::PERMISSION__ACCESS_NOTIFICATIONS,
            self::PERMISSION__ACCESS_SETTINGS,
            self::PERMISSION__ACCESS_RESOURCES,
            self::PERMISSION__ACCESS_INTEGRATIONS,
            self::PERMISSION__ACCESS_LOGS,
        ];
    }
}
