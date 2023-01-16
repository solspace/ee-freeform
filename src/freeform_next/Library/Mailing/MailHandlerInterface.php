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

namespace Solspace\Addons\FreeformNext\Library\Mailing;

use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

interface MailHandlerInterface
{
    /**
     * Send out an email to recipients using the given mail template
     *
     * @param Form             $form
     * @param array            $recipients
     * @param int              $notificationId
     * @param FieldInterface[] $fields
     * @param SubmissionModel  $submission
     *
     * @return bool
     */
    public function sendEmail(
        Form $form,
        array $recipients,
        $notificationId,
        array $fields,
        SubmissionModel $submission = null
    );

    /**
     * @param int $id
     *
     * @return NotificationInterface
     */
    public function getNotificationById($id);
}
