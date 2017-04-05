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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait RecipientTrait
{
    /** @var int */
    protected $notificationId;

    /**
     * @return int|null
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * Returns true/false based on if the field should or should not act
     * as a recipient email field and receive emails
     *
     * @return bool
     */
    public function shouldReceiveEmail()
    {
        return $this->getNotificationId();
    }
}
