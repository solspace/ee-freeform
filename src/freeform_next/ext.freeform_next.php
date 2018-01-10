<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

class Freeform_next_ext
{
    public $version = '1.0.0';

    public function __construct()
    {
        $this->version = \Solspace\Addons\FreeformNext\Utilities\AddonInfo::getInstance()->getVersion();
    }
}
