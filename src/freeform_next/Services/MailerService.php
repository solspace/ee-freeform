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

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\TemplateHelper;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Model\NotificationModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;

class MailerService implements MailHandlerInterface
{
    /**
     * @param Form                 $form
     * @param array                $recipients
     * @param int                  $notificationId
     * @param array                $fields
     * @param SubmissionModel|null $submission
     *
     * @return int
     * @throws FreeformException
     */
    public function sendEmail(
        Form $form,
        array $recipients,
        $notificationId,
        array $fields,
        SubmissionModel $submission = null
    ) {
        $sentMailCount = 0;
        $notification  = $this->getNotificationById($notificationId);

        if (!$notification) {
            throw new FreeformException(
                str_replace('{id}', $notificationId, 'Email notification template with ID {id} not found')
            );
        }

        foreach ($recipients as $recipientName => $emailAddress) {

            $fromEmail = TemplateHelper::renderStringWithForm($notification->fromEmail, $form, $submission);
            $fromName  = TemplateHelper::renderStringWithForm($notification->fromName, $form, $submission);
            $replyTo   = TemplateHelper::renderStringWithForm(
                $notification->replyToEmail ?: $notification->fromEmail,
                $form,
                $submission
            );
            $subject   = TemplateHelper::renderStringWithForm($notification->subject, $form, $submission);
            $bodyHtml  = TemplateHelper::renderStringWithForm($notification->bodyHtml, $form, $submission, true);


            ee()->load->library('email');
            ee()->load->helper('text');

            ee()->email->wordwrap = true;
            ee()->email->mailtype = 'html';
            ee()->email->from($fromEmail, $fromName);
            ee()->email->reply_to($replyTo, $fromName);
            ee()->email->to($emailAddress);
            ee()->email->subject($subject);
            ee()->email->message(entities_to_ascii($bodyHtml));

            if ($notification->includeAttachments) {
                foreach ($fields as $field) {
                    if ($field instanceof FileUploadInterface) {
                        $assetId = $field->getValue();

                        if ($assetId) {
                            $file = ee('Model')
                                ->get('File')
                                ->filter('file_id', $assetId)
                                ->first();

                            if ($file) {
                                $filePath = $file->getAbsolutePath();

                                ee()->email->attach($filePath);
                            }
                        }
                    }
                }
            }

            try {
                $beforeSave = ExtensionHelper::call(
                    ExtensionHelper::HOOK_MAILER_BEFORE_SEND,
                    $notification,
                    $submission
                );

                if (!$beforeSave) {
                    return $sentMailCount;
                }

                $sentToRecipients = (bool) ee()->email->Send();
                $sentMailCount    += $sentToRecipients;

                ExtensionHelper::call(
                    ExtensionHelper::HOOK_MAILER_AFTER_SEND,
                    $sentToRecipients,
                    $notification,
                    $submission
                );
            } catch (\Exception $e) {
            }
        }

        return $sentMailCount;
    }

    /**
     * @param int $id
     *
     * @return NotificationModel|null
     */
    public function getNotificationById($id)
    {
        return NotificationRepository::getInstance()->getNotificationById($id);
    }
}
