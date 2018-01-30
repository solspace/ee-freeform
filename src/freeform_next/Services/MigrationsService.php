<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFieldHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFormHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFormNotificationsHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFormStatusHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicSubmissionHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\NextFieldHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\NextFormHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\NextFormNotificationHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\NextFormStatusHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Helpers\NextSubmissionHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\MigrationResultObject;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class MigrationsService
{
    const STATUS__FIELDS = 'status-fields';
    const STATUS__FORM_STATUSES = 'form-statuses';
    const STATUS__FORM_NOTIFICATIONS = 'form-notifications';
    const STATUS__FORMS = 'status-forms';
    const STATUS__SUBMISSIONS = 'status-submissions';

    /** @var MigrationResultObject */
    public $result;

    /** @var ClassicSubmissionHelper */
    public $classicSubmissionHelper;

    public function isFreeformClassicMigrateable()
    {
        if ($this->isClassicFreeformInstalled() && $this->isFreeformNextFreshlyInstalled()) {
            return true;
        }

        return false;
    }

    public function isClassicFreeformInstalled()
    {
        $installed = ee()->addons->get_installed('modules', TRUE);

        if (array_key_exists('freeform', $installed)) {
            return true;
        }

        return false;
    }

    public function isFreeformNextFreshlyInstalled()
    {
        $forms = FormRepository::getInstance()->getAllForms();

        if (empty($forms)) {
            return true;
        }

        return false;
    }

    public function migrateFreeformClassicFields()
    {
        $nextFieldHelper = $this->getNextFieldHelper();
        $nextFieldHelper->deleteAllFields();
        $classicFields = $this->getClassicFields();

        if (!$classicFields) {
            $this->result->success = false;
            $this->result->addError('No classic fields found');

            return false;
        }

        foreach ($classicFields as $classicField) {
            $this->saveNextField($classicField);
        }

        $this->result->success = true;

        return true;
    }


    public function migrateFreeformClassicFormStatuses()
    {
        $classicStatuses = $this->getClassicFormStatuses();

        if (!$classicStatuses) {
            $this->result->success = false;
            $this->result->addError('No classic Form Statuses found');

            return false;
        }

        foreach ($classicStatuses as $classicStatusHandle => $classicStatusName) {
            $this->saveNextFormStatus($classicStatusHandle, $classicStatusName);
        }

        $this->result->success = true;

        return true;
    }

    public function migrateFreeformClassicFormNotifications()
    {
        $classicNotifications = $this->getClassicFormNotifications();

        if (!$classicNotifications) {
            $this->result->success = false;
            $this->result->addError('No classic Form Notifications found');

            return false;
        }

        foreach ($classicNotifications as $classicNotification) {
            $this->saveNextFormNotification($classicNotification);
        }

        $this->result->success = true;

        return true;
    }

    public function migrateFreeformClassicForms()
    {
        $classicForms = $this->getClassicForms();

        if (!$classicForms) {
            $this->result->success = false;
            $this->result->addError('No classic Forms found');

            return false;
        }

        foreach ($classicForms as $classicForm) {
            $this->saveNextForms($classicForm);
        }

        $this->result->success = true;

        return true;
    }

    public function migrateFreeformClassicSubmissions($formId, $page)
    {
        $classicSubmissions = $this->getClassicSubmissions($formId, $page);

        if (!$classicSubmissions) {
            $this->result->success = false;
            $this->result->addError('No classic Submissions found');

            return false;
        }

        foreach ($classicSubmissions as $submissionFormId => $classicSubmission) {
            $this->saveNextSubmission($classicSubmission, $submissionFormId );
        }

        $this->result->success = true;

        return true;
    }

    public function getClassicFields()
    {
        $classicFieldHelper = $this->getClassicFieldHelper();

        return $classicFieldHelper->getClassicFields();
    }

    public function saveNextField($classicField)
    {
        $nextFieldHelper = $this->getNextFieldHelper();

        return $nextFieldHelper->saveField($classicField);
    }

    public function getClassicFormStatuses()
    {
        $classicFormStatusHelper = $this->getClassicFormStatusHelper();

        return $classicFormStatusHelper->getClassicFormStatuses();
    }

    public function getClassicFormNotifications()
    {
        $classicFormNotificationsHelper = $this->getClassicFormNotificationsHelper();

        return $classicFormNotificationsHelper->getClassicFormNotifications();
    }

    public function saveNextFormStatus($handle, $name)
    {
        $nextFormStatusHelper = $this->getNextFormStatusHelper();

        return $nextFormStatusHelper->saveStatus($handle, $name);
    }

    public function saveNextFormNotification($notification)
    {
        $nextFormNotificationHelper = $this->getNextFormNotificationHelper();

        return $nextFormNotificationHelper->saveNotification($notification);
    }

    public function getClassicForms()
    {
        $classicFormHelper = $this->getClassicFormHelper();

        return $classicFormHelper->getClassicForms();
    }

    public function saveNextForms($classicForm)
    {
        $nextFormHelper = $this->getNextFormHelper();

        return $nextFormHelper->saveForm($classicForm);
    }

    public function getClassicSubmissions($formId, $page)
    {
        $this->classicSubmissionHelper = $this->getClassicSubmissionHelper();

        return $this->classicSubmissionHelper->getClassicSubmissions($formId, $page);
    }

    public function saveNextSubmission($classicSubmission, $formId)
    {
        $nextFormHelper = $this->getNextSubmissionHelper();

        return $nextFormHelper->saveSubmission($classicSubmission, $formId);
    }

    public function getStages()
    {
        return [
            self::STATUS__FIELDS,
            self::STATUS__FORM_STATUSES,
            self::STATUS__FORM_NOTIFICATIONS,
            self::STATUS__FORMS,
            self::STATUS__SUBMISSIONS,
        ];
    }

    public function getStagesInfo()
    {
        return [
            self::STATUS__FIELDS => [
                'type-name' => self::STATUS__FIELDS,
                'in-progress-text' => 'Migrating Fields',
            ],
            self::STATUS__FORM_STATUSES => [
                'type-name' => self::STATUS__FORM_STATUSES,
                'in-progress-text' => 'Migrating Form Statuses',
            ],
            self::STATUS__FORM_NOTIFICATIONS => [
                'type-name' => self::STATUS__FORM_NOTIFICATIONS,
                'in-progress-text' => 'Migrating Form Notifications',
            ],
            self::STATUS__FORMS => [
                'type-name' => self::STATUS__FORMS,
                'in-progress-text' => 'Migrating Forms',
            ],
            self::STATUS__SUBMISSIONS => [
                'type-name' => self::STATUS__SUBMISSIONS,
                'in-progress-text' => 'Migrating Submissions',
            ],
        ];
    }

    public function getStageInfo($stageName)
    {
        return $this->getStagesInfo()[$stageName];
    }

    public function getNextStageInfo($currentStage)
    {
        foreach ($this->getStages() as $key => $stage) {
            if ($stage === $currentStage) {
                if (array_key_exists($key+1, $this->getStages())) {
                    return $this->getStagesInfo()[$this->getStages()[$key+1]];
                }
            }
        }

        return false;
    }

    public function stageExists($stage)
    {
        if (!in_array($stage, $this->getStages())) {
            return false;
        }

        return true;
    }

    public function getFirstStageInfo()
    {
        return $this->getStagesInfo()[$this->getStages()[0]];
    }

    /**
     * @param $stage
     * @param null $formId
     * @param null $page
     * @return MigrationResultObject
     */
    public function runStage($stage, $formId = null, $page = null)
    {
        $this->result = new MigrationResultObject();

        if (!$this->stageExists($stage)) {
            $this->result->errors[] = 'Stage does not exist';

            return $this->result;
        }

        switch ($stage) {
            case self::STATUS__FIELDS:
                $this->migrateFreeformClassicFields();
                break;
            case self::STATUS__FORM_STATUSES:
                $this->migrateFreeformClassicFormStatuses();
                break;
            case self::STATUS__FORM_NOTIFICATIONS:
                $this->migrateFreeformClassicFormNotifications();
                break;
            case self::STATUS__FORMS:
                $this->migrateFreeformClassicForms();
                break;
            case self::STATUS__SUBMISSIONS:
                $this->migrateFreeformClassicSubmissions($formId, $page);
                $this->result->submissionsInfo = $this->getSubmissionInfo();
                break;
        }

        $this->result->finished = $this->isFinished($stage);

        return $this->result;
    }

    private function getSubmissionInfo()
    {
        if (!$this->classicSubmissionHelper) {
            return [];
        }

        $formsCount = $this->classicSubmissionHelper->getFormsCount();
        $nextFormId = $this->classicSubmissionHelper->formId;
        $formFinished = $this->classicSubmissionHelper->finished;
        $nextPage = (int) ($this->classicSubmissionHelper->page + 1);
        $pageCount = $this->classicSubmissionHelper->formPagesCount;

        if ($formFinished === true) {
            $nextForm = $this->classicSubmissionHelper->getNextForm($this->classicSubmissionHelper->formId);

            if ($nextForm) {
                $nextFormId = (int) $nextForm->id;
            } else {
                $nextFormId = false;
            }

            $nextPage = 1;
        }

        $result = [
            'formsCount' => $formsCount,
            'nextForm' => $nextFormId,
            'finishedForm' => $formFinished,
            'nextPage' => $nextPage,
            'pageCount' => $pageCount,
        ];

        return $result;
    }

    private function isFinished($stage)
    {
        if (!$this->result->isMigrationSuccessful()) {
            return false;
        }

        if ($stage === self::STATUS__SUBMISSIONS) {
            return $this->result->submissionsInfo['nextForm'] === false;
        }

        if (!$this->getNextStageInfo($stage)) {
            return true;
        }

        return false;
    }

    /**
     * @return ClassicSubmissionHelper
     */
    private function getClassicSubmissionHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ClassicSubmissionHelper();
        }

        return $instance;
    }

    /**
     * @return NextSubmissionHelper
     */
    private function getNextSubmissionHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NextSubmissionHelper();
        }

        return $instance;
    }

    /**
     * @return ClassicFormHelper
     */
    private function getClassicFormHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ClassicFormHelper();
        }

        return $instance;
    }

    /**
     * @return ClassicFormStatusHelper
     */
    private function getClassicFormStatusHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ClassicFormStatusHelper();
        }

        return $instance;
    }

    /**
     * @return ClassicFormNotificationsHelper
     */
    private function getClassicFormNotificationsHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ClassicFormNotificationsHelper();
        }

        return $instance;
    }

    /**
     * @return ClassicFieldHelper
     */
    private function getClassicFieldHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ClassicFieldHelper();
        }

        return $instance;
    }

    /**
     * @return NextFieldHelper
     */
    private function getNextFieldHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NextFieldHelper();
        }

        return $instance;
    }

    /**
     * @return NextFormStatusHelper
     */
    private function getNextFormStatusHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NextFormStatusHelper();
        }

        return $instance;
    }

    /**
     * @return NextFormNotificationHelper
     */
    private function getNextFormNotificationHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NextFormNotificationHelper();
        }

        return $instance;
    }

    /**
     * @return NextFormHelper
     */
    private function getNextFormHelper()
    {
        static $instance;

        if (null === $instance) {
            $instance = new NextFormHelper();
        }

        return $instance;
    }
}
