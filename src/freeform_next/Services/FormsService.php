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

class FormsService implements FormHandlerInterface
{
    public function renderFormTemplate(Form $form, $templateName)
    {
        // TODO: Implement renderFormTemplate() method.
    }

    public function isSpamProtectionEnabled()
    {
        return true;
    }

    public function incrementSpamBlockCount(Form $form)
    {
        // TODO: Implement incrementSpamBlockCount() method.
    }
}
