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

use Craft\Freeform_SubmissionModel;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Mailing\NotificationInterface;

class MailerService implements MailHandlerInterface
{
    public function sendEmail(
        Form $form,
        array $recipients,
        $notificationId,
        array $fields,
        Freeform_SubmissionModel $submission = null
    ) {
        // TODO: Implement sendEmail() method.
    }

    public function getNotificationById($id)
    {
        // TODO: Implement getNotificationById() method.
    }
}