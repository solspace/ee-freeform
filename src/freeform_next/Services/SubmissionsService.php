<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\TemplateHelper;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionsService implements SubmissionHandlerInterface
{
    /**
     * @param Form            $form
     * @param AbstractField[] $fields
     *
     * @return SubmissionModel|null
     */
    public function storeSubmission(Form $form, array $fields)
    {
        $savableFields = [];
        foreach ($fields as $field) {
            if ($field instanceof NoStorageInterface) {
                continue;
            }

            $value = $field->getValue();

            // Since the value is obfuscated, we have to get the real value
            if ($field instanceof ObscureValueInterface) {
                $value = $field->getActualValue($value);
            } else if ($field instanceof StaticValueInterface) {
                if (!empty($value)) {
                    $value = $field->getStaticValue();
                }
            }

            $savableFields[$field->getHandle()] = $value;
        }

        $submission = SubmissionModel::create($form, $savableFields);

        foreach ($savableFields as $handle => $value) {
            $submission->setFieldValue($handle, $value);
        }

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_BEFORE_SAVE, $submission, true)) {
            return null;
        }

        $submission->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_AFTER_SAVE, $submission, true);

        if ($submission->id) {
            $this->finalizeFormFiles($form);

            return $submission;
        }

        return null;
    }

    /**
     * @param Form $form
     */
    public function finalizeFormFiles(Form $form)
    {
        $assetIds = [];

        foreach ($form->getLayout()->getFileUploadFields() as $field) {
            $fieldAssetIds = $field->getValue();

            foreach ($fieldAssetIds as $assetId) {
                if (!in_array($assetId, $assetIds)) {
                    $assetIds[] = $assetId;
                }
            }
        }

        if (empty($assetIds)) {
            return;
        }

        ee()->db
            ->where_in('assetId', $assetIds)
            ->delete('freeform_next_unfinalized_files');
    }

    /**
     * Add a session flash variable that the form has been submitted
     *
     * @param Form $form
     */
    public function markFormAsSubmitted(Form $form)
    {
        ee()->session->set_flashdata(Form::SUBMISSION_FLASH_KEY . $form->getId(), true);
    }

    /**
     * Check for a session flash variable for form submissions
     *
     * @param Form $form
     *
     * @return bool
     */
    public function wasFormFlashSubmitted(Form $form)
    {
        return ee()->session->flashdata(Form::SUBMISSION_FLASH_KEY . $form->getId()) === true;
    }
}
