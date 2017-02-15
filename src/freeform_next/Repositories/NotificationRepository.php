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

class NotificationRepository extends Repository
{
    /**
     * @return NotificationRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    public function getAllNotifications()
    {
        ee('Model')
            ->get()
    }
}