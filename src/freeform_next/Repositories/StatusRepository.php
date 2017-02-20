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