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
     *
     * @return string
     */
    public static function renderStringWithForm($string, Form $form, SubmissionModel $submissionModel = null)
    {
        $form = clone $form;

        if ($submissionModel) {
            foreach ($form->getLayout()->getFields() as $field) {
                if ($field instanceof NoStorageInterface) {
                    continue;
                }

                $field->setValue($submissionModel->getFieldValue($field->getHandle()));
            }
        }

        $dataTransformer = new FormToTagDataTransformer($form, $string);
        $string          = $dataTransformer->getOutputWithoutWrappingFormTags();

        ee()->TMPL->parse($string);
        $string = ee()->TMPL->template;
        $string = ee()->TMPL->parse_globals($string);

        return $string;
    }
}
