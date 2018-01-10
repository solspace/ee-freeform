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

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\ExportProfileModel;

class ExportProfilesRepository extends Repository
{
    /** @var ExportProfileModel[] */
    private static $cache;

    /**
     * @return ExportProfilesRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return ExportProfileModel[]
     */
    public function getAllProfiles()
    {
        return ee('Model')
            ->get(ExportProfileModel::MODEL)
            ->all()
            ->asArray();
    }

    /**
     * @param int $id
     *
     * @return ExportProfileModel|null
     */
    public function getProfileById($id)
    {
        if (!isset(self::$cache[$id])) {
            self::$cache[$id] = ee('Model')
                ->get(ExportProfileModel::MODEL)
                ->filter('id', $id)
                ->first();
        }

        return self::$cache[$id];
    }

    /**
     * @param array $ids
     *
     * @return ExportProfileModel[]
     */
    public function getProfilesByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(ExportProfileModel::MODEL)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }
}
