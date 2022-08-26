<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\StatusModel;

class StatusRepository extends Repository
{
    /**
     * @return StatusRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return StatusModel[]
     */
    public function getAllStatuses()
    {
        return ee('Model')
            ->get(StatusModel::MODEL)
            ->all()
            ->asArray();
    }

    /**
     * @return array
     */
    public function getStatusNamesById()
    {
        $names = [];
        foreach ($this->getAllStatuses() as $status) {
            $names[$status->id] = $status->name;
        }

        return $names;
    }

    /**
     * @return array
     */
    public function getColorsById()
    {
        $colors = [];
        foreach ($this->getAllStatuses() as $status) {
            $colors[$status->id] = $status->color;
        }

        return $colors;
    }

    /**
     * @return StatusModel
     */
    public function getDefaultStatus()
    {
        $defaultStatus = ee('Model')
            ->get(StatusModel::MODEL)
            ->filter('isDefault', true)
            ->filter('siteId', ee()->config->item('site_id'))
            ->first();

        if (!$defaultStatus) {
            $statuses      = $this->getAllStatuses();
            $defaultStatus = reset($statuses);

            if (!$defaultStatus) {
                $defaultStatus            = StatusModel::create();
                $defaultStatus->color     = 'gray';
                $defaultStatus->name      = 'Open';
                $defaultStatus->handle    = 'open';
                $defaultStatus->isDefault = true;
                $defaultStatus->save();
            }
        }

        return $defaultStatus;
    }

    /**
     * @return int
     */
    public function getDefaultStatusId()
    {
        return $this->getDefaultStatus()->id;
    }

    /**
     * @param int $id
     *
     * @return StatusModel|null
     */
    public function getStatusById($id)
    {
        return ee('Model')
            ->get(StatusModel::MODEL)
            ->filter('id', $id)
            ->first();
    }

    /**
     * @param $handle
     * @return mixed
     */
    public function getStatusByHandle($handle)
    {
        return ee('Model')
            ->get(StatusModel::MODEL)
            ->filter('handle', $handle)
            ->first();
    }

    /**
     * @param array $ids
     *
     * @return StatusModel[]
     */
    public function getStatusesByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(StatusModel::MODEL)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }
}
