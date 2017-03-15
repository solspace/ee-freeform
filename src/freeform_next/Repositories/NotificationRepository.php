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

use Solspace\Addons\FreeformNext\Model\NotificationModel;

class NotificationRepository extends Repository
{
    /**
     * @return NotificationRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return NotificationModel[]
     */
    public function getAllNotifications()
    {
        return ee('Model')
            ->get(NotificationModel::MODEL)
            ->all()
            ->asArray();
    }

    /**
     * @param int $id
     *
     * @return NotificationModel|null
     */
    public function getNotificationById($id)
    {
        return ee('Model')
            ->get(NotificationModel::MODEL)
            ->filter('id', $id)
            ->first();
    }

    /**
     * @param array $ids
     *
     * @return NotificationModel[]
     */
    public function getNotificationsByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(NotificationModel::MODEL)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }
}
