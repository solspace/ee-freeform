<?php

namespace Solspace\Addons\FreeformNext\Utilities\AddonUpdater;

class PluginExtension
{
    /** @var string */
    private $methodName;

    /** @var string */
    private $hookName;

    /** @var array */
    private $settings;

    /** @var int */
    private $priority;

    /** @var bool */
    private $enabled;

    /**
     * PluginExtension constructor.
     *
     * @param string $className
     * @param string $methodName
     * @param string $hookName
     * @param array  $settings
     * @param int    $priority
     * @param bool   $enabled
     */
    public function __construct($methodName, $hookName, array $settings = [], $priority = 5, $enabled = true)
    {
        $this->methodName = $methodName;
        $this->hookName   = $hookName;
        $this->settings   = $settings;
        $this->priority   = $priority;
        $this->enabled    = $enabled;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getHookName()
    {
        return $this->hookName;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings ?: [];
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
