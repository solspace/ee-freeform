<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;

class FormTransformer implements Transformer
{
    /**
     * Returns an array meant for EE Tag processing
     *
     * @param Form $form
     * @param int  $submissionCount
     *
     * @return array
     */
    public function transformForm(Form $form, $submissionCount = 0)
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
            'form:row_class'                 => $form->getCustomAttributes()->getRowClass(),
            'form:column_class'              => $form->getCustomAttributes()->getColumnClass(),
            'form:submission_count'          => $submissionCount,
            'form:fields'                    => $this->getFields($form),
        ];
    }

    /**
     * @param Form   $form
     * @param string $prefix
     *
     * @return array
     */
    private function getFields(Form $form, $prefix = 'field:')
    {
        $fieldTransformer = new FieldTransformer();

        $data = [];
        foreach ($form->getLayout()->getFields() as $field) {
            $data[] = $fieldTransformer->transformField($field, $prefix);
        }

        return $data;
    }
}
