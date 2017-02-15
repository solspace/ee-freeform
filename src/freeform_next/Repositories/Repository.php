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

namespace Solspace\Addons\FreeformNext\Repositories;

abstract class Repository
{
    /** @var Repository[] */
    protected static $instances;

    /**
     * Repository constructor.
     */
    private final function __construct()
    {
    }

    /**
     * Prevent object from being cloned
     */
    final private function __clone()
    {
    }

    /**
     * @return Repository|mixed
     */
    public static function getInstance()
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }

        return self::$instances[$class];
    }
}