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

namespace Solspace\Addons\FreeformNext\Library\Logging;

class EELogger implements LoggerInterface
{
    /** @var \Logger[] */
    private static $loggers = [];

    /** @var bool */
    private static $loggerInitiated;

	public function __construct()
	{
		ee()->load->library('logger');
	}

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
		ee()->logger->developer("[{$category}][{$this->getLevel($level)}]: " . $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function debug($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
		ee()->logger->developer("[{$category}][{$this->getLevel('debug')}]: " . $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function info($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
		ee()->logger->developer("[{$category}][{$this->getLevel('info')}]: " . $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function warn($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
		ee()->logger->developer("[{$category}][{$this->getLevel('warn')}]: " . $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function error($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
		ee()->logger->developer("[{$category}][{$this->getLevel('error')}]: " . $message);
    }

    /**
     * @param string $message
     * @param string $category
     */
    public function fatal($message, $category = self::DEFAULT_LOGGER_CATEGORY)
    {
		ee()->logger->developer("[{$category}][{$this->getLevel('fatal')}]: " . $message);
    }

    /**
     * @param string $level
     *
     * @return string
	 */
    private function getLevel($level)
    {
        switch ($level) {
            case self::LEVEL_DEBUG:
                return self::LEVEL_DEBUG;

            case self::LEVEL_FATAL:
                return self::LEVEL_FATAL;

            case self::LEVEL_INFO:
                return self::LEVEL_INFO;

            case self::LEVEL_WARNING:
                return self::LEVEL_WARNING;

            default:
                return self::LEVEL_ERROR;
        }
    }
}
