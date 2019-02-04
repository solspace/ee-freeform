<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\ComposerState;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;
use Symfony\Component\Config\Definition\Exception\Exception;

class NextFormNotificationHelper
{
    const STRICT_MODE = true;

    /** @var array */
    public $errors;

    public function saveNotification($classicNotification)
    {
        $notification = NotificationRepository::getInstance()->getOrCreateNotification(null);
        $isNew        = !$notification->id;

        if (isset($classicNotification['includeAttachments'])) {
            $classicNotification['includeAttachments'] = $classicNotification['includeAttachments'] === 'y';
        }

        $validValues = [];
        $validValues['includeAttachments'] = false;
        $validValues['name'] = $classicNotification['notification_label'];
        $validValues['handle'] = $classicNotification['notification_name'];
        $validValues['description'] = $classicNotification['notification_description'];
        $validValues['subject'] = $this->formatHtml($classicNotification['email_subject']);
        $validValues['fromEmail'] = $this->formatHtml($classicNotification['from_email']);
        $validValues['fromName'] = $this->formatHtml($classicNotification['from_name']);
        $validValues['replyToEmail'] = $classicNotification['reply_to_email'];
        $validValues['bodyHtml'] = $this->formatHtml($classicNotification['template_data']);
        $validValues['legacyId'] = $classicNotification['notification_id'];

        $notification->set($validValues);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_NOTIFICATION_BEFORE_SAVE, $notification, $isNew)) {
            return $notification;
        }

        $notification->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_NOTIFICATION_AFTER_SAVE, $notification, $isNew);

        return true;
    }

    private function formatHtml($html)
    {
        $formattedHtml = $html;

        foreach ($this->htmlFormattingMap() as $oldElement => $newElement) {
            $formattedHtml = str_replace($oldElement, $newElement, $formattedHtml);
        }

        return $formattedHtml;
    }

    private function htmlFormattingMap()
    {
        /* Old Element => New Element */

        return [
            '{all_form_fields}' => '{form:fields}',
            '{/all_form_fields}' => '{/form:fields}',
            '{field_label}' => '{field:label}',
            '{field_data}' => '{field:value}',
            '{entry_date format=' => '{date_created format=',
            '{freeform_entry_id}' => '{submission:id}',
            '{form_label}' => '{form:name}',
            '{form_name}' => '{form:handle}',
            '{form_id}' => '{form:id}',
            '{all_form_fields}' => '{form:fields}',
        ];
    }
}
