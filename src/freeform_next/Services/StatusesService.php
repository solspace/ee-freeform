<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Database\StatusHandlerInterface;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;

class StatusesService implements StatusHandlerInterface
{
    /**
     * @return int
     */
    public function getDefaultStatusId()
    {
        return StatusRepository::getInstance()->getDefaultStatusId();
    }
}
