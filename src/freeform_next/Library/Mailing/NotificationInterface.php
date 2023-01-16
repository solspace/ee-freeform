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
