<?php

namespace Solspace\Addons\FreeformNext\Utilities;

use Solspace\Addons\FreeformNext\Utilities\Extension\Hook;

abstract class Extension
{
    /**
     * @return Hook[]
     */
    public abstract function getHooks();

    /**
     * Installs all hooks
     */
    public final function activate_extension()
    {
        foreach ($this->getHooks() as $hook) {
            ee()->db
                ->insert(
                    'extensions',
                    [
                        'class'    => $hook->getClass(),
                        'method'   => $hook->getMethod(),
                        'hook'     => $hook->getHook() ?: $hook->getMethod(),
                        'settings' => $hook->getSettings() ? json_encode($hook->getSettings()) : '',
                        'priority' => $hook->getPriority(),
                        'version'  => $hook->getVersion(),
                        'enabled'  => $hook->isEnabled() ? 'y' : 'n',
                    ]
                );
        }
    }

    /**
     * Removes all hooks
     */
    public final function disable_extension()
    {
        foreach ($this->getHooks() as $hook) {
            ee()->db
                ->where('class', $hook->getClass())
                ->where('method', $hook->getMethod())
                ->where('hook', $hook->getHook() ?: $hook->getMethod())
                ->delete('exp_extensions');
        }
    }
}
