<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;
use Stringy\Stringy;

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
            $this->getFieldController()->save();

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
    public function notifications(array $args = [])
    {
        $view = new AjaxView();

        if (isset($args[1]) && $args[1] === 'create') {
            $settings    = SettingsRepository::getInstance()->getOrCreate();

            $errors = [];

            $name      = ee()->input->post('name');
            $handle    = ee()->input->post('handle');
            $forceFile = ee()->input->post('force_file', false);

            $isDbStorage = $forceFile ? false : $settings->isDbEmailTemplateStorage();

            if (!$name) {
                $errors[] = lang('Name is required');
            }

            if (!$handle && $isDbStorage) {
                $errors[] = lang('Handle is required');
            }

            if (empty($errors)) {
                if ($isDbStorage) {
                    $notification         = NotificationModel::create();
                    $notification->name   = $name;
                    $notification->handle = $handle;
                    $notification->save();
                } else {
                    $templateDirectory = $settings->getAbsoluteEmailTemplateDirectory();
                    $templateName      = (string) Stringy::create($name)->underscored();
                    $extension         = '.html';

                    $templatePath = $templateDirectory . '/' . $templateName . $extension;
                    if (file_exists($templatePath)) {
                        $errors[] = (new EETranslator())->translate(
                            "Template '{name}' already exists",
                            ['name' => $templateName . $extension]
                        );
                    } else {
                        try {
                            file_put_contents($templatePath, $settings->getEmailTemplateContent());
                            $notification = NotificationModel::createFromTemplate($templatePath);
                        } catch (FreeformException $exception) {
                            $errors[] = $exception->getMessage();
                        }
                    }
                }
            }

            if (empty($errors)) {
                $view->addVariable('success', true);
                $view->addVariable('id', $notification->id);
            } else {
                $view->addVariable('success', false);
                $view->addVariable('errors', $errors);
            }

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
