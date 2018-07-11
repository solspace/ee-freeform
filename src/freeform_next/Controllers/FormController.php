<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Repositories\CrmRepository;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FileRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

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
                'id'                 => ['type' => Table::COL_ID],
                'Form'               => ['type' => Table::COL_TEXT],
                'Handle'             => ['type' => Table::COL_TEXT],
                'Submissions'        => ['type' => Table::COL_TEXT],
                'blocked_spam_count' => ['type' => Table::COL_TEXT],
                'manage'             => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $forms            = FormRepository::getInstance()->getAllForms();
        $submissionTotals = SubmissionRepository::getInstance()->getSubmissionTotalsPerForm();

        $tableData = [];
        foreach ($forms as $form) {
            $tableData[] = [
                $form->id,
                [
                    'content' => $form->name,
                    'href'    => $this->getLink('forms/' . $form->id),
                ],
                $form->handle,
                [
                    'content' => isset($submissionTotals[$form->id]) ? $submissionTotals[$form->id] : 0,
                    'href'    => $this->getLink('submissions/' . $form->handle),
                ],
                $form->spamBlockCount,
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => $this->getLink('forms/' . $form->id),
                            'title' => lang('edit'),
                        ],
                        'sync' => [
                            'href'                 => 'javascript:;',
                            'class'                => 'reset-spam-count',
                            'title'                => lang('Reset Spam Count'),
                            'data-csrf'            => CSRF_TOKEN,
                            'data-url'             => $this->getLink('api/reset_spam'),
                            'data-form-id'         => $form->id,
                            'data-confirm-message' => sprintf(
                                lang('Are you sure you want to reset the spam count for %s to 0?'),
                                $form->name
                            ),
                        ],
                    ],
                ],
                [
                    'name'  => 'id_list[]',
                    'value' => $form->id,
                    'data'  => [
                        'confirm' => lang('Form') . ': <b>' . htmlentities(
                                $form->getForm()->getName(),
                                ENT_QUOTES
                            ) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $view = new CpView(
            'form/listing',
            [
                'table'            => $table->viewData(),
                'cp_page_title'    => lang('Forms'),
                'form_right_links' => FreeformHelper::get('right_links', $this),
            ]
        );

        $view
            ->setHeading(lang('Forms'))
            ->addJavascript('formIndex')
            ->addModal((new ConfirmRemoveModal($this->getLink('forms/delete')))->setKind('Forms'));

        return $view;
    }

    /**
     * @param FormModel $form
     *
     * @return CpView
     */
    public function edit(FormModel $form)
    {
        $fileService     = new FilesService();
        $settingsService = new SettingsService();

        $view = new CpView('form/edit');
        $view
            ->setHeading($form->name ?: 'New Form')
            ->setSidebarDisabled(true)
            ->addJavascript('composer/vendors.js')
            ->addJavascript('composer/app.js')
            ->addBreadcrumb(new NavigationLink('Forms', 'forms'))
            ->setTemplateVariables(
                [
                    'form'                     => $form,
                    'fields'                   => FieldRepository::getInstance()->getAllFields(false),
                    'notifications'            => NotificationRepository::getInstance()->getAllNotifications(),
                    'statuses'                 => StatusRepository::getInstance()->getAllStatuses(),
                    'assetSources'             => FileRepository::getInstance()->getAllAssetSources(),
                    'fileKinds'                => $fileService->getFileKinds(),
                    'fieldTypeList'            => $this->getFieldsService()->getFieldTypes(),
                    'formTemplates'            => $settingsService->getCustomFormTemplates(),
                    'solspaceFormTemplates'    => $settingsService->getSolspaceFormTemplates(),
                    'defaultTemplates'         => $settingsService->isDefaultTemplates(),
                    'showTutorial'             => $settingsService->getSettingsModel()->isShowTutorial(),
                    'mailingLists'             => MailingListRepository::getInstance()->getAllIntegrationObjects(),
                    'crmIntegrations'          => CrmRepository::getInstance()->getAllIntegrationObjects(),
                    'isDbEmailTemplateStorage' => $settingsService
                        ->getSettingsModel()
                        ->isDbEmailTemplateStorage(),
                    'isWidgetsInstalled'       => false,
                    'sourceTargets'            => $this->getSourceTargetsList(),
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

        $isNew = !$form->id;

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
            $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();

            $formAttributes = new FormAttributes($formId, $sessionImplementation, new EERequest());
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

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_SAVE, $form, $isNew)) {
            $view->addError(ExtensionHelper::getLastCallData());

            return $view;
        }

        $existing = FormRepository::getInstance()->getFormByIdOrHandle($form->handle);
        if ($existing && $existing->id !== $form->id) {
            $view->addError(sprintf('Handle "%s" already taken', $form->handle));
        } else {
            try {
                $form->save();

                if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_AFTER_SAVE, $form, $isNew)) {
                    return $view;
                }

                $view->addVariable('id', $form->id);
                $view->addVariable('handle', $form->handle);
            } catch (\Exception $e) {
                $view->addError($e->getMessage());
            }
        }

        return $view;
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

            $models = FormRepository::getInstance()->getFormByIdList($ids);

            foreach ($models as $model) {

                if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_FORM_AFTER_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink(''));
    }

    /**
     * @return array
     */
    private function getSourceTargetsList()
    {
        $entryTypes = (new Query())
            ->select(['id', 'sectionId', 'name', 'hasTitleField', 'fieldLayoutId'])
            ->from('{{%entrytypes}}')
            ->orderBy(['sectionId' => SORT_ASC, 'sortOrder' => SORT_ASC])
            ->all();

        $fieldLayoutFields = (new Query())
            ->select(['fieldId', 'layoutId'])
            ->from('{{%fieldlayoutfields}}')
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();

        $fieldByLayoutGroupId = [];
        foreach ($fieldLayoutFields as $field) {
            $layoutId = $field['layoutId'];

            if (!isset($fieldByLayoutGroupId[$layoutId])) {
                $fieldByLayoutGroupId[$layoutId] = [];
            }

            $fieldByLayoutGroupId[$layoutId][] = (int) $field['fieldId'];
        }

        $entryTypesBySectionId = [];
        foreach ($entryTypes as $entryType) {
            $fieldLayoutId = $entryType['fieldLayoutId'];
            $fieldIds      = [];
            if (isset($fieldByLayoutGroupId[$fieldLayoutId])) {
                $fieldIds = $fieldByLayoutGroupId[$fieldLayoutId];
            }

            $entryTypesBySectionId[$entryType['sectionId']][] = [
                'key'                 => $entryType['id'],
                'value'               => $entryType['name'],
                'hasTitleField'       => (bool) $entryType['hasTitleField'],
                'fieldLayoutFieldIds' => $fieldIds,
            ];
        }
        $sections    = \Craft::$app->sections->getAllSections();
        $sectionList = [0 => ['key' => '', 'value' => Freeform::t('All Sections')]];

        foreach ($sections as $group) {
            $sectionList[] = [
                'key'        => $group->id,
                'value'      => $group->name,
                'entryTypes' => $entryTypesBySectionId[$group->id] ?? [],
            ];
        }

        $categories   = \Craft::$app->categories->getAllGroups();
        $categoryList = [0 => ['key' => '', 'value' => Freeform::t('All Category Groups')]];
        foreach ($categories as $group) {
            $categoryList[] = [
                'key'   => $group->id,
                'value' => $group->name,
            ];
        }

        $tags    = \Craft::$app->tags->getAllTagGroups();
        $tagList = [0 => ['key' => '', 'value' => Freeform::t('All Tag Groups')]];
        foreach ($tags as $group) {
            $tagList[] = [
                'key'   => $group->id,
                'value' => $group->name,
            ];
        }

        $userFieldLayoutId = (int) (new Query())
            ->select('id')
            ->from('fieldlayouts')
            ->where(['type' => User::class])
            ->scalar();
        $userGroups        = \Craft::$app->userGroups->getAllGroups();
        $userList          = [0 => ['key' => '', 'value' => Freeform::t('All User Groups')]];
        foreach ($userGroups as $group) {
            $fieldIds = [];
            if (isset($fieldByLayoutGroupId[$userFieldLayoutId])) {
                $fieldIds = $fieldByLayoutGroupId[$userFieldLayoutId];
            }

            $userList[] = [
                'key'                 => $group->id,
                'value'               => $group->name,
                'fieldLayoutFieldIds' => $fieldIds,
            ];
        }

        return [
            ExternalOptionsInterface::SOURCE_ENTRIES    => $sectionList,
            ExternalOptionsInterface::SOURCE_CATEGORIES => $categoryList,
            ExternalOptionsInterface::SOURCE_TAGS       => $tagList,
            ExternalOptionsInterface::SOURCE_USERS      => $userList,
        ];
    }
}
