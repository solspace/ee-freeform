<?php

namespace Solspace\Addons\FreeformNext\Utilities\Extension;

class Hook
{
    /** @var string */
    private $class;

    /** @var string */
    private $method;

    /** @var string */
    private $hook;

    /** @var array */
    private $settings;

    /** @var int */
    private $priority;

    /** @var string */
    private $version;

    /** @var bool */
    private $enabled;

    /**
     * Hook constructor.
     *
     * @param string $class
     * @param string $method
     * @param string $hook
     * @param string $version
     * @param array  $settings
     * @param int    $priority
     * @param bool   $enabled
     */
    public function __construct(
        $class,
        $method,
        $hook = null,
        $version = '1.0.0',
        array $settings = [],
        $priority = 10,
        $enabled = true
    ) {
        $this->class    = $class;
        $this->method   = $method;
        $this->hook     = $hook;
        $this->settings = $settings;
        $this->priority = $priority;
        $this->version  = $version;
        $this->enabled  = $enabled;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return (int) $this->priority;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->enabled;
    }
}
