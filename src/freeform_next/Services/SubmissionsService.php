<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\ObscureValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Helpers\TwigHelper;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionsService implements SubmissionHandlerInterface
{
    public function storeSubmission(Form $form, array $fields)
    {
        $savableFields = [];
        foreach ($fields as $field) {
            if ($field instanceof NoStorageInterface) {
                continue;
            }

            $columnName = SubmissionModel::getFieldColumnName($field->getId());
            $value      = $field->getValue();

            // Since the value is obfuscated, we have to get the real value
            if ($field instanceof ObscureValueInterface) {
                $value = $field->getActualValue($value);
            } else if ($field instanceof StaticValueInterface) {
                if (!empty($value)) {
                    $value = $field->getStaticValue();
                }
            }

            if ($field instanceof MultipleValueInterface) {
                $value = json_encode($value);
            }

            $savableFields[$columnName] = $value;
        }

        $insertData = [
            'siteId'   => ee()->config->item('site_id'),
            'formId'   => $form->getId(),
            'statusId' => $form->getDefaultStatus(),
        ];

        $fieldsByHandle      = $form->getLayout()->getFieldsByHandle();
        $insertData['title'] = TwigHelper::renderString(
            $form->getSubmissionTitleFormat(),
            array_merge(
                $fieldsByHandle,
                [
                    'dateCreated' => new \DateTime(),
                    'form'        => $form,
                ]
            )
        );

        foreach ($savableFields as $fieldName => $value) {
            $insertData[$fieldName] = $value;
        }

        ee()->db
            ->insert(
                SubmissionModel::TABLE,
                $insertData
            );

        $submissionId = ee()->db->insert_id();

        if ($submissionId) {
            $this->finalizeFormFiles($form);

            return $submissionId;
        }

        return null;
    }

    public function finalizeFormFiles(Form $form)
    {
        // TODO: Implement finalizeFormFiles() method.
    }
}
