<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components;

interface ComponentInterface
{
    /**
     * ComponentInterface constructor.
     *
     * @param string $location
     */
    public function __construct($location);

    /**
     * Calls the installation of this component
     *
     * @param string $prefix
     */
    public function install($prefix = null);
}
