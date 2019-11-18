<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionPreferencesRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\FileDownloadView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Stringy\Stringy;

class ApiController extends Controller
{
    const TYPE_FIELDS            = 'fields';
    const TYPE_NOTIFICATIONS     = 'notifications';
    const TYPE_RESET_SPAM        = 'reset_spam';
    const TYPE_DUPLICATE         = 'duplicate';
    const TYPE_SUBMISSION_LAYOUT = 'submission_layout';
    const TYPE_SUBMISSION_EXPORT = 'submission_export';

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

            case self::TYPE_RESET_SPAM:
                return $this->resetSpam();

            case self::TYPE_SUBMISSION_LAYOUT:
                return $this->submissionLayout();

            case self::TYPE_DUPLICATE:
                return $this->duplicate();

            case self::TYPE_SUBMISSION_EXPORT:
                return $this->submissionExport($args);
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
            try {
                $model = $this->getFieldController()->save();
                FreeformHelper::get('validate', $model);

                $view->addVariable('success', true);
            } catch (\Exception $e) {
                $view->addError($e->getMessage());
            }

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
            $settings = SettingsRepository::getInstance()->getOrCreate();

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
     * @return AjaxView
     */
    public function duplicate()
    {
        $formId = ee()->input->post('formId');

        /** @var FormModel $form */
        $form = FormRepository::getInstance()->getFormById($formId);
        $view = new AjaxView();

        if (!$form) {
            $view->addError('Form not found');
            return $view;
        } else {
            $newForm = $this->createNewForm($form);
        }

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_SAVE, $newForm, true)) {
            $view->addError(ExtensionHelper::getLastCallData());
            return $view;
        }

        try {
            $newForm = $this->setNewHandle($newForm);
            $newForm->save();

            if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_AFTER_SAVE, $newForm, true)) {
                return $view;
            }

            $view->addVariable('success', true);
        } catch (\Exception $e) {
            $view->addError($e->getMessage());
        }

        return $view;
    }

    /**
     * @return AjaxView
     */
    public function resetSpam()
    {
        $formId = ee()->input->post('formId');

        $form = FormRepository::getInstance()->getFormById($formId);
        $view = new AjaxView();

        if (!$form) {
            $view->addError('Form not found');
        } else {
            $form->spamBlockCount = 0;
            $form->save();

            $view->addVariable('success', true);
        }

        return $view;
    }

    /**
     * @return AjaxView
     */
    public function submissionLayout()
    {
        $formId   = ee()->input->post('formId');
        $data     = ee()->input->post('data');
        $memberId = ee()->session->userdata('member_id');

        $form = FormRepository::getInstance()->getFormById($formId)->getForm();
        $view = new AjaxView();

        if (!$form) {
            $view->addError('Form not found');
        } else {
            $layout = [];
            foreach ($data as $item) {
                $layout[] = SubmissionPreferenceSetting::createFromArray($item);
            }

            $prefs           = SubmissionPreferencesRepository::getInstance()->getOrCreate($form, $memberId);
            $prefs->settings = $layout;
            $prefs->save();

            $view->addVariable('success', true);
        }

        return $view;
    }

    /**
     * @param array $args
     *
     * @return View
     * @throws FreeformException
     */
    public function submissionExport(array $args = [])
    {
        $formId = @$args[1];

        $form = FormRepository::getInstance()->getFormById($formId)->getForm();
        if (!$form) {
            throw new FreeformException('Form not found');
        }

        $isRemoveNewlines = (bool) SettingsRepository::getInstance()->getOrCreate()->removeNewlines;
        $fileName         = sprintf('%s_submissions_%s.csv', $form->getHandle(), date('Y-m-d_H-i'));

        $headers = $data = [];

        $preferences = SubmissionPreferencesRepository::getInstance()->getOrCreate(
            $form,
            ee()->session->userdata('member_id')
        );

        ob_start();
        $output = fopen('php://output', 'w');

        $layout = $preferences->getLayout();

        foreach ($layout as $item) {
            if (!$item->isChecked()) {
                continue;
            }

            $headers[] = $item->getLabel();
        }

        fputcsv($output, $headers);

        $limit  = 20;
        $offset = 0;

        $attributes = new SubmissionAttributes($form);
        $attributes
            ->setLimit($limit)
            ->setOffset($offset);

        $submissionRepository = SubmissionRepository::getInstance();
        $submissions          = $submissionRepository->getAllSubmissionsFor($attributes);

        while (!empty($submissions)) {
            foreach ($submissions as $submission) {
                $row = [];
                foreach ($layout as $item) {
                    if (!$item->isChecked()) {
                        continue;
                    }

                    if (is_numeric($item->getId())) {
                        $value = $submission->getFieldValueAsString($item->getHandle());
                    } else {
                        $value = $submission->{$item->getHandle()};
                    }

                    if ($isRemoveNewlines) {
                        $value = trim(preg_replace('/\s+/', ' ', $value));
                    }

                    $row[] = $value;
                }

                fputcsv($output, $row);
            }

            $attributes->setOffset($attributes->getOffset() + $limit);
            $submissions = $submissionRepository->getAllSubmissionsFor($attributes);
        }

        fclose($output);

        $content = ob_get_clean();

        return new FileDownloadView($fileName, $content);
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

    private function setNewHandle($newForm)
    {
        $newHandleBase = $newForm->handle;

        if (strpos($newForm->handle, '_copy_') !== false) {
            $newHandleBase = substr($newForm->handle, 0, strpos($newForm->handle, "_copy"));
        }

        $newHandle = $newHandleBase . '_copy_' . time();

        $composer = $newForm->getComposer();
        $composerJson = $composer->getComposerStateJSON();
        $composerState = json_decode($composerJson, true);
        $composerState['composer']['properties']['form']['handle'] = $newHandle;
        $newForm->layoutJson = json_encode($composerState);
        $newForm->setProperty('handle', $newHandle);

        return $newForm;
    }

    private function createNewForm($form)
    {
        $reflectionClass = new \ReflectionClass(FormModel::class);
        $properties = $reflectionClass->getProperties();
        $newForm = FormModel::create();

        foreach ($properties as $varName => $varValue) {
            if (in_array($varValue->name, [
                'id',
                'siteId',
                'name',
                'handle',
                'spamBlockCount',
                'description',
                'layoutJson',
                'returnUrl',
                'defaultStatus',
                'legacyId',
                'dateCreated',
                'dateUpdated',
            ])) {
                $newForm->setProperty($varValue->name, $this->getProtectedProperty($varValue->name, $form));
            }
        }

        $newForm->setId(null);

        return $newForm;
    }

    private function getProtectedProperty($property, $object)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
