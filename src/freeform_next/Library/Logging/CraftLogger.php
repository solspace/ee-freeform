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

class CraftLogger implements LoggerInterface
{
    /**
     * @param string $level
     * @param string $message
     */
    public function log($level, $message)
    {
        Craft::log($message, $this->getCraftLogLevel($level));
    }

    /**
     * @param string $level
     *
     * @return string
     */
    private function getCraftLogLevel($level)
    {
        switch ($level) {
            case self::LEVEL_WARNING:
                $craftLogLevel = LogLevel::Warning;
                break;

            case self::LEVEL_ERROR:
                $craftLogLevel = LogLevel::Error;
                break;

            case self::LEVEL_INFO:
            default:
                $craftLogLevel = LogLevel::Info;
                break;
        }

        return $craftLogLevel;
    }
}
