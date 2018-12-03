<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\SubmissionTransformer;
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
        self::loadTemplateLib();

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

        $replaceValues = [
            'date_created' => ee()->localize->now,
        ];

        if ($submissionModel) {
            $submissionTransformer = new SubmissionTransformer();
            foreach ($form->getLayout()->getFields() as $field) {
                if ($field instanceof NoStorageInterface) {
                    continue;
                }

                $value = $submissionModel->getFieldValue($field->getHandle());

                $field->setValue($value);
                $replaceValues[$field->getHandle()] = $field->getValueAsString();
            }

            $string = self::renderString($string, $submissionTransformer->transformSubmission($submissionModel));
            $string = self::renderString($string, $replaceValues);
        } else {
            foreach ($form->getLayout()->getFields() as $field) {
                if ($field instanceof NoStorageInterface || $field instanceof FileUploadInterface) {
                    continue;
                }

                $handle                 = $field->getHandle();
                $replaceValues[$handle] = ee()->input->post($handle);
            }

            $string = self::renderString($string, $replaceValues);
        }

        $dataTransformer = new FormToTagDataTransformer($form, $string, $skipHelperFields);
        $string          = $dataTransformer->getOutputWithoutWrappingFormTags();

        self::loadTemplateLib();

        ee()->TMPL->parse($string);
        if (!empty($string)) {
            $string = ee()->TMPL->template;
            $string = ee()->TMPL->parse_globals($string);
        }

        return $string;
    }

    /**
     * Loads the TMPL if it's not initialized
     */
    private static function loadTemplateLib()
    {
        if (!isset(ee()->TMPL)) {
            ee()->load->library('template', null, 'TMPL');
        }
    }
}
