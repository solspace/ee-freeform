<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;

class FormTransformer implements Transformer
{
    /**
     * Returns an array meant for EE Tag processing
     *
     * @param Form $form
     * @param int  $submissionCount
     * @param bool $skipHelperFields
     *
     * @return array
     */
    public function transformForm(Form $form, $submissionCount = 0, $skipHelperFields = false)
    {
        return [
            'form:id'                        => $form->getId(),
            'form:name'                      => $form->getName(),
            'form:handle'                    => $form->getHandle(),
            'form:description'               => $form->getDescription(),
            'form:return_url'                => $form->getReturnUrl(),
            'form:return'                    => $form->getReturnUrl(),
            'form:action'                    => $form->getCustomAttributes()->getAction(),
            'form:method'                    => $form->getCustomAttributes()->getMethod(),
            'form:class'                     => $form->getCustomAttributes()->getClass(),
            'form:page_count'                => count($form->getPages()),
            'form:is_submitted_successfully' => $form->isSubmittedSuccessfully(),
            'form:has_errors'                => $form->hasErrors(),
            'form:errors'                    => $this->getErrors($form),
            'form:row_class'                 => $form->getCustomAttributes()->getRowClass(),
            'form:column_class'              => $form->getCustomAttributes()->getColumnClass(),
            'form:submission_count'          => $submissionCount,
            'form:field_id_prefix'           => $form->getCustomAttributes()->getFieldIdPrefix(),
            'form:fields'                    => $this->getFields($form, 'field:', $skipHelperFields),
            'form:current_page'              => [
                [
                    'page:index' => $form->getCurrentPage()->getIndex(),
                    'page:label' => $form->getCurrentPage()->getLabel(),
                ],
            ],
        ];
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    private function getErrors(Form $form)
    {
        $data = [];
        foreach ($form->getErrors() as $error) {
            $data[] = ['error' => $error];
        }

        return $data;
    }

    /**
     * @param Form   $form
     * @param string $prefix
     * @param bool   $skipHelperFields
     *
     * @return array
     */
    private function getFields(Form $form, $prefix = 'field:', $skipHelperFields = false)
    {
        $fieldTransformer = new FieldTransformer();

        $data = [];
        foreach ($form->getLayout()->getFields() as $field) {
            if ($skipHelperFields && ($field instanceof NoStorageInterface || $field instanceof FileUploadInterface)) {
                continue;
            }

            if ($field instanceof DynamicRecipientField) {
                $value = $field->getValue();
            } else if ($field instanceof StaticValueInterface) {
                if ($field instanceof CheckboxField) {
                    $value = $field->isChecked() ? $field->getStaticValue() : '';
                } else {
                    $value = $field->getStaticValue();
                }
            } else {
                $value = $field->getValueAsString();
            }

            $data[] = $fieldTransformer->transformField($field, $value, $prefix);
        }

        return $data;
    }
}
