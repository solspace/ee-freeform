<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

class AdminNotificationProperties extends AbstractProperties
{
    /** @var string */
    protected $notificationId;

    /** @var string */
    protected $recipients;

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
        $list = array_filter($list);

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
    protected function getPropertyManifest()
    {
        return [
            'notificationId' => self::TYPE_STRING,
            'recipients'     => self::TYPE_STRING,
        ];
    }
}
