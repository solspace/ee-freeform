<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\PermissionsModel;
use Solspace\Addons\FreeformNext\Model\SettingsModel;

class PermissionsRepository extends Repository
{
    /** @var PermissionsModel[] */
    private static $cache;

    /**
     * @return PermissionsRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return PermissionsModel
     */
    public function getOrCreate()
    {
        $siteId = ee()->config->item('site_id');

        if (!isset(self::$cache[$siteId])) {
            /** @var SettingsModel $model */
            $model = ee('Model')
                ->get(PermissionsModel::MODEL)
                ->filter('siteId', $siteId)
                ->first();

            if (!$model) {
                $model = PermissionsModel::create();
            }

            self::$cache[$siteId] = $model;
        }

        return self::$cache[$siteId];
    }
}
