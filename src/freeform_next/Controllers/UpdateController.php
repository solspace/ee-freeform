<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Services\UpdateService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class UpdateController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $updateService = new UpdateService();

        $view = new CpView(
            'update/index',
            [
                'updates' => $updateService->getInstallableUpdates(),
                'format'  => ee()->config->item('date_format'),
            ]
        );

        $view->setHeading('Updates');

        return $view;
    }
}
