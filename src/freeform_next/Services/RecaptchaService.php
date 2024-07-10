<?php

namespace Solspace\Addons\FreeformNext\Services;

use GuzzleHttp\Client;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;

class RecaptchaService
{
    /**
     * Adds Recaptcha javascript to forms
     *
     * @param FormRenderObject $renderObject
     */
    public function addRecaptchaJavascriptToForm(FormRenderObject $renderObject)
    {
        $settingsModel = $this->getSettingsService()->getSettingsModel();

        $isRecaptchaEnabled = $settingsModel->isRecaptchaEnabled();
        $isRecaptchaV3 = $settingsModel->getRecaptchaType() === 'v3';
        $recaptchaKey = $settingsModel->getRecaptchaKey();
        $recaptchaSecret = $settingsModel->getRecaptchaSecret();

        if (!$isRecaptchaEnabled) {
            return;
        }

        if (!$isRecaptchaV3) {
            return;
        }

        if (!$recaptchaKey) {
            return;
        }

        if (!$recaptchaSecret) {
            return;
        }

        $renderObject->appendToOutput($this->getRecaptchaJavascript());
    }

    /**
     * Assembles a Recaptcha field
     *
     * @param FormRenderObject $renderObject
     */
    public function addRecaptchaInputToForm(FormRenderObject $renderObject)
    {
        $settingsModel = $this->getSettingsService()->getSettingsModel();

        $isRecaptchaEnabled = $settingsModel->isRecaptchaEnabled();
        $isRecaptchaV3 = $settingsModel->getRecaptchaType() === 'v3';
        $recaptchaKey = $settingsModel->getRecaptchaKey();
        $recaptchaSecret = $settingsModel->getRecaptchaSecret();

        if (!$isRecaptchaEnabled) {
            return;
        }

        if (!$isRecaptchaV3) {
            return;
        }

        if (!$recaptchaKey) {
            return;
        }

        if (!$recaptchaSecret) {
            return;
        }

        $renderObject->appendToOutput($this->getRecaptchaInput());
    }

    /**
     * @param Form $form
     */
    public function validateFormRecaptcha(Form $form)
    {
        if (!$form->isValid()) {
            return;
        }

        $settingsModel = $this->getSettingsService()->getSettingsModel();

        $isRecaptchaEnabled = $settingsModel->isRecaptchaEnabled();
        $isRecaptchaV3 = $settingsModel->getRecaptchaType() === 'v3';
        $recaptchaKey = $settingsModel->getRecaptchaKey();
        $recaptchaSecret = $settingsModel->getRecaptchaSecret();

        if (!$isRecaptchaEnabled) {
            return;
        }

        if (!$isRecaptchaV3) {
            return;
        }

        if (!$recaptchaKey) {
            return;
        }

        if (!$recaptchaSecret) {
            return;
        }

        $response = ee()->input->post('g-recaptcha-response');

        if (!$response) {
            $form->addError(lang('Please verify that you are not a robot.'));
        } else {
            $client = new Client();

            $postResponse = $client->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'secret' => $recaptchaSecret,
                        'response' => $response,
                    ],
                ]
            );

            $result = json_decode((string) $postResponse->getBody(), true);

            if (isset($result['score'])) {
                $minScore = $settingsModel->getRecaptchaScoreThreshold();

                $minScore = min(1, $minScore);
                $minScore = max(0, $minScore);

                if ($result['score'] < $minScore) {
                    $form->addError(lang('Score check failed with ['.$result['score'].']'));
                }
            }

            if ($result['success']) {
                return;
            }

            $errors = [];
            $errorCodes = $result['error-codes'];

            if (\in_array('missing-input-secret', $errorCodes, true)) {
                $errors[] = lang('The secret parameter is missing.');
            }

            if (\in_array('invalid-keys', $errorCodes, true)) {
                $errors[] = lang('The key parameter is invalid or malformed.');
            }

            if (\in_array('invalid-input-secret', $errorCodes, true)) {
                $errors[] = lang('The secret parameter is invalid or malformed.');
            }

            if (\in_array('missing-input-response', $errorCodes, true)) {
                $errors[] = lang('The response parameter is missing.');
            }

            if (\in_array('invalid-input-response', $errorCodes, true)) {
                $errors[] = lang('The response parameter is invalid or malformed.');
            }

            if (\in_array('bad-request', $errorCodes, true)) {
                $errors[] = lang('The request is invalid or malformed.');
            }

            if (\in_array('timeout-or-duplicate', $errorCodes, true)) {
                $errors[] = lang('The response is no longer valid: either is too old or has been used previously.');
            }
        }

        if (empty($errors)) {
            return;
        }

        $form->addErrors($errors);
    }

    /**
     * @return string
     */
    public function getRecaptchaJavascript()
    {
        $output = '';

        $recaptchaKey = $this->getSettingsService()->getSettingsModel()->getRecaptchaKey();
        if ($recaptchaKey) {
            $output .= '<script src="https://www.google.com/recaptcha/api.js?render='.$recaptchaKey.'" async></script>';
            $output .= '<script>window.onload=()=>{grecaptcha.ready(function(){grecaptcha.execute("'.$recaptchaKey.'",{action:"submit"}).then(function(token){const gRecaptchaResponse=document.querySelector("#g-recaptcha-response");if(gRecaptchaResponse){gRecaptchaResponse.value=token;}});});};</script>';
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getRecaptchaInput()
    {
        return '<textarea data-recaptcha="" id="g-recaptcha-response" name="g-recaptcha-response" style="visibility: hidden; position: absolute; top: -9999px; left: -9999px; width: 1px; height: 1px; overflow: hidden; border: none;"></textarea>';
    }

    /**
     * @return SettingsService
     */
    private function getSettingsService()
    {
        return new SettingsService();
    }
}
