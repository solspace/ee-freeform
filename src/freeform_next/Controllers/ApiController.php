<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class ApiController extends Controller
{
    const TYPE_FIELDS        = 'fields';
    const TYPE_NOTIFICATIONS = 'notifications';

    /**
     * @param string $type
     * @param array  $args
     *
     * @return View
     * @throws FreeformException
     */
    public function handle($type, $args = [])
    {
        switch ($type) {
            case self::TYPE_FIELDS:
                return $this->fields();

            case self::TYPE_NOTIFICATIONS:
                return $this->notifications($args);
        }

        throw new FreeformException(sprintf('"%s" action is not present in the API controller', $type));
    }

    /**
     * @return View
     */
    public function fields()
    {
        $view = new AjaxView();

        if (!empty($_POST)) {
            $field = $this->getFieldController()->save();

            $view->addVariable('success', true);

            return $view;
        }

        $view->setVariables(FieldRepository::getInstance()->getAllFields());

        return $view;
    }

    /**
     * @param array $args
     *
     * @return View
     */
    public function notifications($args = [])
    {
        $view = new AjaxView();

        if (isset($args[1]) && $args[1] === 'create') {
            $notification         = NotificationModel::create();
            $notification->name   = ee()->input->post('name', true);
            $notification->handle = ee()->input->post('handle', true);
            $notification->save();

            $view->addVariable('success', true);

            return $view;
        }

        $view->setVariables(NotificationRepository::getInstance()->getAllNotifications());

        return $view;
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
