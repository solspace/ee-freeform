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
 * @property text   $fieldsPermissions
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
    protected $fieldsPermissions;

    protected static $_typed_columns = [
        'formsPermissions'   => 'json',
        'fieldsPermissions'  => 'json',
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
                'formsPermissions'            => null,
                'fieldsPermissions'           => null,
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
}
