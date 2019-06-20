<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Session;

class EERequest implements RequestInterface
{
    /**
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getPost($key, $defaultValue = null)
    {
        $post = ee()->input->post($key);

        return $post !== false ? $post : $defaultValue;
    }
}
