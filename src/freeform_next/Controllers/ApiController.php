<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
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
use Stringy\Stringy;

class ApiController extends Controller
{
    const TYPE_FIELDS            = 'fields';
    const TYPE_NOTIFICATIONS     = 'notifications';
    const TYPE_RESET_SPAM        = 'reset_spam';
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

        $fileName = sprintf('%s_submissions_%s.csv', $form->getHandle(), date('Y-m-d_H-i'));

        $headers = $data = [];

        $preferences = SubmissionPreferencesRepository::getInstance()->getOrCreate(
            $form,
            ee()->session->userdata('member_id')
        );

        $output = fopen('php://output', 'w');

        $layout = $preferences->getLayout();

        foreach ($layout as $item) {
            if (!$item->isChecked()) {
                continue;
            }

            $headers[] = $item->getLabel();
        }

        fputcsv($output, $headers);

        $limit = 20;
        $offset = 0;

        $submissionRepository = SubmissionRepository::getInstance();
        $submissions = $submissionRepository->getAllSubmissionsFor($form, [], 'id', 'asc', $limit, $offset);

        while (!empty($submissions)) {
            foreach ($submissions as $submission) {
                $row = [];
                foreach ($layout as $item) {
                    if (!$item->isChecked()) {
                        continue;
                    }

                    if (is_numeric($item->getId())) {
                        $row[] = $submission->getFieldValueAsString($item->getHandle());
                    } else {
                        $row[] = $submission->{$item->getHandle()};
                    }
                }

                fputcsv($output, $row);
            }

            $offset += $limit;
            $submissions = $submissionRepository->getAllSubmissionsFor(
                $form,
                [],
                'id',
                'asc',
                $limit,
                $offset
            );
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
}
