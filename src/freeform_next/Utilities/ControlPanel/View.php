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
 * Date: 17.13.2
 * Time: 11:24
 */
namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

abstract class View
{
    /**
     * @return mixed
     */
    abstract public function compile();
}