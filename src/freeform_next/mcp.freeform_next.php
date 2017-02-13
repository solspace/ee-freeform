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
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
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
            $this->renderAjaxView($this->getFormController()->saveForm());
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

            return $this->renderView($this->getFormController()->editForm($form));
        }

        return $this->renderView($this->getFormController()->index());
    }

    /**
     * @return array
     */
    public function fields()
    {
        if (!empty($_POST)) {
            $this->renderAjaxView($this->getFieldController()->saveField());
        }

        $view = new AjaxView();
        $view->setVariables(FieldRepository::getInstance()->getAllFields());

        $this->renderAjaxView($view);
    }

    public function notifications()
    {
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