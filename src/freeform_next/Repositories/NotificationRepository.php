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
    /** @var NotificationModel[] */
    private static $notificationCache;
    private static $allNotificationsLoaded;

    /**
     * @return NotificationRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param int|null $id
     *
     * @return NotificationModel
     */
    public function getOrCreateNotification($id)
    {
        $notification = null;
        if ($id) {
            $notification = $this->getNotificationById($id);
        }

        if (!$notification) {
            $notification = NotificationModel::create();
        }

        return $notification;
    }

    /**
     * @param bool $indexById
     *
     * @return NotificationModel[]
     */
    public function getAllNotifications($indexById = true)
    {
        if (null === self::$notificationCache || !self::$allNotificationsLoaded) {
            $notificationRecords = ee('Model')
                ->get(NotificationModel::MODEL)
                ->order('name', 'asc')
                ->all()
                ->asArray();

            if (!$indexById) {
                $notificationRecords = array_values($notificationRecords);
            }

            self::$notificationCache = $notificationRecords;

            $settings = SettingsRepository::getInstance()->getOrCreate();
            foreach ($settings->listTemplatesInEmailTemplateDirectory() as $filePath => $name) {
                $model = NotificationModel::createFromTemplate($filePath);
                self::$notificationCache[$model->id] = $model;
            }

            self::$allNotificationsLoaded = true;
        }

        return self::$notificationCache;
    }

    /**
     * @param int $id
     *
     * @return NotificationModel|null
     */
    public function getNotificationById($id)
    {
        if (null === self::$notificationCache || !isset(self::$notificationCache[$id])) {
            if (is_numeric($id)) {
                $notification = ee('Model')
                    ->get(NotificationModel::MODEL)
                    ->filter('id', $id)
                    ->first();
            } else {
                $notification = ee('Model')
                    ->get(NotificationModel::MODEL)
                    ->filter('handle', $id)
                    ->first();
            }

            self::$notificationCache[$id] = null;

            if ($notification) {
                self::$notificationCache[$id] = $notification;
            } else {
                $settings = SettingsRepository::getInstance()->getOrCreate();

                foreach ($settings->listTemplatesInEmailTemplateDirectory() as $filePath => $name) {
                    if ($id === $name) {
                        $model = NotificationModel::createFromTemplate($filePath);
                        self::$notificationCache[$id] = $model;
                    }
                }
            }
        }

        return self::$notificationCache[$id];
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
