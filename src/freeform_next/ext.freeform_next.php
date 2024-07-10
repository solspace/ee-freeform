<?php

use GuzzleHttp\Client;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RecaptchaField;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Services\HoneypotService;
use Solspace\Addons\FreeformNext\Services\PermissionsService;
use Solspace\Addons\FreeformNext\Services\RecaptchaService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

require_once version_compare(PHP_VERSION, '8.0.0') < 0 ? __DIR__ . '/php7/vendor/autoload.php' : __DIR__ . '/vendor/autoload.php';

/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
        $settingsModel = $this->getSettingsService()->getSettingsModel();

        $isRecaptchaEnabled = $settingsModel->isRecaptchaEnabled();
        $isRecaptchaV3 = $settingsModel->getRecaptchaType() === 'v3';
        $recaptchaKey = $settingsModel->getRecaptchaKey();
        $recaptchaSecret = $settingsModel->getRecaptchaSecret();

        if (!$isRecaptchaEnabled) {
            return false;
        }

        if ($isRecaptchaV3) {
            return false;
        }

        if (!$recaptchaKey) {
            return false;
        }

        if (!$recaptchaSecret) {
            return false;
        }

        if ($field instanceof RecaptchaField) {
            $response = ee()->input->post('g-recaptcha-response');
            if (!$response) {
                $field->addError(lang('Please verify that you are not a robot.'));
            } else {
                $secret = SettingsRepository::getInstance()->getOrCreate()->getRecaptchaSecret();

                $client  = new Client();
				$postResponse = $client->post(
                    'https://www.google.com/recaptcha/api/siteverify',
					[
						'headers' => [
							'Content-Type' => 'application/x-www-form-urlencoded',
						],
						'form_params'         => [
							'secret'   => $secret,
							'response' => $response,
						],
					]
				);


                // $postResponse = $request->send();
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
    public function validateRecaptcha(Form $form)
    {
        $this->getRecaptchaService()->validateFormRecaptcha($form);
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addRecaptchaInputToForm(Form $form, FormRenderObject $renderObject)
    {
        $this->getRecaptchaService()->addRecaptchaInputToForm($renderObject);
    }

    /**
     * @param Form             $form
     * @param FormRenderObject $renderObject
     */
    public function addRecaptchaJavascriptToForm(Form $form, FormRenderObject $renderObject)
    {
        $this->getRecaptchaService()->addRecaptchaJavascriptToForm($renderObject);
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
    	if($this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled())
		{
        	$this->getHoneypotService()->addHoneyPotInputToForm($renderObject);
		}
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
    public function addTableJavascript(Form $form, FormRenderObject $renderObject)
    {
        if ($form->getLayout()->hasTableFields()) {
            static $tableScriptLoaded;

            if (null === $tableScriptLoaded) {
                $tableJs = file_get_contents(__DIR__ . '/javascript/fields/table.js');
                $renderObject->appendJsToOutput($tableJs);

                $tableScriptLoaded = true;
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
        $autoScroll = $this->getSettingsService()->getSettingsModel()->isAutoScrollToErrors();

        if ($autoScroll && $form->isFormPosted()) {
            $anchorJs = file_get_contents(__DIR__ . '/javascript/invalid-form.js');
            $anchorJs = str_replace('{{FORM_ANCHOR}}', $form->getAnchor(), $anchorJs);

            $renderObject->appendJsToOutput($anchorJs);
        }
    }

	/**
	 * Add the Freeform Menu
	 *
	 * @param object $menu ExpressionEngine\Service\CustomMenu\Menu
	 */
    public function addCpCustomMenu($menu)
	{
		$permissionsService = new PermissionsService;

		$sub = $menu->addSubmenu(FreeformHelper::get('name'));

		if($permissionsService->canManageForms(ee()->session->userdata('group_id')))
		{
			$sub->addItem(
				lang('Forms'),
				ee('CP/URL', 'addons/settings/freeform_next/forms')
			);
		}

		if($permissionsService->canAccessFields(ee()->session->userdata('group_id')))
		{
			$sub->addItem(
				lang('Fields'),
				ee('CP/URL', 'addons/settings/freeform_next/fields')
			);
		}

		if($permissionsService->canAccessNotifications(ee()->session->userdata('group_id')))
		{
			$sub->addItem(
				lang('Notifications'),
				ee('CP/URL', 'addons/settings/freeform_next/notifications')
			);
		}

		if($permissionsService->canAccessExport(ee()->session->userdata('group_id')) && FreeformHelper::get('version') === 'pro')
		{
			$sub->addItem(
				lang('Export'),
				ee('CP/URL', 'addons/settings/freeform_next/export_profiles')
			);
		}

		if($permissionsService->canAccessSettings(ee()->session->userdata('group_id')))
		{
			$sub->addItem(
				lang('Settings'),
				ee('CP/URL', 'addons/settings/freeform_next/settings/general')
			);
		}
	}

    /**
     * @return RecaptchaService
     */
    private function getRecaptchaService()
    {
        static $service;

        if (null === $service) {
            $service = new RecaptchaService();
        }

        return $service;
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
