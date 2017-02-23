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

use Solspace\Addons\FreeformNext\Controllers\FieldController;
use Solspace\Addons\FreeformNext\Controllers\FormController;
use Solspace\Addons\FreeformNext\Controllers\NotificationController;
use Solspace\Addons\FreeformNext\Controllers\SubmissionController;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\Navigation;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanelView;

require_once __DIR__ . '/vendor/autoload.php';

class Freeform_next_mcp extends ControlPanelView
{
    /**
     * @return array
     */
    public function index()
    {
        return $this->renderView($this->getFormController()->index());
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
        if (isset($_POST['composerState'])) {
            $this->renderView($this->getFormController()->save());
        }

        if (null !== $formId) {
            if (strtolower($formId) === 'new') {
                $form = FormModel::create();
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
     * @return array
     */
    public function fields()
    {
        if (!empty($_POST)) {
            $this->renderView($this->getFieldController()->save());
        }

        $view = new AjaxView();
        $view->setVariables(FieldRepository::getInstance()->getAllFields());

        $this->renderView($view);
    }

    /**
     * @param null $notificationId
     *
     * @return array
     * @throws FreeformException
     */
    public function notifications($notificationId = null)
    {
        if (isset($_POST['name'])) {
            $this->renderView($this->getNotificationController()->create());
        }

        if (null !== $notificationId) {
            if (strtolower($notificationId) === 'new') {
                $notification = NotificationModel::create();
            } else if (strtolower($notificationId) === 'list') {
                $ajaxView = new AjaxView();
                $ajaxView->setVariables(NotificationRepository::getInstance()->getAllNotifications());

                $this->renderView($ajaxView);
            } else {
                $notification = NotificationRepository::getInstance()->getNotificationById($notificationId);
            }

            if (!$notification) {
                throw new FreeformException("Notification doesn't exist");
            }

            return $this->renderView($this->getNotificationController()->editForm($notification));
        }

        return $this->renderView($this->getNotificationController()->index());
    }

    /**
     * @param string   $formHandle
     * @param int|null $submissionId
     *
     * @return array
     */
    public function submissions($formHandle, $submissionId = null)
    {
        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($formHandle);
        $form      = $formModel->getForm();

        if ($submissionId) {
            $submission = SubmissionRepository::getInstance()->getSubmission($form, $submissionId);

            if ($submission) {
                echo $submission->firstName;
                die();
            }
        }

        return $this->renderView($this->getSubmissionController()->index($form));
    }

    public function templates()
    {
    }

    public function finish_tutorial()
    {
    }

    /**
     * @return Navigation
     */
    protected function buildNavigation()
    {
        $forms = new NavigationLink('Forms');
        $forms->setButtonLink(new NavigationLink('New', 'forms/new'));

        $nav = new Navigation();
        $nav
            ->addLink($forms)
            ->addLink(new NavigationLink('Notifications', 'notifications'))
            ->addLink(new NavigationLink('Settings', 'settings'));

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
}
