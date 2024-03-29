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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait MailingListTrait
{
    /** @var int */
    protected $integrationId;

    /** @var string */
    protected $resourceId;

    /** @var string */
    protected $emailFieldHash;

    /**
     * @return int
     */
    public function getIntegrationId()
    {
        return $this->integrationId;
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function getEmailFieldHash()
    {
        return $this->emailFieldHash;
    }
}
