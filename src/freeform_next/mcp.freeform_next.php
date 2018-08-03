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

use Solspace\Addons\FreeformNext\Controllers\ApiController;
use Solspace\Addons\FreeformNext\Controllers\CrmController;
use Solspace\Addons\FreeformNext\Controllers\ExportController;
use Solspace\Addons\FreeformNext\Controllers\ExportProfilesController;
use Solspace\Addons\FreeformNext\Controllers\FieldController;
use Solspace\Addons\FreeformNext\Controllers\FormController;
use Solspace\Addons\FreeformNext\Controllers\LogController;
use Solspace\Addons\FreeformNext\Controllers\MailingListsController;
use Solspace\Addons\FreeformNext\Controllers\MigrationsController;
use Solspace\Addons\FreeformNext\Controllers\NotificationController;
use Solspace\Addons\FreeformNext\Controllers\SettingsController;
use Solspace\Addons\FreeformNext\Controllers\SubmissionController;
use Solspace\Addons\FreeformNext\Controllers\UpdateController;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Model\ExportProfileModel;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\MigrationsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\UpdateService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\Navigation;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanelView;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Freeform_next_mcp extends ControlPanelView
{
    /**
     * @return array
     */
    public function index()
    {
        return $this->renderView(new RedirectView($this->getLink('forms')));
    }

    /**
     * @param int|string|null $formId
     *
     * @return array
     * @throws \Exception
     * @throws FreeformException
     */
    public function forms($formId = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        if (isset($_POST['composerState'])) {
            $this->renderView($this->getFormController()->save());
        }

        if (null !== $formId) {
            if (strtolower($formId) === 'new') {
                $form = FormModel::create();
            } else if (strtolower($formId) === 'delete') {
                return $this->renderView($this->getFormController()->batchDelete());
            } else {
                $form = FormRepository::getInstance()->getFormById($formId);
            }

            if (!$form) {
                throw new FreeformException("Form doesn't exist");
            }

            return $this->renderView($this->getFormController()->edit($form));
        }

        return $this->renderView($this->getFormController()->index());
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function api($type)
    {
        var_dump($type);die();
        $apiController = new ApiController();
        $args          = func_get_args();

        return $this->renderView($apiController->handle($type, $args));
    }

    /**
     * @param int|null $id
     *
     * @return array
     */
    public function fields($id = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        if (strtolower($id) === 'delete') {
            return $this->renderView($this->getFieldController()->batchDelete());
        }

        if (null !== $id) {
            $validation = null;
            if (isset($_POST['label'])) {
                $validation = ee('Validation')->make(FieldModel::createValidationRules())->validate($_POST);
                if ($validation->isValid()) {
                    $this->getFieldController()->save($id);

                    return $this->renderView(
                        new RedirectView(
                            UrlHelper::getLink('fields/')
                        )
                    );
                }
            }

            return $this->renderView($this->getFieldController()->edit($id, $validation));
        }

        return $this->renderView($this->getFieldController()->index());
    }

    /**
     * @param null $notificationId
     *
     * @return array
     * @throws FreeformException
     */
    public function notifications($notificationId = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        if (strtolower($notificationId) === 'delete') {
            return $this->renderView($this->getNotificationController()->batchDelete());
        }

        if (null !== $notificationId) {
            $validation = null;
            if (isset($_POST['name'])) {
                $validation = ee('Validation')->make(NotificationModel::createValidationRules())->validate($_POST);
                if ($validation->isValid()) {
                    $this->getNotificationController()->save($notificationId);

                    return $this->renderView(
                        new RedirectView(
                            UrlHelper::getLink('notifications/')
                        )
                    );
                }
            }

            return $this->renderView($this->getNotificationController()->edit($notificationId, $validation));
        }

        return $this->renderView($this->getNotificationController()->index());
    }

    /**
     * @param int    $seg1
     * @param string $seg2
     *
     * @return array
     */
    public function export_profiles($seg1 = null, $seg2 = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        if (strtolower($seg1) === 'delete') {
            return $this->renderView($this->getExportProfilesController()->batchDelete());
        }

        if (null !== $seg1) {
            if (in_array($seg1, ['csv', 'xml', 'json', 'text'], true)) {
                return $this->getExportProfilesController()->export($seg2, $seg1);
            }

            $validation = null;
            if (isset($_POST['name'])) {
                $validation = ee('Validation')->make(ExportProfileModel::createValidationRules())->validate($_POST);
                if ($validation->isValid()) {
                    $this->getExportProfilesController()->save($seg1);
                    return $this->renderView(
                        new RedirectView(
                            UrlHelper::getLink('export_profiles/')
                        )
                    );
                }
            }

            return $this->renderView($this->getExportProfilesController()->edit($seg1, $seg2, $validation));
        }

        return $this->renderView($this->getExportProfilesController()->index());
    }

    /**
     * @param null $id
     *
     * @return array
     */
    public function export($id = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        $controller = new ExportController();
        if ($id === 'dialogue') {
            return $this->renderView($controller->exportDialogue());
        }

        return $this->renderView($controller->export());
    }

    /**
     * @param string   $formHandle
     * @param int|null $submissionId
     *
     * @return array
     * @throws FreeformException
     */
    public function submissions($formHandle, $submissionId = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($formHandle);
        $form      = $formModel->getForm();

        if (null !== $submissionId) {
            if (strtolower($submissionId) === 'delete') {
                return $this->renderView($this->getSubmissionController()->batchDelete($form));
            }

            $submission = SubmissionRepository::getInstance()->getSubmission($form, $submissionId);

            if ($submission) {
                if (isset($_POST['title'])) {
                    $this->getSubmissionController()->save($form, $submission);

                    return $this->renderView(
                        new RedirectView(
                            UrlHelper::getLink('submissions/' . $formHandle . '/')
                        )
                    );
                }

                return $this->renderView($this->getSubmissionController()->edit($form, $submission));
            }

            throw new FreeformException(lang('Submission not found'));
        }

        return $this->renderView($this->getSubmissionController()->index($form));
    }

    /**
     * @return array
     */
    public function templates()
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        $ajaxView = new AjaxView();
        $ajaxView->addVariable('success', true);

        if (isset($_POST['templateName'])) {
            $settings = SettingsRepository::getInstance()->getOrCreate();
            if ($settings->getFormattingTemplatePath()) {
                $templateName = ee()->input->post('templateName');
                $templateName = (string) \Stringy\Stringy::create($templateName)->underscored()->toAscii();
                $templateName .= '.html';

                $filePath = $settings->getFormattingTemplatePath() . '/' . $templateName;

                if (file_exists($filePath)) {
                    $ajaxView->addError(sprintf('Template "%s" already exists', $templateName));
                } else {
                    $handle = fopen($filePath, 'w');

                    if (false === $handle) {
                        $ajaxView->addError('');
                    } else {
                        $content = $settings->getDemoTemplateContent();
                        fwrite($handle, $content);
                        fclose($handle);

                        $ajaxView->addVariable('templateName', $templateName);
                    }
                }
            } else {
                $ajaxView->addError('No custom template directory specified in settings');
            }
        } else {
            $ajaxView->addError('No template name specified');
        }

        return $this->renderView($ajaxView);
    }

    /**
     * @return array
     */
    public function formTemplates()
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        $settings = new SettingsService();
        $ajaxView = new AjaxView();
        $ajaxView->setVariables($settings->getCustomFormTemplates());

        return $this->renderView($ajaxView);
    }

    /**
     * @return array
     */
    public function finish_tutorial()
    {
        $service = new SettingsService();
        $service->finishTutorial();

        $ajaxView = new AjaxView();
        $ajaxView->addVariable('success', true);

        return $this->renderView($ajaxView);
    }

    /**
     * @param string          $type
     * @param null|string|int $id
     *
     * @return array
     */
    public function settings($type, $id = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        return $this->renderView($this->getSettingsController()->index($type, $id));
    }

    /**
     * @return array
     */
    public function updates()
    {
        $updateController = new UpdateController();

        return $this->renderView($updateController->index());
    }

    /**
     * @param string $type
     * @param null   $id
     *
     * @return array
     */
    public function integrations($type, $id = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        switch (strtolower($type)) {
            case 'mailing_lists':
                return $this->renderView($this->getMailingListsController()->handle($id));

            case 'crm':
                return $this->renderView($this->getCrmController()->handle($id));
        }

        return null;
    }

    /**
     * @param string $type
     * @param null   $id
     *
     * @return array
     */
    public function migrations($type, $id = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        switch (strtolower($type)) {
            case 'ff_classic':
                return $this->renderView($this->getMigrationController()->handle($id));
        }
    }

    /**
     * @param string      $logName
     * @param string|null $action
     *
     * @return array
     */
    public function logs($logName, $action = null)
    {
        if (!($this->getPermissionsService()->canUserAccessSection(__FUNCTION__, ee()->session->userdata('group_id')))) {
            return $this->renderView(new RedirectView($this->getLink('denied')));
        }

        $controller = new LogController();

        return $this->renderView($controller->view($logName, $action));
    }

    public function denied()
    {
        return $this->renderView($this->getSettingsController()->permissionDenied());
    }

    /**
     * @return Navigation
     */
    protected function buildNavigation()
    {
        $forms = new NavigationLink('Forms', 'forms');
        FreeformHelper::get('navigation', $forms);

        $notifications = new NavigationLink('Notifications', 'notifications');
        $notifications->setButtonLink(new NavigationLink('New', 'notifications/new'));

        $fields = new NavigationLink('Fields', 'fields');
        FreeformHelper::get('navigation', $fields);

        $integrations = new NavigationLink('Integrations');
        $integrations
            ->addSubNavItem(new NavigationLink('Mailing Lists', 'integrations/mailing_lists'))
            ->addSubNavItem(new NavigationLink('CRM', 'integrations/crm'));

        $isMigrationAvailable = false;

        $migrations = new NavigationLink('Migrations');

        if ($this->isClassicFreeformInstalled()) {
            $isMigrationAvailable = true;
            $migrations
                ->addSubNavItem(new NavigationLink('Migrate Freeform Classic', 'migrations/ff_classic'));
        }


        $exportProfiles = null;
        if (class_exists('Solspace\Addons\FreeformNext\Controllers\ExportProfilesController')) {
            $exportProfiles = new NavigationLink('Export', 'export_profiles');
        }

        $settings = new NavigationLink('Settings');
        $settings
            ->addSubNavItem(new NavigationLink('License', 'settings/license'))
            ->addSubNavItem(new NavigationLink('General', 'settings/general'))
            ->addSubNavItem(new NavigationLink('Permissions', 'settings/permissions'))
            ->addSubNavItem(new NavigationLink('Formatting Templates', 'settings/formatting_templates'))
            ->addSubNavItem(new NavigationLink('Email Templates', 'settings/email_templates'))
            ->addSubNavItem(new NavigationLink('Statuses', 'settings/statuses'))
            ->addSubNavItem(new NavigationLink('Demo Templates', 'settings/demo_templates'));

        $resources = new NavigationLink('Resources');
        $resources
            ->addSubNavItem(
                new NavigationLink('Product Info', 'https://solspace.com/expressionengine/freeform')
            )
            ->addSubNavItem(
                new NavigationLink('Documentation', 'https://solspace.com/expressionengine/freeform/docs')
            )
            ->addSubNavItem(
                new NavigationLink('Official Support', 'https://solspace.com/expressionengine/freeform/support')
            );

        $logs   = null;
        $logdir = __DIR__ . '/logs/';
        if (file_exists($logdir) && is_dir($logdir)) {
            $fs = new Finder();
            /** @var SplFileInfo[] $files */
            $files = $fs
                ->files()
                ->in($logdir)
                ->name('*.log')
                ->sortByName();

            if (count($files)) {
                $logs = new NavigationLink('Logs');

                foreach ($files as $file) {
                    $modTime    = $file->getMTime();
                    $accessTime = $file->getATime();

                    $logs->addSubNavItem(
                        new NavigationLink(
                            $file->getFilename() . ($modTime > $accessTime ? ' (new)' : ''),
                            'logs/' . str_replace('.log', '', $file->getFilename())
                        )
                    );
                }
            }
        }

        $updates = null;
        $updateService = new UpdateService();
        if ($updateService->updateCount()) {
            $updates = new NavigationLink(
                'Updates Available (' . $updateService->updateCount() . ')',
                'updates'
            );
        }

        $nav = new Navigation();
        $nav
            ->addLink($forms)
            ->addLink($fields)
            ->addLink($notifications)
            ->addLink($exportProfiles)
            ->addLink($settings)
            ->addLink($integrations);

        if ($isMigrationAvailable) {
            $nav->addLink($migrations);
        }

        $nav->addLink($resources);

        if ($logs) {
            $nav->addLink($logs);
        }

        if ($updates) {
            $nav->addLink($updates);
        }

        return $nav;
    }

    /**
     * @return FormController
     */
    private function getFormController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new FormController();
        }

        return $instance;
    }

    /**
     * @return SubmissionController
     */
    private function getSubmissionController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new SubmissionController();
        }

        return $instance;
    }

    /**
     * @return NotificationController
     */
    private function getNotificationController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NotificationController();
        }

        return $instance;
    }

    /**
     * @return ExportProfilesController
     */
    private function getExportProfilesController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ExportProfilesController();
        }

        return $instance;
    }

    /**
     * @return FieldController
     */
    private function getFieldController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new FieldController();
        }

        return $instance;
    }

    /**
     * @return SettingsController
     */
    private function getSettingsController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new SettingsController();
        }

        return $instance;
    }

    /**
     * @return CrmController
     */
    public function getCrmController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new CrmController();
        }

        return $instance;
    }

    /**
     * @return MailingListsController
     */
    private function getMailingListsController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new MailingListsController();
        }

        return $instance;
    }

    /**
     * @return MigrationsController
     */
    private function getMigrationController()
    {
        static $instance;

        if (null === $instance) {
            $instance = new MigrationsController();
        }

        return $instance;
    }

    /**
     * @return bool
     */
    private function isClassicFreeformInstalled()
    {
        return (new MigrationsService())->isClassicFreeformInstalled();
    }

    /**
     * @return \Solspace\Addons\FreeformNext\Services\PermissionsService
     */
    private function getPermissionsService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new \Solspace\Addons\FreeformNext\Services\PermissionsService();
        }

        return $instance;
    }
}
