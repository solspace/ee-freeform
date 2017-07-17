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

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Helpers\UrlHelper;
use Solspace\Addons\FreeformNext\Services\FieldsService;

abstract class Controller
{
    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    protected function getPost($key, $defaultValue = null)
    {
        $value = ee()->input->post($key);

        if ($value === false) {
            return $defaultValue;
        }

        return $value;
    }

    /**
     * @param string $target
     *
     * @return mixed
     */
    protected function getLink($target)
    {
        return UrlHelper::getLink($target);
    }

    /**
     * @return FieldsService
     */
    protected function getFieldsService()
    {
        static $service;

        if (null === $service) {
            $service = new FieldsService();
        }

        return $service;
    }
}
