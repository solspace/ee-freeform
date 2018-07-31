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

namespace Solspace\Addons\FreeformNext\Library\Database;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

interface FormHandlerInterface
{
    const EVENT_BEFORE_SUBMIT      = 'beforeSubmit';
    const EVENT_AFTER_SUBMIT       = 'afterSubmit';
    const EVENT_BEFORE_SAVE        = 'beforeSave';
    const EVENT_AFTER_SAVE         = 'afterSave';
    const EVENT_BEFORE_DELETE      = 'beforeDelete';
    const EVENT_AFTER_DELETE       = 'afterDelete';
    const EVENT_RENDER_OPENING_TAG = 'renderOpeningTag';
    const EVENT_RENDER_CLOSING_TAG = 'renderClosingTag';
    const EVENT_FORM_VALIDATE      = 'validateForm';

    /**
     * @param Form   $form
     * @param string $templateName
     *
     * @return string
     */
    public function renderFormTemplate(Form $form, $templateName);

    /**
     * Increments the spam block counter by 1
     *
     * @param Form $form
     *
     * @return int - new spam block count
     */
    public function incrementSpamBlockCount(Form $form);

    /**
     * @return bool
     */
    public function isSpamBehaviourSimulateSuccess();

    /**
     * @return bool
     */
    public function isSpamBehaviourReloadForm();

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled();

    /**
     * Do something before the form is saved
     * Return bool determines whether the form should be saved or not
     *
     * @param Form $form
     *
     * @return bool
     */
    public function onBeforeSubmit(Form $form);

    /**
     * Do something after the form is saved
     *
     * @param Form                 $form
     * @param SubmissionModel|null $submission
     */
    public function onAfterSubmit(Form $form, SubmissionModel $submission = null);

    /**
     * Attach anything to the form after opening tag
     *
     * @param Form $form
     *
     * @return string
     */
    public function onRenderOpeningTag(Form $form);

    /**
     * Attach anything to the form before the closing tag
     *
     * @param Form $form
     *
     * @return string
     */
    public function onRenderClosingTag(Form $form);

    /**
     * @param Form $form
     */
    public function onFormValidate(Form $form);
}
