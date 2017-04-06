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

namespace Solspace\Addons\FreeformNext\Library\Configuration;

class EEPluginConfiguration implements ConfigurationInterface
{
    const CONFIG_INDEX = 'freeform_next';

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return ee()->config->item($key, self::CONFIG_INDEX);
    }
}
