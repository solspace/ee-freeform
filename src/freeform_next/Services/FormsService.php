<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\SettingsModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
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
    public function isSpamBehaviourSimulateSuccess()
    {
        return SettingsRepository::getInstance()->getOrCreate()->isSpamBlockLikeSuccessfulPost();
    }

    /**
     * @return bool
     */
    public function isSpamBehaviourReloadForm()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled()
    {
        return SettingsRepository::getInstance()->getOrCreate()->isSpamProtectionEnabled();
    }

    /**
     * @return bool
     */
    public function isSpamBlockLikeSuccessfulPost()
    {
        return SettingsRepository::getInstance()->getOrCreate()->isSpamBlockLikeSuccessfulPost();
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
     * @return null|string
     */
    public function getSubmitUrl()
    {
        try {
            $actionId = ee()->db
                ->where(
                    array(
                        'class'  => 'Freeform_next',
                        'method' => 'submitForm',
                    )
                )
                ->get('actions')
                ->row()
                ->action_id;
        } catch (\Exception $e) {
            return null;
        }

        return sprintf(
            '%s%s?ACT=%d',
            ee()->config->item('base_url'),
            ee()->config->item('site_index'),
            $actionId
        );
    }

    /**
     * @inheritDoc
     */
    public function onBeforeSubmit(Form $form)
    {
        return ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_SUBMIT, $form);
    }

    /**
     * @inheritDoc
     */
    public function onAfterSubmit(Form $form, SubmissionModel $submission = null)
    {
        ExtensionHelper::call(ExtensionHelper::HOOK_FORM_AFTER_SUBMIT, $form, $submission);
    }

    /**
     * @inheritDoc
     */
    public function onRenderOpeningTag(Form $form, array $outputChunks = [])
    {
        $renderObject = new FormRenderObject($form);
        ExtensionHelper::call(ExtensionHelper::HOOK_FORM_RENDER_OPENING_TAG, $form, $renderObject);

        return $renderObject->getCompiledOutput();
    }

    /**
     * @inheritDoc
     */
    public function onRenderClosingTag(Form $form)
    {
        $renderObject = new FormRenderObject($form);
        ExtensionHelper::call(ExtensionHelper::HOOK_FORM_RENDER_CLOSING_TAG, $form, $renderObject);

        return $renderObject->getCompiledOutput();
    }

    /**
     * @inheritDoc
     */
    public function onFormValidate(Form $form)
    {
        ExtensionHelper::call(ExtensionHelper::HOOK_FORM_VALIDATE, $form);
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
