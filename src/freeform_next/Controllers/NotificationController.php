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
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;

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
                'name'   => ['type' => 'html'],
                'handle' => ['type' => 'html'],
                'manage' => ['type' => 'html'],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $notifications = NotificationRepository::getInstance()->getAllNotifications();

        $tableData = [];
        foreach ($notifications as $notification) {
            $tableData[] = [
                $notification->id,
                $notification->name,
                $notification->handle,
                0,
                [
                    'name'  => 'selections[]',
                    'value' => $notification->id,
                    'data'  => [
                        'confirm' => lang('notification') . ': <b>' . htmlentities("test", ENT_QUOTES) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $view = new CpView('notifications/listing', ['table' => $table->viewData()]);
        $view->setHeading(lang('Notifications'));

        return $view;
    }

    /**
     * @return AjaxView
     */
    public function create()
    {
        $view = new AjaxView();
        $notification = NotificationModel::create();
        $notification->name   = ee()->input->post('name', true);
        $notification->handle = ee()->input->post('handle', true);
        $notification->save();

        $view->addVariable('success', true);

        return $view;
    }

    /**
     * @param NotificationModel $notification
     *
     * @return CpView
     */
    public function edit(NotificationModel $notification)
    {
        $view = new CpView('form/edit');
        $view
            ->setHeading('Notification')
            ->setTemplateVariables(
                [
                    'notification' => $notification,
                ]
            );

        return $view;
    }

    /**
     * @return AjaxView
     * @throws \Exception
     */
    public function save()
    {
        $post = $_POST;



        return $view;
    }
}
