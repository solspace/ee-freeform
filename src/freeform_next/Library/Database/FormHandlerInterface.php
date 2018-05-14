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

interface FormHandlerInterface
{
    /**
     * @param Form   $form
     * @param string $templateName
     *
     * @return string
     */
    public function renderFormTemplate(Form $form, $templateName);

    /**
     * @return bool
     */
    public function isSpamProtectionEnabled();

    /**
     * @return bool
     */
    public function isSpamBlockLikeSuccessfulPost();

    /**
     * Increments the spam block counter by 1
     *
     * @param Form $form
     *
     * @return int - new spam block count
     */
    public function incrementSpamBlockCount(Form $form);

    /**
     * @param Form $form
     *
     * @return string
     */
    public function addScriptsToPage(Form $form);

    /**
     * @return string|null
     */
    public function getSubmitUrl();
}
