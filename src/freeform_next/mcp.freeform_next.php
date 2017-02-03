<?php

use Solspace\Addons\FreeformNext\Controllers\FormController;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\Navigation;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanelView;

class Freeform_next_mcp extends ControlPanelView
{
    /**
     * @return array
     */
    public function index()
    {
        return $this->renderView($this->getFormController()->index());
    }

    public function new_form()
    {
        $form = FormRepository::getInstance()->getOrCreateForm();

        return $this->renderView($this->getFormController()->editForm($form));
    }

    public function edit_form()
    {

    }

    public function notifications()
    {
        return $this->renderView();
    }

    /**
     * @return Navigation
     */
    protected function buildNavigation()
    {
        $forms = new NavigationLink('Forms');
        $forms->setButtonLink(new NavigationLink('New', 'new_form'));

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

        if (is_null($instance)) {
            $instance = new FormController();
        }

        return $instance;
    }
}