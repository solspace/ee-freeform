<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations;

interface IntegrationStorageInterface
{
    /**
     * Update the access token
     *
     * @param string $accessToken
     */
    public function updateAccessToken($accessToken);

    /**
     * Update the settings that are to be stored
     *
     * @param array $settings
     */
    public function updateSettings(array $settings = []);
}
