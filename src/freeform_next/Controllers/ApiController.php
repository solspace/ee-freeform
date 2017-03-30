<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class ApiController extends Controller
{
    const TYPE_FIELDS = 'fields';

    /**
     * @param string $type
     *
     * @return View
     * @throws FreeformException
     */
    public function handle($type)
    {
        switch ($type) {
            case self::TYPE_FIELDS:
                return $this->fields();
        }

        throw new FreeformException(sprintf('"%s" action is not present in the API controller', $type));
    }

    /**
     * @return View
     */
    public function fields()
    {
        if (!empty($_POST)) {
            return $this->getFieldController()->save();
        }

        $view = new AjaxView();
        $view->setVariables(FieldRepository::getInstance()->getAllFields());

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
