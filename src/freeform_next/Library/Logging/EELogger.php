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

namespace Solspace\Addons\FreeformNext\Library\Logging;

use Craft\Craft;
use Craft\LogLevel;

class EELogger implements LoggerInterface
{
    /**
     * @param string $level
     * @param string $message
     */
    public function log($level, $message)
    {
        if (!isset(ee()->logger)) {
            ee()->load->library('logger');
        }

        ee()->logger->developer($level . ': ' . $message);
    }
}
