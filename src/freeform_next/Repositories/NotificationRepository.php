<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Exceptions\DataObjects\EmailTemplateException;
use Solspace\Addons\FreeformNext\Library\Logging\EELogger;
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
                try {
                    $model = NotificationModel::createFromTemplate($filePath);
                    self::$notificationCache[$model->id] = $model;
                } catch (EmailTemplateException $exception) {
					(new EELogger())->error($exception->getMessage(), 'FreeformNotifications');
                }
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
     * @param int $id
     *
     * @return NotificationModel|null
     */
    public function getNotificationByLegacyId($id)
    {
        if (null === self::$notificationCache || !isset(self::$notificationCache[$id])) {
            if (is_numeric($id)) {
                $notification = ee('Model')
                    ->get(NotificationModel::MODEL)
                    ->filter('legacyId', $id)
                    ->first();
            } else {
                $notification = ee('Model')
                    ->get(NotificationModel::MODEL)
                    ->filter('legacyId', $id)
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
