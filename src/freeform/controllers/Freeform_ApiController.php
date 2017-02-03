<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Craft;

use GuzzleHttp\Exception\ClientException;
use Solspace\Freeform\Library\Composer\Components\AbstractField;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Library\Helpers\PermissionsHelper;
use Solspace\Freeform\Library\Integrations\TokenRefreshInterface;
use Solspace\Freeform\Library\Session\FormValueContext;

class Freeform_ApiController extends BaseController
{
    /** @var bool */
    protected $allowAnonymous = true;

    /**
     * @throws HttpException
     */
    public function actionForm()
    {
        $this->requirePostRequest();

        $hash   = craft()->request->getRequiredPost(FormValueContext::FORM_HASH_KEY);
        $formId = FormValueContext::getFormIdFromHash($hash);

        $formModel = $this->getFormService()->getFormById($formId);

        if (!$formModel) {
            throw new Exception(
                Craft::t("Form with ID {id} not found", ["id" => $formId])
            );
        }

        $form          = $formModel->getForm();
        $isAjaxRequest = craft()->request->isAjaxRequest;
        if ($form->isValid()) {
            $submissionModel = $form->submit();

            if ($form->isFormSaved()) {
                $postedReturnUrl = craft()->request->getPost(Form::RETURN_URI_KEY);

                $returnUrl = $postedReturnUrl ?: $form->getReturnUrl();
                $returnUrl = craft()->templates->renderString(
                    $returnUrl,
                    [
                        "form"       => $form,
                        "submission" => $submissionModel,
                    ]
                );

                if ($isAjaxRequest) {
                    $this->returnJson(
                        [
                            "success"      => true,
                            "finished"     => true,
                            "returnUrl"    => $returnUrl,
                            "submissionId" => $submissionModel ? $submissionModel->id : null,
                        ]
                    );
                } else {
                    $this->redirect($returnUrl);
                }
            } else if ($isAjaxRequest) {
                $this->returnJson(
                    [
                        "success"  => true,
                        "finished" => false,
                    ]
                );
            }
        } else {
            if ($isAjaxRequest) {
                $fieldErrors = [];

                foreach ($form->getLayout()->getFields() as $field) {
                    if ($field->hasErrors()) {
                        $fieldErrors[$field->getHandle()] = $field->getErrors();
                    }
                }

                return $this->returnJson(
                    [
                        "success"  => false,
                        "finished" => false,
                        "errors"   => $fieldErrors,
                    ]
                );
            }
        }
    }

    /**
     * GET fields
     *
     * @return string
     */
    public function actionFields()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_FORMS_ACCESS);
        $this->returnJson($this->getFieldsService()->getAllFields(false));
    }

    /**
     * GET notifications
     *
     * @return string
     */
    public function actionNotifications()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_FORMS_ACCESS);

        $this->returnJson($this->getNotificationService()->getAllNotifications(false));
    }

    /**
     * GET mailing lists
     *
     * @return string
     */
    public function actionMailingLists()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_FORMS_ACCESS);

        $mailingLists = $this->getMailingListsService()->getAllIntegrations();
        foreach ($mailingLists as $integration) {
            $integration->setForceUpdate(true);
        }

        $this->returnJson($mailingLists);
    }

    /**
     * GET integrations
     *
     * @return string
     */
    public function actionCrmIntegrations()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_FORMS_ACCESS);

        $crmIntegrations = $this->getCrmService()->getAllIntegrations();
        foreach ($crmIntegrations as $integration) {
            $integration->setForceUpdate(true);

            try {
                $integration->getFields();
            } catch (ClientException $e) {
                if ($integration instanceof TokenRefreshInterface) {
                    try {
                        if ($integration->refreshToken() && $integration->isAccessTokenUpdated()) {
                            $this->getCrmService()->updateAccessToken($integration);

                            $integration->getFields();
                        }
                    } catch (\Exception $e) {
                        $this->returnErrorJson($e->getMessage());
                    }
                } else {
                    $this->returnErrorJson($e->getMessage());
                }
            } catch (\Exception $e) {
                $this->returnErrorJson($e->getMessage());
            }
        }

        $this->returnJson($crmIntegrations);
    }

    /**
     * GET fields
     *
     * @return string
     */
    public function actionFormTemplates()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_FORMS_ACCESS);

        $this->returnJson($this->getSettingsService()->getCustomFormTemplates());
    }

    /**
     * POST a field, and save it to the database
     */
    public function actionQuickCreateField()
    {
        $this->requirePostRequest();

        /** @var HttpRequestService $request */
        $request = craft()->request;
        $errors  = [];

        $label  = $request->getPost("label");
        $handle = $request->getPost("handle");
        $type   = $request->getPost("type");

        if (!$label) {
            $errors[] = Craft::t("Label is required");
        }

        if (!$handle) {
            $errors[] = Craft::t("Handle is required");
        }

        $allowedFieldTypes = array_keys(AbstractField::getFieldTypes());
        if (!$type || !in_array($type, $allowedFieldTypes)) {
            $errors[] = Craft::t(
                "Type {type} is not allowed. Allowed types are ({allowedTypes})",
                [
                    "type"         => $type,
                    "allowedTypes" => implode(", ", $allowedFieldTypes),
                ]
            );
        }

        $field         = Freeform_FieldModel::create();
        $field->label  = $label;
        $field->handle = $handle;
        $field->type   = $type;

        if (empty($errors) && $this->getFieldsService()->save($field)) {
            $this->returnJson(["success" => true]);
        } else {
            $fieldErrors = $field->getErrors();
            $errors      = [];
            array_walk_recursive(
                $fieldErrors,
                function ($array) use (&$errors) {
                    $errors[] = $array;
                }
            );

            $this->returnJson(["success" => false, "errors" => $errors]);
        }
    }

    /**
     * POST a field, and save it to the database
     */
    public function actionQuickCreateNotification()
    {
        $this->requirePostRequest();

        /** @var HttpRequestService $request */
        $request = craft()->request;
        $errors  = [];

        $name   = $request->getPost("name");
        $handle = $request->getPost("handle");

        if (!$name) {
            $errors[] = Craft::t("Name is required");
        }

        if (!$handle) {
            $errors[] = Craft::t("Handle is required");
        }

        $notification         = Freeform_NotificationModel::create();
        $notification->name   = $name;
        $notification->handle = $handle;

        if (empty($errors) && $this->getNotificationService()->save($notification)) {
            $this->returnJson(["success" => true, "id" => $notification->id]);
        } else {
            $fieldErrors = $notification->getErrors();
            $errors      = [];
            array_walk_recursive(
                $fieldErrors,
                function ($array) use (&$errors) {
                    $errors[] = $array;
                }
            );

            $this->returnJson(["success" => false, "errors" => $errors]);
        }
    }

    /**
     * Returns the data needed to display a Submissions chart.
     */
    public function actionGetSubmissionData()
    {
        PermissionsHelper::requirePermission(PermissionsHelper::PERMISSION_SUBMISSIONS_ACCESS);

        // Required for Dashboard widget, unnecessary for Entries Index view
        $source = craft()->request->getPost('source');
        $formId = null;
        if ($source && strpos($source, "form:") === 0) {
            $formId = (int)substr($source, 5);
        }

        $startDateParam = craft()->request->getRequiredPost('startDate');
        $endDateParam   = craft()->request->getRequiredPost('endDate');

        $startDate = DateTime::createFromString($startDateParam, craft()->timezone);
        $endDate   = DateTime::createFromString($endDateParam, craft()->timezone);
        $endDate->modify('+1 day');

        $intervalUnit = ChartHelper::getRunChartIntervalUnit($startDate, $endDate);

        // Prep the query
        $criteria        = craft()->elements->getCriteria(Freeform_SubmissionModel::ELEMENT_TYPE);
        $criteria->limit = null;

        // Don't use the search
        $criteria->search = null;

        $query = craft()
            ->elements
            ->buildElementsQuery($criteria)
            ->select('COUNT(*) as value');

        if ($formId != 0) {
            $query->andWhere(
                'fs.formId = :formId',
                [':formId' => $formId]
            );
        }

        // Get the chart data table
        $dataTable = ChartHelper::getRunChartDataFromQuery(
            $query,
            $startDate,
            $endDate,
            'fs.dateCreated',
            [
                'intervalUnit' => $intervalUnit,
                'valueLabel'   => Craft::t('Submissions'),
                'valueType'    => 'number',
            ]
        );

        // Get the total submissions
        $total = 0;

        foreach ($dataTable['rows'] as $row) {
            $total = $total + $row[1];
        }

        $this->returnJson(
            [
                'dataTable' => $dataTable,
                'total'     => $total,
                'totalHtml' => $total,

                'formats'          => ChartHelper::getFormats(),
                'orientation'      => craft()->locale->getOrientation(),
                'scale'            => $intervalUnit,
                'localeDefinition' => [],
            ]
        );
    }

    /**
     * Mark the tutorial as finished
     */
    public function actionFinishTutorial()
    {
        $this->returnJson(["success" => $this->getSettingsService()->finishTutorial()]);
    }

    /**
     * @return Freeform_FormsService
     */
    private function getFormService()
    {
        return craft()->freeform_forms;
    }

    /**
     * @return Freeform_FieldsService
     */
    private function getFieldsService()
    {
        return craft()->freeform_fields;
    }

    /**
     * @return Freeform_SubmissionsService
     */
    private function getSubmissionService()
    {
        return craft()->freeform_submissions;
    }

    /**
     * @return Freeform_MailerService
     */
    private function getMailerService()
    {
        return craft()->freeform_mailer;
    }

    /**
     * @return Freeform_MailingListsService
     */
    private function getMailingListsService()
    {
        return craft()->freeform_mailingLists;
    }

    /**
     * @return Freeform_CrmService
     */
    private function getCrmService()
    {
        return craft()->freeform_crm;
    }

    /**
     * @return Freeform_NotificationsService
     */
    private function getNotificationService()
    {
        return craft()->freeform_notifications;
    }

    /**
     * @return Freeform_FilesService
     */
    private function getFileService()
    {
        return craft()->freeform_files;
    }

    /**
     * @return Freeform_SettingsService
     */
    private function getSettingsService()
    {
        return craft()->freeform_settings;
    }
}
