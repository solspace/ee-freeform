<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use GuzzleHttp\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\CRMOAuthConnector;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\CrmRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class CrmController extends Controller
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

        if ($id === 'check') {
            return $this->check();
        }

        if ($id === 'get') {
            return $this->getIntegrationsAjax();
        }

        if ($id === 'delete') {
            return $this->batchDelete();
        }

        return $this->edit($id);
    }

    /**
     * @return CpView
     */
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

        $integrations = CrmRepository::getInstance()->getAllIntegrations();

        $tableData = [];
        foreach ($integrations as $integration) {
            $tableData[] = [
                $integration->id,
                [
                    'content' => $integration->name,
                    'href'    => $this->getLink('integrations/crm/' . $integration->id),
                ],
                $integration->handle,
                $integration->getIntegrationObject()->getServiceProvider(),
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => UrlHelper::getLink('integrations/crm/' . $integration->id),
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                [
                    'name'  => 'id_list[]',
                    'value' => $integration->id,
                    'data'  => [
                        'confirm' => lang('Integration') . ': <b>' . htmlentities(
                                $integration->name,
                                ENT_QUOTES
                            ) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $removeModal = new ConfirmRemoveModal($this->getLink('integrations/crm/delete'));
        $removeModal->setKind('CRM Integrations');

        $serviceProviderTypes = $this->getCrmService()->getAllCrmServiceProviders();

        if (count($serviceProviderTypes)) {
            $formRightLinks = [
                [
                    'title' => lang('New Integration'),
                    'link'  => $this->getLink('integrations/crm/new'),
                ],
            ];
        } else {
            $formRightLinks = [
                [
                    'title' => lang('Upgrade to Pro to Enable'),
                    'link'  => 'https://docs.solspace.com/expressionengine/freeform/v3/',
                ],
            ];
        }

        $view = new CpView(
            'integrations/table',
            [
                'table'            => $table->viewData(),
                'cp_page_title'    => lang('CRM Integrations'),
                'form_right_links' => $formRightLinks,
            ]
        );
        $view
            ->setHeading(lang('CRM Integrations'))
            ->addModal($removeModal);

        return $view;
    }

    /**
     * @param int|string $id
     *
     * @return View
     * @throws IntegrationException
     */
    public function edit($id)
    {
        $serviceProviderTypes = $this->getCrmService()->getAllCrmServiceProviders();

        if (empty($serviceProviderTypes)) {
            return new RedirectView('https://docs.solspace.com/expressionengine/freeform/v3/');
        }

        if ($id === 'new') {
            $model        = IntegrationModel::create(IntegrationModel::TYPE_CRM);
            $model->class = array_keys($serviceProviderTypes)[0];
        } else {
            $model = CrmRepository::getInstance()->getIntegrationById($id);
        }

        if (!$model) {
            throw new IntegrationException('Integration does not exist');
        }

        $errors = null;
        if (isset($_POST['class'])) {
            $errors = $this->save($model);

            if (empty($errors)) {
                $view = new RedirectView($this->getLink('integrations/crm/'));

                return $view;
            }
        }

        if (ee()->input->get('code')) {
            $this->handleAuthorization($model);
        }

        $integration = $model->getIntegrationObject();
        $settings    = $integration->getSettings();

        $blueprints = $this->getCrmService()->getAllCrmSettingBlueprints();

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
                            'type'     => $item->getType() === SettingBlueprint::TYPE_BOOL ? 'yes_no' : 'text',
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
                            'attrs' => 'data-generator-base',
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
                            'attrs' => 'data-generator-target',
                        ],
                    ],
                ],
            ],
            'Settings' => $settingGroups,
        ];

        if ($model->id) {
            $link = $this->getLink('integrations/' . $model->type . '/check');
            $sectionData[0][] = [
                'title'  => 'Is Authorized?',
                'desc'   => 'Is the connection authorized?',
                'fields' => [
                    'handle' => [
                        'type'    => 'html',
                        'content' => '
                            <div id="auth-checker" data-url-stub="' . $link . '">
                                <div class="authorized" style="display: none;">
                                    Authorized
                                </div>
                                <div class="not-authorized" style="display: none;">
                                    Not able to authorize.
                                    <a href="' . $link . '" class="">Click here to re-authorize</a>
                                    <div class="errors"></div>
                                </div>
                                <div class="pending-status-check" 
                                     data-id="' . $model->id . '" 
                                     data-type="' . $model->type . '">
                                    Checking credentials...
                                </div>
                            </div>
                        ',
                    ],
                ],
            ];
        }


        ee()->cp->add_js_script(
            [
                'file' => ['cp/form_group'],
            ]
        );

        $view = new CpView(
            'integrations/edit',
            [
                'cp_page_title'         => 'CRM Integration',
                'base_url'              => $this->getLink('integrations/crm/' . $id),
                'sections'              => $sectionData,
                'save_btn_text'         => 'Save',
                'save_btn_text_working' => 'Saving',
                'errors'                => $errors,
            ]
        );

        $view
            ->setHeading($model->name ?: 'New CRM Integration')
            ->addBreadcrumb(new NavigationLink('CRM Integrations', 'integrations/crm'))
            ->addJavascript('integrations')
            ->addJavascript('handleGenerator');

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

        $settings = $model->settings ? json_decode($model->settings, true) : [];

        $blueprints = $this->getCrmService()->getCrmSettingBlueprints($class);

        foreach ($blueprints as $blueprint) {
            if (!$blueprint->isEditable()) {
                continue;
            }

            $blueprintHandle = $blueprint->getHandle();
            $value           = $postedSettings[$blueprintHandle];

            if ($blueprint->getType() === SettingBlueprint::TYPE_BOOL) {
                $value = $value === 'y';
            }

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

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_CRM_BEFORE_SAVE, $model, $isNew)) {
            return null;
        }

        $model->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_CRM_AFTER_SAVE, $model, $isNew);

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('Success'))
            ->defer();

        return null;
    }

    /**
     * @return AjaxView
     */
    public function getIntegrationsAjax()
    {
        $integrations = CrmRepository::getInstance()->getAllIntegrationObjects();

        foreach ($integrations as $integration) {
            $integration->setForceUpdate(true);
        }

        $ajaxView = new AjaxView();
        $ajaxView->setVariables($integrations);

        return $ajaxView;
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

            $models = CrmRepository::getInstance()->getIntegrationsByIdList($ids);

            foreach ($models as $model) {
                if (!ExtensionHelper::call(ExtensionHelper::HOOK_CRM_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_CRM_BEFORE_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink('integrations/crm/'));
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

        if (!$integration instanceof CRMOAuthConnector || empty($code)) {
            return;
        }

        $accessToken = $integration->fetchAccessToken();

        $model->accessToken = $accessToken;
        $model->settings    = $integration->getSettings();

        $this->save($model);
    }

    /**
     * @return AjaxView
     */
    private function check()
    {
        $view = new AjaxView();

        $id = ee()->input->post('id');
        $model = CrmRepository::getInstance()->getIntegrationById($id);
        $integration = $model->getIntegrationObject();

        if (!$model) {
            $view->addVariable('success', false);
            $view->addError('Integration does not exist');
        }

        try {
            if ($integration->checkConnection()) {
                $view->addVariable('success', true);
            } else {
                $view->addVariable('success', false);
                $view->addError('Could not connect');
            }
        } catch (BadResponseException $e) {
            if ($integration instanceof TokenRefreshInterface) {
                try {
                    if ($integration->refreshToken() && $integration->isAccessTokenUpdated()) {
                        $crmService = new CrmService();
                        $crmService->updateAccessToken($integration);

                        $view->addVariable('success', true);
                    } else {
                        $view->addVariable('success', false);
                        $view->addError($e->getResponse()->getBody(true));
                    }
                } catch (\Exception $e) {
                    $view->addVariable('success', false);
                    $view->addError($e->getMessage());
                }
            } else {
                $view->addVariable('success', false);
                $view->addError($e->getMessage());
            }
        }

        return $view;
    }

    /**
     * @return CrmService
     */
    private function getCrmService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new CrmService();
        }

        return $instance;
    }
}
