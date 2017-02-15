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
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;

class SubmissionsService implements SubmissionHandlerInterface
{
    public function storeSubmission(Form $form, array $fields)
    {
        // TODO: Implement storeSubmission() method.
    }

    public function finalizeFormFiles(Form $form)
    {
        // TODO: Implement finalizeFormFiles() method.
    }
}