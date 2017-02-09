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

namespace Solspace\Addons\FreeformNext\Library\Database;

use Craft\Freeform_NotificationModel;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;

interface SubmissionHandlerInterface
{
    /**
     * Stores the submitted fields to database
     *
     * @param Form  $form
     * @param array $fields
     *
     * @return Freeform_NotificationModel|null
     */
    public function storeSubmission(Form $form, array $fields);

    /**
     * Finalize all files uploaded in this form, so that they don' get deleted
     *
     * @param Form $form
     */
    public function finalizeFormFiles(Form $form);
}
