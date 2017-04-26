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

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\Validation\Result;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

class NotificationController extends Controller
{
    /**
     * @return CpView
     */
    public function index()
    {
        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $table->setColumns(
            [
                'id'     => ['type' => Table::COL_ID],
                'name'   => ['type' => Table::COL_TEXT],
                'handle' => ['type' => Table::COL_TEXT],
                'manage' => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $notifications = NotificationRepository::getInstance()->getAllNotifications();

        $tableData = [];
        foreach ($notifications as $notification) {
            $link       = null;
            $editButton = ['toolbar_items' => []];
            $checkboxes = ['name' => '', 'value' => '', 'disabled' => true];
            if (!$notification->isFileTemplate()) {
                $link       = $this->getLink('notifications/' . $notification->id);
                $editButton = [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => $link,
                            'title' => lang('edit'),
                        ],
                    ],
                ];
                $checkboxes = [
                    'name'  => 'id_list[]',
                    'value' => $notification->id,
                    'data'  => [
                        'confirm' => lang('Notification') . ': <b>' . htmlentities(
                                $notification->name,
                                ENT_QUOTES
                            ) . '</b>',
                    ],
                ];
            }

            $tableData[] = [
                $notification->id,
                [
                    'content' => $notification->name,
                    'href'    => $link,
                ],
                $notification->handle,
                $editButton,
                $checkboxes,
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $view = new CpView(
            'notifications/listing',
            [
                'table'            => $table->viewData(),
                'cp_page_title'    => lang('Notifications'),
                'form_right_links' => [
                    [
                        'title' => lang('New Notification'),
                        'link'  => $this->getLink('notifications/new'),
                    ],
                ],
            ]
        );
        $view->setHeading(lang('Notifications'));
        $view->addModal((new ConfirmRemoveModal($this->getLink('notifications/delete')))->setKind('Notifications'));

        return $view;
    }

    /**
     * @param string      $notificationId
     * @param Result|null $validation
     *
     * @return CpView
     * @throws FreeformException
     */
    public function edit($notificationId, Result $validation = null)
    {
        if (strtolower($notificationId) === 'new') {
            $notification = NotificationModel::create();
        } else {
            $notification = NotificationRepository::getInstance()->getNotificationById($notificationId);
        }

        if (!$notification) {
            throw new FreeformException("Notification doesn't exist");
        }

        $view = new CpView('notifications/edit');
        $view
            ->setHeading($notification->name ?: lang('New Notification'))
            ->addBreadcrumb(new NavigationLink('Notifications', 'notifications'))
            ->addJavascript('notifications')
            ->addJavascript('handleGenerator')
            ->setTemplateVariables(
                [
                    'errors'                => $validation,
                    'cp_page_title'         => 'Notification',
                    'base_url'              => $this->getLink('notifications/' . $notificationId),
                    'save_btn_text'         => 'Save',
                    'save_btn_text_working' => 'Saving',
                    'sections'              => [
                        [
                            [
                                'title'  => 'Name',
                                'desc'   => 'What this field will be called in the CP.',
                                'fields' => [
                                    'name' => [
                                        'type'     => 'text',
                                        'value'    => $notification->name,
                                        'required' => true,
                                        'attrs'    => 'data-generator-base',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Handle',
                                'desc'   => 'How you’ll refer to this notification template in the templates.',
                                'fields' => [
                                    'handle' => [
                                        'type'     => 'text',
                                        'value'    => $notification->handle,
                                        'required' => true,
                                        'attrs'    => 'data-generator-target',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Description',
                                'desc'   => 'Description of this notification.',
                                'fields' => [
                                    'description' => [
                                        'type'  => 'textarea',
                                        'value' => $notification->description,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Subject',
                                'desc'   => 'The subject line for the email notification.',
                                'fields' => [
                                    'subject' => [
                                        'type'     => 'text',
                                        'required' => true,
                                        'value'    => $notification->subject,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'From email',
                                'desc'   => 'The email address that the email will appear from in your email notification.',
                                'fields' => [
                                    'fromEmail' => [
                                        'type'     => 'text',
                                        'required' => true,
                                        'value'    => $notification->fromEmail,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'From Name',
                                'desc'   => 'The name that the email will appear from in your email notification.',
                                'fields' => [
                                    'fromName' => [
                                        'type'     => 'text',
                                        'required' => true,
                                        'value'    => $notification->fromName,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Reply-to Email',
                                'desc'   => 'The reply-to email address for your email notification. Leave blank to use \'From Email\' address.',
                                'fields' => [
                                    'replyToEmail' => [
                                        'type'  => 'text',
                                        'value' => $notification->replyToEmail,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Include attachments',
                                'desc'   => 'Include uploaded files as attachments in email notification.',
                                'fields' => [
                                    'includeAttachments' => [
                                        'type'  => 'yes_no',
                                        'value' => $notification->includeAttachments ? 'y' : 'n',
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Email Body',
                                'desc'   => 'The content of the email notification. See documentation for availability of variables.',
                                'wide'   => true,
                                'fields' => [
                                    'includeAttachments' => [
                                        'type'    => 'html',
                                        'content' => $this->getFieldHtml($notification, 'html_field'),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        return $view;
    }

    /**
     * @param $id
     *
     * @return NotificationModel
     */
    public function save($id)
    {
        $notification = NotificationRepository::getInstance()->getOrCreateNotification($id);

        $post        = $_POST;
        $validValues = [];
        foreach ($post as $key => $value) {
            if (property_exists($notification, $key)) {
                $validValues[$key] = $value;
            }
        }

        if (isset($validValues['includeAttachments'])) {
            $validValues['includeAttachments'] = $validValues['includeAttachments'] === 'y';
        }

        $notification->set($validValues);
        $notification->save();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('Success'))
            ->defer();

        return $notification;
    }

    /**
     * @return RedirectView
     */
    public function batchDelete()
    {
        if (isset($_POST['id_list'])) {
            $ids = [];
            foreach ($_POST['id_list'] as $id) {
                $ids[] = (int) $id;
            }

            $models = NotificationRepository::getInstance()->getNotificationsByIdList($ids);

            foreach ($models as $model) {
                $model->delete();
            }
        }

        return new RedirectView($this->getLink('notifications'));
    }

    /**
     * @param NotificationModel $model
     * @param string            $template
     *
     * @return string
     */
    private function getFieldHtml(NotificationModel $model, $template)
    {
        ob_start();
        include PATH_THIRD . "freeform_next/Templates/notifications/{$template}.php";

        return ob_get_clean();
    }
}
