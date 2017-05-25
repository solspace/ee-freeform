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

namespace Solspace\Addons\FreeformNext\Library\Mailing;

interface NotificationInterface
{
    /**
     * @return string
     */
    public function getHandle();

    /**
     * @return string
     */
    public function getFromName();

    /**
     * @return string
     */
    public function getFromEmail();

    /**
     * @return string
     */
    public function getReplyToEmail();

    /**
     * @return bool
     */
    public function isIncludeAttachmentsEnabled();

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return string
     */
    public function getBodyHtml();

    /**
     * @return string
     */
    public function getBodyText();
}
