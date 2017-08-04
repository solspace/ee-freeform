<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\ExportSettingModel;

class ExportSettingsRepository extends Repository
{
    /**
     * @return ExportSettingsRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param int $userId
     *
     * @return ExportSettingModel
     */
    public function getOrCreate($userId)
    {
        $model = ee('Model')
            ->get(ExportSettingModel::MODEL)
            ->filter('userId', $userId)
            ->filter('siteId', ee()->config->item('site_id'))
            ->first();

        if (!$model) {
            $model = ExportSettingModel::create($userId);
        }

        return $model;
    }
}
