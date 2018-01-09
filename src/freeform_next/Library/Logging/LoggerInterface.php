<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Logging;

interface LoggerInterface
{
    const DEFAULT_LOGGER_CATEGORY = 'freeform_next';

    const LEVEL_DEBUG   = 'debug';
    const LEVEL_INFO    = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR   = 'error';
    const LEVEL_FATAL   = 'fatal';

    /**
     * @param string $level
     * @param string $message
     * @param string $category
     */
    public function log($level, $message, $category = self::DEFAULT_LOGGER_CATEGORY);

    /**
     * @param string $message
     * @param string $category
     */
    public function debug($message, $category = self::DEFAULT_LOGGER_CATEGORY);

    /**
     * @param string $message
     * @param string $category
     */
    public function info($message, $category = self::DEFAULT_LOGGER_CATEGORY);

    /**
     * @param string $message
     * @param string $category
     */
    public function warn($message, $category = self::DEFAULT_LOGGER_CATEGORY);

    /**
     * @param string $message
     * @param string $category
     */
    public function error($message, $category = self::DEFAULT_LOGGER_CATEGORY);

    /**
     * @param string $message
     * @param string $category
     */
    public function fatal($message, $category = self::DEFAULT_LOGGER_CATEGORY);
}
