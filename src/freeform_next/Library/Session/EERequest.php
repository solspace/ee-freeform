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

/**
 * Created by PhpStorm.
 * User: gustavs
 * Date: 17.8.2
 * Time: 17:09
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

        return $post ?: $defaultValue;
    }
}