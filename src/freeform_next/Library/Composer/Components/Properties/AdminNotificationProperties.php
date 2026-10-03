<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

class AdminNotificationProperties extends AbstractProperties
{
    protected string $format = 'html';

    /** @var string */
    protected $notificationId;

    /** @var string */
    protected $recipients;

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @return string
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Gets all recipients as an array
     *
     * @return array
     */
    public function getRecipientArray()
    {
        $recipients = $this->getRecipients();

        if (empty($recipients)) {
            return [];
        }

        $list = preg_split("/\r\n|\n|\r/", $recipients);
        $list = array_map('trim', $list);
        $list = array_unique($list);
        // Saved forms may predate the row editor or have been modified outside
        // the builder. Never hand malformed addresses to the mail transport.
        $list = array_filter($list, static fn ($email) => false !== filter_var($email, FILTER_VALIDATE_EMAIL));

        return $list;
    }

    /**
     * Return a list of all property fields and their type
     *
     * [propertyKey => propertyType, ..]
     * E.g. ["name" => "string", ..]
     *
     * @return array
     */
    protected function getPropertyManifest(): array
    {
        return [
            'format'         => self::TYPE_STRING,
            'notificationId' => self::TYPE_STRING,
            'recipients'     => self::TYPE_STRING,
        ];
    }
}
