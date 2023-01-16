<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 *
 * @property int    $id
 * @property int    $siteId
 * @property string $defaultPermissions
 * @property string $formsPermissions
 * @property string $submissionsPermissions
 * @property string $manageSubmissionsPermissions
 * @property string $fieldsPermissions
 * @property string $exportPermissions
 * @property string $notificationsPermissions
 * @property string $settingsPermissions
 * @property string $integrationsPermissions
 * @property string $resourcesPermissions
 * @property string $logsPermissions
 */
class PermissionsModel extends Model
{
    const MODEL = 'freeform_next:PermissionsModel';
    const TABLE = 'freeform_next_permissions';

    const DEFAULT_PERMISSIONS__ALLOW_ALL = 'allow_all';
    const DEFAULT_PERMISSIONS__DENY_ALL  = 'deny_all';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $defaultPermissions;
    protected $formsPermissions;
    protected $submissionsPermissions;
    protected $manageSubmissionsPermissions;
    protected $fieldsPermissions;
    protected $exportPermissions;
    protected $notificationsPermissions;
    protected $settingsPermissions;
    protected $integrationsPermissions;
    protected $resourcesPermissions;
    protected $logsPermissions;

    protected static $_typed_columns = [
        'formsPermissions'             => 'json',
        'submissionsPermissions'       => 'json',
        'manageSubmissionsPermissions' => 'json',
        'fieldsPermissions'            => 'json',
        'exportPermissions'            => 'json',
        'notificationsPermissions'     => 'json',
        'settingsPermissions'          => 'json',
        'integrationsPermissions'      => 'json',
        'resourcesPermissions'         => 'json',
        'logsPermissions'              => 'json',
    ];

    /**
     * Creates a Permissions Model
     *
     * @return PermissionsModel
     */
    public static function create()
    {
        /** @var PermissionsModel $settings */
        $settings = ee('Model')->make(
            self::MODEL,
            [
                'siteId'                       => ee()->config->item('site_id'),
                'defaultPermissions'           => self::DEFAULT_PERMISSIONS__ALLOW_ALL,
                'formsPermissions'             => self::getDefaultPermissions(),
                'submissionsPermissions'       => self::getDefaultPermissions(),
                'manageSubmissionsPermissions' => self::getDefaultPermissions(),
                'fieldsPermissions'            => self::getDefaultPermissions(),
                'exportPermissions'            => self::getDefaultPermissions(),
                'notificationsPermissions'     => self::getDefaultPermissions(),
                'settingsPermissions'          => null,
                'integrationsPermissions'      => self::getDefaultPermissions(),
                'resourcesPermissions'         => self::getDefaultPermissions(),
                'logsPermissions'              => self::getDefaultPermissions(),
            ]
        );

        return $settings;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return (int) $this->siteId;
    }

    /**
     * @return array
     */
    private static function getDefaultPermissions()
    {
        return [];
    }
}
