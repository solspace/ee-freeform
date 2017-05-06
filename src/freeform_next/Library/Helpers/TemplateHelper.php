<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class TemplateHelper
{
    /**
     * @param string     $string
     * @param array|null $variables
     *
     * @return string
     */
    public static function renderString($string, array $variables = null)
    {
        return ee()->TMPL->parse_variables($string, [$variables]);
    }

    /**
     * @param string          $string
     * @param Form            $form
     * @param SubmissionModel $submissionModel
     * @param bool            $skipHelperFields
     *
     * @return string
     */
    public static function renderStringWithForm(
        $string,
        Form $form,
        SubmissionModel $submissionModel = null,
        $skipHelperFields = false
    ) {
        $form = clone $form;

        if ($submissionModel) {
            $replaceValues = [];
            foreach ($form->getLayout()->getFields() as $field) {
                if ($field instanceof NoStorageInterface) {
                    continue;
                }

                $value = $submissionModel->getFieldValue($field->getHandle());

                $field->setValue($value);
                $replaceValues[$field->getHandle()] = $value;
            }

            $string = self::renderString($string, $replaceValues);
        }

        $dataTransformer = new FormToTagDataTransformer($form, $string, $skipHelperFields);
        $string          = $dataTransformer->getOutputWithoutWrappingFormTags();

        ee()->TMPL->parse($string);
        $string = ee()->TMPL->template;
        $string = ee()->TMPL->parse_globals($string);

        return $string;
    }
}
