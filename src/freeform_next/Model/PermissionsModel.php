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

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 *
 * @property int    $id
 * @property int    $siteId
 * @property string   $defaultPermissions
 * @property text   $formsPermissions
 * @property text   $submissionsPermissions
 * @property text   $manageSubmissionsPermissions
 * @property text   $fieldsPermissions
 * @property text   $exportPermissions
 * @property text   $notificationsPermissions
 * @property text   $settingsPermissions
 * @property text   $integrationsPermissions
 * @property text   $resourcesPermissions
 * @property text   $logsPermissions
 */
class PermissionsModel extends Model
{
    const MODEL = 'freeform_next:PermissionsModel';
    const TABLE = 'freeform_next_permissions';

    const DEFAULT_PERMISSIONS__ALLOW_ALL = 'allow_all';
    const DEFAULT_PERMISSIONS__DENY_ALL = 'deny_all';

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
        'formsPermissions'   => 'json',
        'submissionsPermissions'   => 'json',
        'manageSubmissionsPermissions'   => 'json',
        'fieldsPermissions'  => 'json',
        'exportPermissions'  => 'json',
        'notificationsPermissions'  => 'json',
        'settingsPermissions'  => 'json',
        'integrationsPermissions'  => 'json',
        'resourcesPermissions'  => 'json',
        'logsPermissions'  => 'json',
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
                'siteId'                      => ee()->config->item('site_id'),
                'defaultPermissions'          => self::DEFAULT_PERMISSIONS__ALLOW_ALL,
                'formsPermissions'            => self::getDefaultPermissions(),
                'submissionsPermissions'      => self::getDefaultPermissions(),
                'manageSubmissionsPermissions'=> self::getDefaultPermissions(),
                'fieldsPermissions'           => self::getDefaultPermissions(),
                'exportPermissions'           => self::getDefaultPermissions(),
                'notificationsPermissions'    => self::getDefaultPermissions(),
                'settingsPermissions'         => null,
                'integrationsPermissions'     => self::getDefaultPermissions(),
                'resourcesPermissions'        => self::getDefaultPermissions(),
                'logsPermissions'        => self::getDefaultPermissions(),
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

    // Default permission settings is an array with super admin group id
    private static function getDefaultPermissions()
    {
        return [
            1,
        ];
    }
}
