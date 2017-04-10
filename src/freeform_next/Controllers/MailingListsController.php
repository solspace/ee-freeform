<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListOAuthConnector;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class MailingListsController extends Controller
{
    /**
     * @param int|null $id
     *
     * @return View
     */
    public function handle($id = null)
    {
        if (null === $id) {
            return $this->index();
        }

        if ($id === 'get') {
            return $this->getIntegrationsAjax();
        }

        return $this->edit($id);
    }

    public function index()
    {
        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $table->setColumns(
            [
                'id'               => ['type' => Table::COL_ID],
                'Name'             => ['type' => Table::COL_TEXT],
                'Handle'           => ['type' => Table::COL_TEXT],
                'Service Provider' => ['type' => Table::COL_TEXT],
                'manage'           => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $integrations = MailingListRepository::getInstance()->getAllIntegrations();

        $tableData = [];
        foreach ($integrations as $integration) {
            $tableData[] = [
                $integration->id,
                $integration->name,
                $integration->handle,
                $integration->getIntegrationObject()->getServiceProvider(),
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => UrlHelper::getLink('integrations/mailing_lists/' . $integration->id),
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                [
                    'name'  => 'id_list[]',
                    'value' => $integration->id,
                    'data'  => [
                        'confirm' => lang('Integration') . ': <b>' . htmlentities($integration->name, ENT_QUOTES) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $removeModal = new ConfirmRemoveModal($this->getLink('integrations/mailing_lists/delete'));
        $removeModal->setKind('Mailing List Integrations');

        $view = new CpView(
            'integrations/table',
            [
                'table'            => $table->viewData(),
                'cp_page_title'    => lang('Mailing List Integrations'),
                'form_right_links' => [
                    [
                        'title' => lang('New Integration'),
                        'link'  => $this->getLink('integrations/mailing_lists/new'),
                    ],
                ],
            ]
        );
        $view->setHeading(lang('Mailing List Integrations'));
        $view->addModal($removeModal);

        return $view;
    }

    /**
     * @param int|string $id
     *
     * @return CpView
     * @throws IntegrationException
     */
    public function edit($id)
    {
        if ($id === 'new') {
            $model        = IntegrationModel::create(IntegrationModel::TYPE_MAILING_LIST);
            $model->class = '\Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\Implementations\MailChimp';
        } else {
            $model = MailingListRepository::getInstance()->getIntegrationById($id);
        }

        if (!$model) {
            throw new IntegrationException('Integration does not exist');
        }

        $errors = null;
        if (isset($_POST['class'])) {
            $errors = $this->save($model);

            if (empty($errors)) {
                $view = new RedirectView($this->getLink('integrations/mailing_lists/' . $model->id));

                return $view;
            }
        }

        if (ee()->input->get('code')) {
            $this->handleAuthorization($model);
        }

        $integration = $model->getIntegrationObject();
        $settings    = $integration->getSettings();

        $serviceProviderTypes = $this->getMailingListService()->getAllMailingListServiceProviders();
        $blueprints           = $this->getMailingListService()->getAllMailingListSettingBlueprints();

        $types = $targets = $settingGroups = [];
        foreach ($serviceProviderTypes as $className => $name) {
            $hash                = md5($className);
            $types[$className]   = $name;
            $targets[$className] = $hash;
        }

        foreach ($blueprints as $className => $rows) {
            /** @var SettingBlueprint $item */
            foreach ($rows as $item) {
                if (!$item->isEditable()) {
                    continue;
                }

                $hash = md5($className);

                $settingGroups[] = [
                    'title'  => $item->getLabel(),
                    'desc'   => $item->getInstructions(),
                    'group'  => $hash,
                    'fields' => [
                        $hash . '-' . $item->getHandle() => [
                            'type'     => 'text',
                            'required' => $item->isRequired(),
                            'value'    => isset($settings[$item->getHandle()]) ? $settings[$item->getHandle()] : null,
                        ],
                    ],
                ];
            }
        }

        $sectionData = [
            [
                [
                    'title'  => lang('Service Provider'),
                    'fields' => [
                        'class' => [
                            'type'         => 'select',
                            'value'        => $model->class,
                            'choices'      => $types,
                            'group_toggle' => $targets,
                        ],
                    ],
                ],
                [
                    'title'  => 'Name',
                    'desc'   => 'What this integration will be called in the CP.',
                    'fields' => [
                        'name' => [
                            'type'  => 'text',
                            'value' => $model->name,
                            'attrs'    => 'data-generator-base',
                        ],
                    ],
                ],
                [
                    'title'  => 'Handle',
                    'desc'   => 'The unique name used to identify this integration.',
                    'fields' => [
                        'handle' => [
                            'type'  => 'text',
                            'value' => $model->handle,
                            'attrs'    => 'data-generator-target',
                        ],
                    ],
                ],
            ],
            'Settings' => $settingGroups,
        ];

        ee()->cp->add_js_script(
            [
                'file' => ['cp/form_group'],
            ]
        );

        $view = new CpView(
            'integrations/edit',
            [
                'cp_page_title'         => 'Mailing List Integration',
                'base_url'              => $this->getLink('integrations/mailing_lists/' . $id),
                'sections'              => $sectionData,
                'save_btn_text'         => 'Save',
                'save_btn_text_working' => 'Saving',
                'errors'                => $errors,
            ]
        );
        $view->addJavascript('handleGenerator');

        return $view;
    }

    /**
     * @param IntegrationModel $model
     *
     * @return bool
     */
    public function save(IntegrationModel $model)
    {
        $isNew = !$model->id;

        $class  = ee()->input->post('class');
        $hash   = md5($class);
        $name   = ee()->input->post('name');
        $handle = ee()->input->post('handle');

        $rules = [
            'class'  => 'required',
            'name'   => 'required',
            'handle' => 'required',
        ];

        $postedSettings = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, $hash) === 0) {
                $postedSettings[str_replace($hash . '-', '', $key)] = $value;
            }
        }

        $settings = json_decode($model->settings, true) ?: [];

        $blueprints = $this->getMailingListService()->getMailingListSettingBlueprints($class);

        foreach ($blueprints as $blueprint) {
            if (!$blueprint->isEditable()) {
                continue;
            }

            $blueprintHandle = $blueprint->getHandle();
            $value           = $postedSettings[$blueprintHandle];

            $settings[$blueprintHandle] = $value;
            if ($blueprint->isRequired()) {
                $rules["{$hash}-{$blueprint->getHandle()}"] = 'required';
            }
        }

        $validation = ee('Validation')->make($rules)->validate($_POST);
        if (!$validation->isValid()) {
            return $validation;
        }

        $model->updateSettings($settings);
        $model->name        = $name;
        $model->handle      = $handle;
        $model->class       = $class;
        $model->forceUpdate = true;

        $model->getIntegrationObject()->onBeforeSave($model);

        if ($isNew) {
            $model->getIntegrationObject()->initiateAuthentication();
        }

        $model->save();

        return null;
    }

    /**
     * @return AjaxView
     */
    public function getIntegrationsAjax()
    {
        $integrations = MailingListRepository::getInstance()->getAllIntegrationObjects();

        foreach ($integrations as $integration) {
            $integration->setForceUpdate(true);
        }

        $ajaxView = new AjaxView();
        $ajaxView->setVariables($integrations);

        return $ajaxView;
    }

    /**
     * Handle OAuth2 authorization
     *
     * @param IntegrationModel $model
     */
    private function handleAuthorization(IntegrationModel $model)
    {
        $integration = $model->getIntegrationObject();
        $code        = ee()->input->get('code');

        if (!$integration instanceof MailingListOAuthConnector || empty($code)) {
            return;
        }

        $accessToken = $integration->fetchAccessToken();

        $model->accessToken = $accessToken;
        $model->settings    = $integration->getSettings();

        if ($this->save($model)) {
            //// Return JSON response if the request is an AJAX request
            //craft()->userSession->setNotice(Craft::t("Mailing List Integration saved"));
            //craft()->userSession->setFlash(Craft::t("Mailing List Integration saved"), true);
            //
            //craft()->request->redirect(UrlHelper::getCpUrl("freeform/settings/mailing-lists/" . $model->handle));
        } else {
            //craft()->userSession->setError(Craft::t("Mailing List Integration not saved"));
            //
            //craft()->request->redirect(UrlHelper::getCpUrl("freeform/settings/mailing-lists/" . $model->handle));
        }
    }

    /**
     * @return MailingListsService
     */
    private function getMailingListService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new MailingListsService();
        }

        return $instance;
    }
}
