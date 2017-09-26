<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;

class FormsService implements FormHandlerInterface
{
    /** @var array */
    private static $spamBlockCache = [];

    /**
     * @param Form   $form
     * @param string $templateName
     *
     * @return string
     * @throws FreeformException
     */
    public function renderFormTemplate(Form $form, $templateName)
    {
        $settings = $this->getSettingsService();

        if (empty($templateName)) {
            throw new FreeformException(lang("Can't use render() if no form template specified"));
        }

        $customTemplates   = $settings->getCustomFormTemplates();
        $solspaceTemplates = $settings->getSolspaceFormTemplates();

        $templatePath = null;
        foreach ($customTemplates as $template) {
            if ($template->getFileName() === $templateName) {
                $templatePath = $template->getFilePath();
                break;
            }
        }

        if (!$templatePath) {
            foreach ($solspaceTemplates as $template) {
                if ($template->getFileName() === $templateName) {
                    $templatePath = $template->getFilePath();
                    break;
                }
            }
        }

        if (null === $templatePath || !file_exists($templatePath)) {
            $translator = new EETranslator();
            throw new FreeformException($translator->translate("Form template '{name}' not found", ['name' => $templateName]));
        }

        $content     = file_get_contents($templatePath);
        $transformer = new FormToTagDataTransformer($form, $content);

        return $transformer->getOutput();
    }

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled()
    {
        return SettingsRepository::getInstance()->getOrCreate()->isSpamProtectionEnabled();
    }

    /**
     * @param Form $form
     *
     * @return int
     */
    public function incrementSpamBlockCount(Form $form)
    {
        if (!isset(self::$spamBlockCache[$form->getId()])) {
            ee()->db->query("UPDATE exp_freeform_next_forms SET spamBlockCount = spamBlockCount + 1 WHERE id = {$form->getId()}");

            $result = ee()->db
                ->select('spamBlockCount')
                ->from('exp_freeform_next_forms')
                ->where(['id' => $form->getId()])
                ->get()
                ->row('spamBlockCount');

            self::$spamBlockCache[$form->getId()] = (int) $result;
        }

        return self::$spamBlockCache[$form->getId()];
    }

    /**
     * @inheritDoc
     */
    public function addScriptsToPage(Form $form)
    {
        $output = '';

        if ($this->isSpamProtectionEnabled()) {
            $output .= '<script type="text/javascript">' . $form->getHoneypotJavascriptScript() . '</script>';
        }

        if ($form->getLayout()->hasDatepickerEnabledFields()) {
            static $datepickerLoaded;

            if (null === $datepickerLoaded) {
                $flatpickrCss = file_get_contents(PATH_THIRD_THEMES . 'freeform_next/css/fields/datepicker.css');
                $output .= "<style>$flatpickrCss</style>";

                $flatpickrJs = file_get_contents(__DIR__ . '/../javascript/fields/flatpickr.js');
                $datepickerJs = file_get_contents(__DIR__ . '/../javascript/fields/datepicker.js');

                $output .= '<script type="text/javascript">' . $flatpickrJs . '</script>';
                $output .= '<script type="text/javascript">' . $datepickerJs . '</script>';

                $datepickerLoaded = true;
            }
        }

        if ($form->isPagePosted() && !$form->isValid()) {
            $anchorJs = file_get_contents(__DIR__ . '/../javascript/invalid-form.js');
            $anchorJs = str_replace('{{FORM_ANCHOR}}', $form->getAnchor(), $anchorJs);
            $output .= '<script type="text/javascript">' . $anchorJs . '</script>';
        }

        return $output;
    }

    /**
     * @return SettingsService
     */
    private function getSettingsService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new SettingsService();
        }

        return $instance;
    }
}
