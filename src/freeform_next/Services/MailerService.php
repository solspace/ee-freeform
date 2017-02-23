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
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\TwigHelper;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Mailing\NotificationInterface;
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

        $fieldValues = $this->getFieldValues($fields, $form, $submission);

        foreach ($recipients as $recipientName => $emailAddress) {
            $fromEmail = TwigHelper::renderString($notification->fromEmail, $fieldValues);
            $fromName  = TwigHelper::renderString($notification->fromName, $fieldValues);
            $replyTo   = TwigHelper::renderString($notification->replyToEmail ?: $notification->fromEmail, $fieldValues);
            $subject   = TwigHelper::renderString($notification->subject, $fieldValues);
            $bodyHtml  = TwigHelper::renderString($notification->bodyHtml, $fieldValues);

            $message = \Swift_Message::newInstance()
                ->setFrom([$fromEmail => $fromName])
                ->setReplyTo([$replyTo => $fromName])
                ->setTo([$emailAddress => $recipientName])
                ->setSubject($subject)
                ->setBody($bodyHtml, 'text/html');

            if ($notification->includeAttachments) {
                foreach ($fields as $field) {
                    // TODO: implement EE file attaching to emails

                    //if ($field instanceof FileUploadInterface) {
                    //    $asset = craft()->assets->getFileById($field->getValue());
                    //    if ($asset) {
                    //        $source = $asset->getSource();
                    //
                    //        if ($source->type != 'Local') {
                    //            // We do not email remote files
                    //            continue;
                    //        } else {
                    //            $sourcePath = $source->settings['path'];
                    //            $folderPath = $asset->getFolder()->path;
                    //
                    //            $sourcePath = craft()->templates->renderObjectTemplate($sourcePath, $fieldValues);
                    //            $folderPath = craft()->templates->renderObjectTemplate($folderPath, $fieldValues);
                    //
                    //            $path = $sourcePath . $folderPath . $asset->filename;
                    //        }
                    //
                    //        $email->addAttachment($path);
                    //    }
                    //}
                }
            }

            try {
                ee()->extensions->call('freeform_next_before_send_email', $message);
                if (ee()->extensions->end_script === TRUE) return $sentMailCount;

                $mailer = $this->getSwiftMailer();
                $sentMailCount += $mailer->send($message);

                ee()->extensions->call('freeform_next_after_send_email', $message);
                if (ee()->extensions->end_script === TRUE) return $sentMailCount;
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
     * @param FieldInterface[] $fields
     * @param Form             $form
     * @param SubmissionModel  $submission
     *
     * @return array
     */
    private function getFieldValues(array $fields, Form $form, SubmissionModel $submission = null)
    {
        $postedValues = [];
        foreach ($fields as $field) {
            if ($field instanceof NoStorageInterface || $field instanceof FileUploadInterface) {
                continue;
            }

            $postedValues[$field->getHandle()] = $field;
        }

        $postedValues['allFields'] = $postedValues;
        $postedValues['form']        = $form;
        $postedValues['submission']  = $submission;
        $postedValues['dateCreated'] = new \DateTime();

        return $postedValues;
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
