<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Database\StatusHandlerInterface;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Repositories\PermissionsRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

class PermissionsService
{
    /**
     * Check if user is allowed in the section
     *
     * @param string $method - NavigationLink's method
     * @param integer $groupId - EE Member group's id
     * @return bool
     */
    public function canUserAccessSection($method, $groupId)
    {
        $settings = PermissionsRepository::getInstance()->getOrCreate();
        $propertyName = $method . 'Permissions';

        if (!property_exists($settings, $propertyName)) {
            return true;
        }

        $permissions = $settings->{$propertyName};

        if (!$permissions) {
            return false;
        }

        if (!in_array($groupId, $permissions)) {
            return false;
        }

        return true;
    }
}
