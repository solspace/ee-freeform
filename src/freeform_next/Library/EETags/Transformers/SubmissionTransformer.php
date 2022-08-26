<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionTransformer
{
    /** @var array */
    private static $fieldsByFormId;

    /**
     * @param SubmissionModel           $model
     * @param int                       $count
     * @param int                       $totalResults
     * @param int                       $absoluteTotal
     * @param SubmissionAttributes|null $attributes
     *
     * @return array
     */
    public function transformSubmission(
        SubmissionModel $model,
        $count = 1,
        $totalResults = 1,
        $absoluteTotal = 1,
        SubmissionAttributes $attributes = null
    ) {
        $prefix        = 'submission:';
        $absoluteCount = $count;

        if (null !== $attributes && null !== $attributes->getLimit()) {
            $absoluteCount = $attributes->getOffset() + $count;
        }

        $attachmentCount = 0;
        foreach ($this->getFieldList($model) as $field) {
            if ($field instanceof FileUploadField)
            {
                $fieldValue = $model->getFieldValue($field->getHandle()) ?: [];
                if(count($fieldValue))
                {
                    $attachmentCount += count($fieldValue);
                }
            }
        }

        $data = [
            $prefix . 'id'               => $model->id,
            $prefix . 'title'            => $model->title,
            $prefix . 'token'            => $model->token,
            $prefix . 'date'             => $model->dateCreated,
            $prefix . 'status_id'        => $model->statusId,
            $prefix . 'status'           => $model->statusName,
            $prefix . 'status_name'      => $model->statusName,
            $prefix . 'status_handle'    => $model->statusHandle,
            $prefix . 'status_color'     => $model->statusColor,
            $prefix . 'count'            => $count,
            $prefix . 'total_results'    => $totalResults,
            $prefix . 'absolute_count'   => $absoluteCount,
            $prefix . 'absolute_results' => $absoluteTotal,
            $prefix . 'attachment_count' => $attachmentCount,
            $prefix . 'fields'           => $this->getFields($model),
        ];

        foreach ($this->getSeparateFieldInfo($model) as $fieldInfo) {
            $data = array_merge($data, $fieldInfo);
        }

        return $data;
    }

    /**
     * @param SubmissionModel $model
     * @param string          $prefix
     *
     * @return array
     */
    private function getFields(SubmissionModel $model, $prefix = 'field:')
    {
        $fieldTransformer = new FieldTransformer();
        $data             = [];
        /** @var AbstractField $field */
        foreach ($this->getFieldList($model) as $field) {
            if ($field instanceof NoStorageInterface) {
                continue;
            }

            $value = $model->getFieldValue($field->getHandle());

            if ($field instanceof TableField) {
                $field->setValue($value);
            }

            $data[] = $fieldTransformer->transformField($field, $value, $prefix);
        }

        return $data;
    }

    /**
     * @param SubmissionModel $model
     * @param string          $prefix
     *
     * @return array
     */
    private function getSeparateFieldInfo(SubmissionModel $model, $prefix = 'submission:')
    {
        $fieldTransformer = new FieldTransformer();

        $data = [];
        foreach ($this->getFieldList($model) as $field) {
            if ($field instanceof NoStorageInterface) {
                continue;
            }

            $value = $model->getFieldValue($field->getHandle());
            $data[] = $fieldTransformer->transformField($field, $value, $prefix . $field->getHandle() . ':');
        }

        return $data;
    }

    /**
     * @param SubmissionModel $model
     *
     * @return AbstractField[]
     */
    private function getFieldList(SubmissionModel $model)
    {
        if (!isset(self::$fieldsByFormId[$model->formId])) {
            $form = $model->getForm();

            self::$fieldsByFormId[$form->id] = $form->getForm()->getLayout()->getFields();
        }

        return self::$fieldsByFormId[$model->formId];
    }
}
