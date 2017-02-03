<?php

namespace Solspace\Addons\FreeformNext\Utilities;

use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\Navigation;

class ControlPanelView
{
    /**
     * Returns a navigation view.
     * Override this method to customize the navigation view
     *
     * @return Navigation
     */
    protected function buildNavigation()
    {
        return new Navigation();
    }

    /**
     * @param CpView $view
     *
     * @return array
     */
    protected final function renderView(CpView $view)
    {
        $viewData = [
            'sidebar' => $this->buildNavigation()->buildNavigationView(),
            'body'    => $view->renderBody(),
        ];

        if ($view->getHeading()) {
            $viewData['heading'] = $view->getHeading();
        }

        return $viewData;
    }
}