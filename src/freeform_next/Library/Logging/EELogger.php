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
    /** @var \Logger[] */
    private static $loggers = [];

    /** @var bool */
    private static $loggerInitiated;

    /**
     * @param string $category
     *
     * @return \Logger
     */
    public static function get($category = self::DEFAULT_LOGGER_CATEGORY)
    {
        if (!isset(self::$loggers[$category])) {
            if (null === self::$loggerInitiated) {
                $config = include __DIR__ . '/logger_config.php';
                \Logger::configure($config);

                self::$loggerInitiated = true;
            }

            self::$loggers[$category] = \Logger::getLogger($category);
        }

        return self::$loggers[$category];
    }

    /**
     * @param string $level
     * @param string $message
     * @param string $category
     */
    public function log($level, $message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        $logger = self::get($category);

        $logger->log($this->getLevel($level), $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function debug($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        self::get($category)->debug($message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function info($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        self::get($category)->info($message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function warn($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        self::get($category)->warn($message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function error($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        self::get($category)->error($message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function fatal($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
        self::get($category)->fatal($message);
    }

    /**
     * @param string $level
     *
     * @return \LoggerLevel
     */
    private function getLevel($level)
    {
        switch ($level) {
            case self::LEVEL_DEBUG:
                return \LoggerLevel::getLevelDebug();

            case self::LEVEL_ERROR:
                return \LoggerLevel::getLevelError();

            case self::LEVEL_FATAL:
                return \LoggerLevel::getLevelFatal();

            case self::LEVEL_INFO:
                return \LoggerLevel::getLevelInfo();

            case self::LEVEL_WARNING:
                return \LoggerLevel::getLevelWarn();

            default:
                return \LoggerLevel::getLevelError();
        }
    }
}
