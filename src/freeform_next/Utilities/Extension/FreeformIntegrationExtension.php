<?php

namespace Solspace\Addons\FreeformNext\Utilities\Extension;

use Solspace\Addons\FreeformNext\Utilities\Extension;

abstract class FreeformIntegrationExtension extends Extension
{
    const REGISTER_TYPES_METHOD      = 'registerIntegrations';
    const HOOK_REGISTER_INTEGRATIONS = 'freeform_next.registerIntegrations';

    public $version = '1.0.0';

    /**
     * @return Hook[]
     */
    public function getHooks()
    {
        return [
            new Hook(
                get_class($this),
                self::REGISTER_TYPES_METHOD,
                self::HOOK_REGISTER_INTEGRATIONS,
                $this->version
            ),
        ];
    }

    /**
     * @return array
     */
    public abstract function registerIntegrations();
}
