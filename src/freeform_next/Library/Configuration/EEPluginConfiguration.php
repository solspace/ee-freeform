<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
