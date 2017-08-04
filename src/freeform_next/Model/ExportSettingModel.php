<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Class Freeform_ExportProfileModel
 *
 * @property int   $id
 * @property int   $siteId
 * @property int   $userId
 * @property array $settings
 */
class ExportSettingModel extends Model
{
    const MODEL = 'freeform_next:ExportSettingModel';
    const TABLE = 'freeform_next_export_settings';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $userId;
    protected $settings;

    protected static $_typed_columns = [
        'settings' => 'json',
    ];

    /**
     * @param int $userId
     *
     * @return ExportSettingModel
     */
    public static function create($userId)
    {
        return ee('Model')->make(
            self::MODEL,
            [
                'siteId' => ee()->config->item('site_id'),
                'userId' => $userId,
            ]
        );
    }
}
