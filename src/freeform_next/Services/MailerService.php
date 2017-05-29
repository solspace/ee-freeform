<?php
/**
 * Freeform Next for Expression Engine
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
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
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Loader
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

            $message = \Swift_Message::newInstance()
                ->setFrom([$fromEmail => $fromName])
                ->setReplyTo([$replyTo => $fromName])
                ->setTo([$emailAddress => $emailAddress])
                ->setSubject($subject)
                ->setBody($bodyHtml, 'text/html');

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

                                $message->attach(\Swift_Attachment::fromPath($filePath));
                            }
                        }
                    }
                }
            }

            try {
                if (!ExtensionHelper::call(ExtensionHelper::HOOK_MAILER_BEFORE_SEND, $message)) {
                    return $sentMailCount;
                }

                $mailer        = $this->getSwiftMailer();
                $sentToRecipients = $mailer->send($message);
                $sentMailCount += $sentToRecipients;

                ExtensionHelper::call(ExtensionHelper::HOOK_MAILER_AFTER_SEND, (bool) $sentToRecipients);
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

    /**
     * @return \Swift_Mailer
     */
    private function getSwiftMailer()
    {
        switch (ee()->config->item('mail_protocol')) {
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance();

                break;

            case 'smtp':
                $host = ee()->config->item('smtp_server');
                $port = ee()->config->item('smtp_port');
                $user = ee()->config->item('smtp_username');
                $pass = ee()->config->item('smtp_password');
                $type = ee()->config->item('email_smtp_crypto');

                $transport = \Swift_SmtpTransport::newInstance($host, $port, $type ?: null);
                $transport->setUsername($user);
                $transport->setPassword($pass);

                break;

            case 'mail':
            default:
                $transport = \Swift_MailTransport::newInstance();

                break;
        }

        return \Swift_Mailer::newInstance($transport);
    }
}
