<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionTransformer
{
    /** @var array */
    private static $fieldsByFormId;

    /**
     * @param SubmissionModel $model
     *
     * @return array
     */
    public function transformSubmission(SubmissionModel $model)
    {
        $prefix = 'submission:';

        $data = [
            $prefix . 'id'        => $model->id,
            $prefix . 'title'     => $model->title,
            $prefix . 'status_id' => $model->statusId,
            $prefix . 'fields'    => $this->getFields($model),
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

            $field->setValue($model->getFieldValue($field->getHandle()));
            $data[] = $fieldTransformer->transformField($field, $prefix);
        }

        return $data;
    }

    private function getSeparateFieldInfo(SubmissionModel $model, $prefix = 'submission:')
    {
        $fieldTransformer = new FieldTransformer();
        $data             = [];
        foreach ($this->getFieldList($model) as $field) {
            if ($field instanceof NoStorageInterface) {
                continue;
            }

            $field->setValue($model->getFieldValue($field->getHandle()));
            $data[] = $fieldTransformer->transformField($field, $prefix . $field->getHandle() . ':');
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
