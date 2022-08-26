<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;

class PasswordField extends TextField implements NoStorageInterface
{
    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_PASSWORD;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        $output = parent::getInputHtml();
        $output = str_replace('type="text"', 'type="password"', $output);

        return $output;
    }
}
