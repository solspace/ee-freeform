<?php

namespace Solspace\Addons\FreeformNext\Repositories;

abstract class Repository
{
    /** @var Repository[] */
    protected static $instances;

    /**
     * Repository constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Prevent object from being cloned
     */
    final private function __clone()
    {
    }

    /**
     * @return Repository|FormRepository
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