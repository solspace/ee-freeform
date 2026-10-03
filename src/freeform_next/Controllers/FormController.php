<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Controllers;

use Exception;
use stdClass;
use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ExternalOptionsInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
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
use Solspace\Addons\FreeformNext\Services\FieldsService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\PermissionsService;
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
    public function index(): CpView
    {
        $canManageForms = $this->getPermissionsService()->canManageForms();
        $canAccessSubmissions = $this->getPermissionsService()->canAccessSubmissions();
        $settingsService = new SettingsService();
        $spamFolderEnabled = $settingsService->getSettingsModel()->isSpamFolderEnabled();

        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $columns = [
            'id'                 => ['type' => Table::COL_ID],
            'Form'               => ['type' => Table::COL_TEXT],
            'Handle'             => ['type' => Table::COL_TEXT],
            'Submissions'        => ['type' => Table::COL_TEXT],
            'Spam'               => ['type' => Table::COL_TEXT],
            'manage'             => ['type' => Table::COL_TOOLBAR],
        ];

        if ($canManageForms) {
            $columns[] = ['type' => Table::COL_CHECKBOX, 'name' => 'selection'];
        }

        $table->setColumns($columns);

        $forms            = FormRepository::getInstance()->getAllForms();
        $submissionTotals = SubmissionRepository::getInstance()->getSubmissionTotalsPerForm();
        $spamTotals = SubmissionRepository::getInstance()->getSpamTotalsPerForm();

        $tableData = [];
        foreach ($forms as $form) {

            $toolbarItems = [];

            if ($canManageForms) {
                $toolbarItems = [
                    'edit' => [
                        'href'  => $this->getLink('forms/' . $form->id),
                        'title' => lang('edit'),
                    ],
                    'copy' => [
                        'href'                 => 'javascript:;',
                        'class'                => 'duplicate',
                        'title'                => lang('Duplicate'),
                        'data-csrf'            => CSRF_TOKEN,
                        'data-url'             => $this->getLink('api/duplicate'),
                        'data-form-id'         => $form->id,
                    ],
                ];
            }

            $toolbar = [
                'toolbar_items' => $toolbarItems,
            ];

            $data = [
                $form->id,
                [
                    'content' => $form->name,
                    'href'    => ($canManageForms ? $this->getLink('forms/' . $form->id) : null),
                ],
                $form->handle,
                [
                    'content' => $submissionTotals[$form->id] ?? 0,
                    'href'    => ($canAccessSubmissions ? $this->getLink('submissions/' . $form->handle) : null ),
                ],
                [
                    'content' => $spamTotals[$form->id] ?? 0,
                    'href'    => (
                        $canAccessSubmissions && $spamFolderEnabled
                            ? $this->getLink('spam/' . $form->handle)
                            : null
                    ),
                ],
                $toolbar,
            ];

            if ($canManageForms) {
                $data[] = [
                    'name'  => 'id_list[]',
                    'value' => $form->id,
                    'data'  => [
                        'confirm' => lang('Form') . ': <b>' . htmlentities(
                                $form->getForm()->getName(),
                                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'
                            ) . '</b>',
                    ],
                ];
            }

            $tableData[] = $data;
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $template = [
            'table'            => $table->viewData(),
            'cp_page_title'    => lang('Forms'),
        ];

        if ($canManageForms) {
            $template['form_right_links'] = FreeformHelper::getRightLinks($this);
        }

		$template['footer'] = ['submit_lang' => lang('submit'), 'type'        => 'bulk_action_form'];

        $view = new CpView('form/listing', $template);

        $view
            ->setHeading(lang('Forms'))
            ->addJavascript('formIndex')
            ->addModal((new ConfirmRemoveModal($this->getLink('forms/delete')))->setKind('Forms'));

        return $view;
    }

    /**
     * @return CpView
     */
    public function edit(FormModel $form): RedirectView|CpView
    {
        if (!($this->getPermissionsService()->canManageForms())) {
            return new RedirectView($this->getLink('denied'));
        }

        $fileService     = new FilesService();
        $settingsService = new SettingsService();

        $view = new CpView('form/edit');
        $view
            ->setHeading($form->name ?: 'New Form')
            ->setSidebarDisabled(true)
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
                    'generatedOptions'         => $this->getGeneratedOptionsList($form->getForm()),
                    'channelFields'            => $this->getChannelFields(),
                    'categoryFields'           => $this->getCategoryFields(),
                    'memberFields'             => $this->getMemberFields(),
                    'isRecaptchaEnabled'       => $settingsService->getSettingsModel()->isRecaptchaEnabled(),
                    'isRecaptchaV3'            => $settingsService->getSettingsModel()->getRecaptchaType() === 'v3',
                    'captchaProvider'          => $settingsService->getSettingsModel()->getCaptchaProvider(),
                ]
            );

        return $view;
    }

    /**
     * @return AjaxView
     * @throws Exception
     */
    public function save()
    {
        $view = new AjaxView();

        if (!($this->getPermissionsService()->canManageForms())) {
            return $view->addError('No access');
        }

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
                [$string, $mainPart, $iterator] = $matches;

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
                $formsService,
                new FieldsService(),
                new SubmissionsService(),
                new MailerService(),
                new FilesService(),
                new MailingListsService(),
                new CrmService(),
                new StatusesService(),
                new EETranslator(),
                $composerState,
                $formAttributes,
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
            } catch (Exception $e) {
                $view->addError($e->getMessage());
            }
        }

        return $view;
    }

    /**
     * @return RedirectView
     */
    public function batchDelete(): RedirectView
    {
        if (!($this->getPermissionsService()->canManageForms())) {
            return new RedirectView($this->getLink('denied'));
        }

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
     * @return array|stdClass
     */
    private function getGeneratedOptionsList(Form $form)
    {
        $options = [];
        foreach ($form->getLayout()->getFields() as $field) {
            if ($field instanceof ExternalOptionsInterface) {
                if ($field->getOptionSource() !== ExternalOptionsInterface::SOURCE_CUSTOM) {
                    $options[$field->getHash()] = $this->getFieldsService()->getOptionsFromSource(
                        $field->getOptionSource(),
                        $field->getOptionTarget(),
                        $field->getOptionConfiguration()
                    );
                }
            }
        }

        if (empty($options)) {
            return new stdClass();
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getSourceTargetsList(): array
    {
        $channels = ee('Model')
            ->get('Channel')
            ->filter('site_id', ee()->config->item('site_id'))
            ->all();

        $channelList = [0 => ['key' => '', 'value' => lang('All Channels')]];

        foreach ($channels as $group) {
            $channelList[] = [
                'key'   => $group->channel_id,
                'value' => $group->channel_title,
            ];
        }

        $categories = ee('Model')
            ->get('CategoryGroup')
            ->filter('site_id', ee()->config->item('site_id'))
            ->all();

        $categoryList = [0 => ['key' => '', 'value' => lang('All Category Groups')]];
        foreach ($categories as $group) {
            $categoryList[] = [
                'key'   => $group->group_id,
                'value' => $group->group_name,
            ];
        }

        $memberRoles = ee('Model')
            ->get('Role')
			->with('RoleSettings')
            ->filter('RoleSettings.site_id', ee()->config->item('site_id'))
            ->all();

        $memberList = [0 => ['key' => '', 'value' => lang('All Roles')]];
        foreach ($memberRoles as $group) {
            $memberList[] = [
                'key'   => $group->role_id,
                'value' => $group->name,
            ];
        }

        return [
            ExternalOptionsInterface::SOURCE_ENTRIES    => $channelList,
            ExternalOptionsInterface::SOURCE_CATEGORIES => $categoryList,
            ExternalOptionsInterface::SOURCE_MEMBERS    => $memberList,
        ];
    }

    /**
     * @return array
     */
    private function getChannelFields()
    {
        $fieldList = [
            ['key' => 'entry_id', 'value' => 'ID'],
            ['key' => 'title', 'value' => lang('Title')],
            ['key' => 'url_title', 'value' => lang('URL Title')],
        ];

        $fields = ee('Model')
            ->get('ChannelField')
            ->filter('site_id', ee()->config->item('site_id'))
            ->orFilter('site_id', 0)
            ->all();

        foreach ($fields as $field) {
            $fieldList[] = ['key' => $field->field_name, 'value' => $field->field_label];
        }

        return $fieldList;
    }

    /**
     * @return array
     */
    private function getCategoryFields()
    {
        $fieldList = [
            ['key' => 'cat_id', 'value' => 'ID'],
            ['key' => 'cat_name', 'value' => lang('Title')],
            ['key' => 'cat_url_title', 'value' => lang('URL Title')],
        ];

        $fields = ee('Model')
            ->get('CategoryField')
            ->filter('site_id', ee()->config->item('site_id'))
            ->orFilter('site_id', 0)
            ->all();

        foreach ($fields as $field) {
            $fieldList[] = ['key' => $field->field_name, 'value' => $field->field_label];
        }

        return $fieldList;
    }

    /**
     * @return array
     */
    private function getMemberFields()
    {
        $fieldList = [
            ['key' => 'member_id', 'value' => 'ID'],
            ['key' => 'username', 'value' => lang('Username')],
            ['key' => 'screen_name', 'value' => lang('Screen Name')],
            ['key' => 'email', 'value' => lang('Email')],
        ];

        $fields = ee('Model')->get('MemberField')->all();

        foreach ($fields as $field) {
            $fieldList[] = ['key' => $field->m_field_name, 'value' => $field->m_field_label];
        }

        return $fieldList;
    }
}
