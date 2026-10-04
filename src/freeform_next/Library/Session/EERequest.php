<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
    public function getPost($key, mixed $defaultValue = null)
    {
        $post = ee()->input->post($key);

        return $post !== false ? $post : $defaultValue;
    }

    public function getQuery($key, mixed $defaultValue = null)
    {
        $query = ee()->input->get($key);

        return $query !== false ? $query : $defaultValue;
    }
}
