<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Database;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

interface SubmissionHandlerInterface
{
    /**
     * Stores the submitted fields to database
     *
     * @param Form  $form
     * @param array $fields
     *
     * @return SubmissionModel|null
     */
    public function storeSubmission(Form $form, array $fields);

    /**
     * Finalize all files uploaded in this form, so that they don' get deleted
     *
     * @param Form $form
     */
    public function finalizeFormFiles(Form $form);

    /**
     * Add a session flash variable that the form has been submitted
     *
     * @param Form $form
     */
    public function markFormAsSubmitted(Form $form);

    /**
     * Check for a session flash variable for form submissions
     *
     * @param Form $form
     *
     * @return bool
     */
    public function wasFormFlashSubmitted(Form $form);
}
