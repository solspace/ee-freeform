<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use GuzzleHttp\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\MigrationResultObject;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\MigrationsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class MigrationsController extends Controller
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

        if ($id === 'run') {
            return $this->run($id);
        }

        return $this->index();
    }

    /**
     * @return View
     */
    public function index()
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        $pageTitle = lang('Migrate Freeform Classic');

        if (!$this->getMigrationService()->isClassicFreeformInstalled()) {
            $view = new RedirectView($this->getLink(''));

            return $view;
        }

        if (!$this->getMigrationService()->isFreeformNextFreshlyInstalled()) {
            $view = new CpView(
                'migrations/re-install',
                [
                    'cp_page_title'    => $pageTitle,
                    'form_right_links' => [],
                ]
            );
            $view->setHeading($pageTitle);

            return $view;
        }

        if (!$this->getMigrationService()->isExpressCompatible()) {
            $formCount  = (int) ee()->db->count_all('freeform_forms');
            $fieldCount = (int) ee()->db->count_all('freeform_fields');

            $view = new CpView(
                'migrations/express-incompatible',
                [
                    'cp_page_title'    => $pageTitle,
                    'form_right_links' => [],
                    'formCount'        => $formCount,
                    'fieldCount'       => $fieldCount,
                ]
            );
            $view->setHeading($pageTitle);

            return $view;
        }

        return $this->buildHomepage();
    }

    /**
     * @param int|string $id
     *
     * @return View
     */
    public function run($id)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        $result = [];
        $stage = $this->getPost('stage');

        if (null === $stage) {
            header('HTTP/1.1 500 Internal Server Error');
            $result['error'] = 'Stage is missing';

            $ajaxView = new AjaxView();
            $ajaxView->setVariables($result);

            return $ajaxView;
        }

        $nextPage  = null;
        $nextForm  = null;
        $stageName = $stage;

        if ($stageName === MigrationsService::STATUS__SUBMISSIONS) {
            $nextPage = $this->getPost('nextPage');
            $nextForm = $this->getPost('nextForm');
        }

        /** @var MigrationsService $migrationService */
        $migrationService = $this->getMigrationService();

        /** @var MigrationResultObject $migrationResult */
        $migrationResult = $migrationService->runStage($stageName, $nextForm, $nextPage);
        $finished        = $migrationResult->finished;

        $result = [
            'success'         => $migrationResult->isMigrationSuccessful(),
            'errors'          => $migrationResult->getErrors(),
            'stage'           => $migrationService->getStageInfo($stageName),
            'nextStage'       => $migrationService->getNextStageInfo($stageName),
            'finished'        => $finished,
            'submissionsInfo' => $migrationResult->submissionsInfo,
        ];

        $ajaxView = new AjaxView();
        $ajaxView->setVariables($result);

        return $ajaxView;
    }

    private function buildHomepage()
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        $pageTitle = lang('Migrate Freeform Classic');

        $migrateUrl = $this->getLink('migrations/ff_classic/run');

        /** @var MigrationsService $migrationService */
        $migrationService    = $this->getMigrationService();
        $firstStage          = $migrationService->getFirstStageInfo();
        $finishedRedirectUrl = $this->getLink('');

        $formRightLinks = [];

        $view = new CpView(
            'migrations/index',
            [
                'cp_page_title'         => $pageTitle,
                'form_right_links'      => $formRightLinks,
                'migrate_url'           => $migrateUrl,
                'first_stage'           => $firstStage,
                'finished_redirect_url' => $finishedRedirectUrl,
            ]
        );

        $view->addJavascript('migration');
        $view->setHeading($pageTitle);

        return $view;
    }

    /**
     * @param IntegrationModel $model
     *
     * @return bool
     */
    public function save(IntegrationModel $model)
    {
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return false;
        }

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

        $blueprints = $this->getMailingListsService()->getMailingListSettingBlueprints($class);

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

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_BEFORE_SAVE, $model, $isNew)) {
            return null;
        }

        $model->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_AFTER_SAVE, $model, $isNew);

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
        $integrations = MailingListRepository::getInstance()->getAllIntegrationObjects();

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
        $canAccessSettings = $this->getPermissionsService()->canAccessSettings(ee()->session->userdata('group_id'));

        if (!$canAccessSettings) {
            return new RedirectView($this->getLink('denied'));
        }

        if (isset($_POST['id_list'])) {
            $ids = [];
            foreach ($_POST['id_list'] as $id) {
                $ids[] = (int) $id;
            }

            $models = MailingListRepository::getInstance()->getIntegrationsByIdList($ids);

            foreach ($models as $model) {
                if (!ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_AFTER_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink('integrations/mailing_lists/'));
    }

    /**
     * @return AjaxView
     */
    private function ajaxSubmission()
    {
        $view = new AjaxView();

        $id          = ee()->input->post('id');
        $model       = MailingListRepository::getInstance()->getIntegrationById($id);
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
            $view->addVariable('success', false);
            $view->addError($e->getResponse()->getBody(true));
        }

        return $view;
    }

    /**
     * @return MigrationsService
     */
    private function getMigrationService()
    {
        static $service;

        if (null === $service) {
            $service = new MigrationsService();
        }

        return $service;
    }

    /**
     * @return MailingListsService
     */
    private function getMailingListsService()
    {
        static $service;

        if (null === $service) {
            $service = new MailingListsService();
        }

        return $service;
    }
}
