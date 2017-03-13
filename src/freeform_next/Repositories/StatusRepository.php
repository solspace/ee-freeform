<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
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
    public function getNotificationById($id)
    {
        return ee('Model')
            ->get(StatusModel::MODEL)
            ->filter('id', $id)
            ->first();
    }
}
