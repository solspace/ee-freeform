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
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;

class FormController extends Controller
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
                'id'          => ['type' => Table::COL_ID],
                'form'        => ['type' => 'html'],
                'submissions' => ['type' => Table::COL_TEXT],
                'manage'      => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $forms = FormRepository::getInstance()->getAllForms();
        $submissionTotals = SubmissionRepository::getInstance()->getSubmissionTotalsPerForm();

        $tableData = [];
        foreach ($forms as $form) {
            $tableData[] = [
                $form->id,
                $form->name,
                [
                    'content' => isset($submissionTotals[$form->id]) ? $submissionTotals[$form->id] : 0,
                    'href'    => ee('CP/URL', 'addons/settings/freeform_next/submissions/' . $form->handle),
                ],
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => ee('CP/URL', 'addons/settings/freeform_next/forms/' . $form->id),
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                [
                    'name'  => 'selections[]',
                    'value' => $form->id,
                    'data'  => [
                        'confirm' => lang('form') . ': <b>' . htmlentities("test", ENT_QUOTES) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $view = new CpView('form/listing', ['table' => $table->viewData()]);
        $view->setHeading(lang('Forms'));

        return $view;
    }

    /**
     * @param FormModel $form
     *
     * @return CpView
     */
    public function edit(FormModel $form)
    {
        $view = new CpView('form/edit');
        $view
            ->setHeading('Freeform')
            ->setSidebarDisabled(true)
            ->addJavascript('composer/vendors.js')
            ->addJavascript('composer/app.js')
            ->setTemplateVariables(
                [
                    'form'          => $form,
                    'fields'        => FieldRepository::getInstance()->getAllFields(false),
                    'notifications' => NotificationRepository::getInstance()->getAllNotifications(),
                    'statuses'      => StatusRepository::getInstance()->getAllStatuses(),
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
        $view = new AjaxView();
        $post = $_POST;

        if (!isset($post['formId'])) {
            throw new FreeformException('No form ID specified');
        }

        if (!isset($post['composerState'])) {
            throw new FreeformException('No composer data present');
        }

        $formId        = $post['formId'];
        $form          = FormRepository::getInstance()->getOrCreateForm($formId);
        $composerState = json_decode($post['composerState'], true);

        if ($this->getPost('duplicate', false)) {
            $oldHandle = $composerState['composer']['properties']['form']['handle'];

            if (preg_match('/^([a-zA-Z0-9]*[a-zA-Z]+)(\d+)$/', $oldHandle, $matches)) {
                list($string, $mainPart, $iterator) = $matches;

                $newHandle = $mainPart . ((int) $iterator + 1);
            } else {
                $newHandle = $oldHandle . '1';
            }

            $composerState['composer']['properties']['form']['handle'] = $newHandle;
        }

        $formsService = new FormsService();

        try {
            $formAttributes = new FormAttributes($formId, new EESession(), new EERequest());
            $composer       = new Composer(
                $composerState,
                $formAttributes,
                $formsService,
                new SubmissionsService(),
                new MailerService(),
                new FilesService(),
                new MailingListsService(),
                new CrmService(),
                new StatusesService(),
                new EETranslator()
            );
        } catch (ComposerException $exception) {
            $view->addError($exception->getMessage());

            return $view;
        }

        $form->setLayout($composer);
        $form->save();

        $view->addVariable('id', $form->id);
        $view->addVariable('handle', $form->handle);

        return $view;
    }
}
