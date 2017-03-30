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

namespace Solspace\Addons\FreeformNext\Utilities;

use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\Navigation;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

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
     * @param View $view
     *
     * @return array
     */
    protected final function renderView(View $view)
    {
        if ($view instanceof AjaxView) {
            header('Content-Type: application/json');
            echo json_encode($view->compile());
            die();
        }

        if ($view instanceof RedirectView) {
            $view->compile();
        }

        $viewData = [];
        if ($view instanceof CpView) {
            $viewData = [
                'sidebar'    => $view->isSidebarDisabled() ? null : $this->buildNavigation()->buildNavigationView(),
                'body'       => $view->compile(),
                'breadcrumb' => [
                    ee('CP/URL')->make('addons/settings/freeform_next')->compile() => lang('Freeform Next'),
                ],
            ];
        }

        if ($view->getHeading()) {
            $viewData['heading'] = $view->getHeading();
        }

        return $viewData;
    }

    /**
     * @param string $target
     *
     * @return mixed
     */
    protected function getLink($target)
    {
        return ee('CP/URL', 'addons/settings/freeform_next/' . $target);
    }
}
