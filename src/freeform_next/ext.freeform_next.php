<?php

use GuzzleHttp\Client;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RecaptchaField;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;
use Solspace\Addons\FreeformNext\Services\HoneypotService;
use Solspace\Addons\FreeformNext\Services\PermissionsService;
use Solspace\Addons\FreeformNext\Services\RecaptchaService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Utilities\AddonInfo;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */
class Freeform_next_ext
{
    /**
     * @var string
     */
    public $version = '1.0.0';

    public function __construct()
    {
        $this->version = AddonInfo::getInstance()->getVersion();
    }

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

    public function validateRecaptcha(Form $form): void
    {
        $this->getRecaptchaService()->validateFormRecaptcha($form);
    }

    public function addRecaptchaInputToForm(Form $form, FormRenderObject $renderObject): void
    {
        $this->getRecaptchaService()->addRecaptchaInputToForm($renderObject);
    }

    public function addRecaptchaJavascriptToForm(Form $form, FormRenderObject $renderObject): void
    {
        $this->getRecaptchaService()->addRecaptchaJavascriptToForm($renderObject);
    }

    public function validateHoneypot(Form $form): void
    {
        $this->getHoneypotService()->validateFormHoneypot($form);
    }

    public function addHoneypotInputToForm(Form $form, FormRenderObject $renderObject): void
    {
    	if($this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled())
		{
        	$this->getHoneypotService()->addHoneyPotInputToForm($renderObject);
		}
    }

    public function addHoneypotJavascriptToForm(Form $form, FormRenderObject $renderObject): void
    {
        $this->getHoneypotService()->addFormJavascript($renderObject);
    }

    public function addDateTimeJavascript(Form $form, FormRenderObject $renderObject): void
    {
        if ($form->getLayout()->hasDatepickerEnabledFields()) {
            static $datepickerLoaded;

            if (null === $datepickerLoaded) {
                // Construct URL to your Freeform theme assets
                $themeUrl = rtrim(URL_THIRD_THEMES, '/') . '/freeform_next';

                // Inject external <link> and <script> tags directly into the form HTML
                $renderObject->appendToOutput('
                    <link rel="stylesheet" href="' . $themeUrl . '/css/fields/datepicker.css">
                    <script src="' . $themeUrl . '/javascript/fields/flatpickr.js"></script>
                    <script src="' . $themeUrl . '/javascript/fields/datepicker.js"></script>
                ');

                $datepickerLoaded = true;
            }
        }
    }

    public function addTableJavascript(Form $form, FormRenderObject $renderObject): void
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

    public function addFormDisabledJavascript(Form $form, FormRenderObject $renderObject): void
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

    public function addFormAnchorJavascript(Form $form, FormRenderObject $renderObject): void
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
    public function addCpCustomMenu($menu): void
	{
		$permissionsService = new PermissionsService;

		$sub = $menu->addSubmenu(FreeformHelper::getEditionName());

        $canManageForms = $permissionsService->canManageForms();
        $canAccessSubmissions = $permissionsService->canAccessSubmissions();
        $canAccessFields = $permissionsService->canAccessFields();
        $canAccessNotifications = $permissionsService->canAccessNotifications();
        $canAccessExports = $permissionsService->canAccessExport();
        $canAccessSettings = $permissionsService->canAccessSettings();

        if($canManageForms)
        {
          ee()->cp->add_js_script(['package' => 'freeform_next:main-nav']);
          $sub->addItem(
            lang('Forms'),
            ee('CP/URL', 'addons/settings/freeform_next/forms')
          );
        }

        if($canAccessSubmissions)
        {
            $formModels = FormRepository::getInstance()->getAllForms();
            $formModel = reset($formModels);
            if ($formModel) {
                $sub->addItem(
                    lang('Submissions'),
                    ee('CP/URL', "addons/settings/freeform_next/submissions/{$formModel->handle}")
                );

                if (FreeformHelper::isFreeformAtLeast('3.3.5')) {
                    if ($this->getSettingsService()->isSpamFolderEnabled()) {
                        $sub->addItem(
                            lang('Spam'),
                            ee('CP/URL', "addons/settings/freeform_next/spam/{$formModel->handle}")
                        );
                    }
                } else {
                    $sub->addItem(
                        lang('Spam'),
                        ee('CP/URL', "addons/settings/freeform_next/spam/{$formModel->handle}")
                    );
                }
            }
        }

        if($canAccessFields)
        {
          $sub->addItem(
            lang('Fields'),
            ee('CP/URL', 'addons/settings/freeform_next/fields')
          );
        }

        if($canAccessNotifications)
        {
          $sub->addItem(
            lang('Notifications'),
            ee('CP/URL', 'addons/settings/freeform_next/notifications')
          );
        }

        if($canAccessExports && FreeformHelper::getVersion() === FREEFORM_PRO)
        {
          $sub->addItem(
            lang('Export'),
            ee('CP/URL', 'addons/settings/freeform_next/export_profiles')
          );
        }

        if($canAccessSettings)
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
