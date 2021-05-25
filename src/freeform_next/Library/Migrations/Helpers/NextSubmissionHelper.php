<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\EmailField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\RadioGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SelectField;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\ComposerState;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;

class NextSubmissionHelper
{
    const STRICT_MODE = true;

    /** @var array */
    public $errors;

    public function saveSubmission($legacySubmissions, $newFormId)
    {
        $submissionId = null;

        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($newFormId);
        $form      = $formModel->getForm();

        foreach ($legacySubmissions as $legacySubmission) {
            $submission = SubmissionModel::create($form, []);

            $submission->title    = 'Legacy Submission #' . $legacySubmission['legacyId'] . ' (Migrated)';
            $submission->dateCreated = date('Y-m-d H:i:s', $legacySubmission['entryDate']);
            $submission->statusId = StatusRepository::getInstance()->getStatusByHandle($legacySubmission['status'])->id;

            foreach ($form->getLayout()->getFields() as $field) {

                if ($field instanceof NoStorageInterface) {
                    continue;
                }

                $fieldName = $field->getHandle();

                if (!$fieldName) {
                    continue;
                }

                 if (!array_key_exists($fieldName, $legacySubmission)) {
                    throw new \Exception('Cannot find field "' . $fieldName . '"" in legacy submission ' . print_r($legacySubmission, true));
                }

                $value = $this->formatValue($field, $legacySubmission[$fieldName]);

                $submission->setFieldValue($field->getHandle(), $value);
            }

            $submission->save();
        }

        return true;
    }

    private function formatValue(AbstractField $field, $value)
    {
        $formattedValue = $value;

        if ($field instanceof SelectField || $field instanceof RadioGroupField) {
            if (strpos($value, '|~|') !== false) {
                $formattedValue = substr($value, 0, strpos($value, '|~|'));
            }
        }

        if ($field instanceof CheckboxField) {
            $formattedValue = $value === 'y' ? 'yes' : 'no';
        }

        if ($field instanceof EmailField) {
            $formattedValue = [$value];
        }

        if ($field instanceof CheckboxGroupField) {

            $formattedValue = [];
            $valueArray = explode("\n", $value);

            foreach ($valueArray as $arrayValue) {
                $formattedValue[] = substr($arrayValue, 0, strpos($arrayValue, '|~|'));
            }
        }

        if ($field instanceof FileUploadField) {
            $formattedValue = $value;
        }

        return $formattedValue;
    }
}
