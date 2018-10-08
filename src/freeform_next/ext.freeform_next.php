<?php

use Guzzle\Http\Client;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RecaptchaField;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Services\HoneypotService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */
class Freeform_next_ext
{
    public $version = '1.0.0';

    public function __construct()
    {
        $this->version = AddonInfo::getInstance()->getVersion();
    }

    /**
     * @param AbstractField $field
     */
    public function validateRecaptchaFields(AbstractField $field)
    {
        if ($field instanceof RecaptchaField) {
            $response = ee()->input->post('g-recaptcha-response');
            if (!$response) {
                $field->addError(lang('Please verify that you are not a robot.'));
            } else {
                $secret = SettingsRepository::getInstance()->getOrCreate()->getRecaptchaSecret();

                $client  = new Client();
                $request = $client->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    ['Content-Type' => 'application/json'],
                    [
                        'secret'   => $secret,
                        'response' => $response,
                    ]
                );

                $postResponse = $request->send();
                $result       = json_decode((string) $postResponse->getBody(true), true);

                if (!$result['success']) {
                    $field->addError(lang('Please verify that you are not a robot.'));
                }
            }
        }
    }

    /**
     * @param Form $form
     */
    public function validateHoneypot(Form $form)
    {
        $this->getHoneypotService()->validateFormHoneypot($form);
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addHoneypotInputToForm(Form $form, FormRenderObject $renderObject)
    {
        $this->getHoneypotService()->addHoneyPotInputToForm($renderObject);
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addHoneypotJavascriptToForm(Form $form, FormRenderObject $renderObject)
    {
        $this->getHoneypotService()->addFormJavascript($renderObject);
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addDateTimeJavascript(Form $form, FormRenderObject $renderObject)
    {
        if ($form->getLayout()->hasDatepickerEnabledFields()) {
            static $datepickerLoaded;

            if (null === $datepickerLoaded) {
                $flatpickrCss = file_get_contents(PATH_THIRD_THEMES . 'freeform_next/css/fields/datepicker.css');
                $renderObject->appendCssToOutput($flatpickrCss);

                $flatpickrJs = file_get_contents(__DIR__ . '/javascript/fields/flatpickr.js');
                $datepickerJs = file_get_contents(__DIR__ . '/javascript/fields/datepicker.js');

                $renderObject->appendJsToOutput($flatpickrJs);
                $renderObject->appendJsToOutput($datepickerJs);

                $datepickerLoaded = true;
            }
        }
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addFormDisabledJavascript(Form $form, FormRenderObject $renderObject)
    {
        if ($this->getSettingsService()->isFormSubmitDisable()) {
            // Add the form submit disable logic
            $formSubmitJs = file_get_contents(__DIR__ . '/javascript/form-submit.js');
            $formSubmitJs = str_replace(
                ['{{FORM_ANCHOR}}', '{{PREV_BUTTON_NAME}}'],
                [$form->getAnchor(), SubmitField::PREVIOUS_PAGE_INPUT_NAME],
                $formSubmitJs
            );

            $renderObject->appendJsToOutput($formSubmitJs);
        }
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addFormAnchorJavascript(Form $form, FormRenderObject $renderObject)
    {
        if ($form->isPagePosted() && !$form->isValid()) {
            $anchorJs = file_get_contents(__DIR__ . '/javascript/invalid-form.js');
            $anchorJs = str_replace('{{FORM_ANCHOR}}', $form->getAnchor(), $anchorJs);

            $renderObject->appendJsToOutput($anchorJs);
        }
    }

    /**
     * @return HoneypotService
     */
    private function getHoneypotService()
    {
        static $service;

        if (null === $service) {
            $service = new HoneypotService();
        }

        return $service;
    }

    /**
     * @return SettingsService
     */
    private function getSettingsService()
    {
        static $service;

        if (null === $service) {
            $service = new SettingsService();
        }

        return $service;
    }
}
