<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\SettingsModel;

class SettingsRepository extends Repository
{
    /** @var SettingsModel[] */
    private static $cache;

    /**
     * @return SettingsRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return SettingsModel
     */
    public function getOrCreate()
    {
        $siteId = ee()->config->item('site_id');

        if (!isset(self::$cache[$siteId])) {
            /** @var SettingsModel $model */
            $model = ee('Model')
                ->get(SettingsModel::MODEL)
                ->filter('siteId', $siteId)
                ->first();

            if (!$model) {
                $model = SettingsModel::create();
            }

            self::$cache[$siteId] = $model;
        }

        return self::$cache[$siteId];
    }
}
