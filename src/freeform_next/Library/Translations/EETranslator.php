<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Translations;

class EETranslator implements TranslatorInterface
{
    /**
     * Translates a string
     * Replaces any variables in the $string with variables from $variables
     * User brackets to specify variables in string
     *
     * Example:
     * Translation string: "Hello, {firstName}!"
     * Variables: ["firstName": "Icarus"]
     * End result: "Hello, Icarus!"
     *
     * @param string $string
     * @param array  $variables
     *
     * @return string
     */
    public function translate($string, array $variables = [])
    {
        $string = lang($string);

        foreach ($variables as $key => $val) {
            $string = str_replace('{' . $key . '}', $val, $string);
        }

        return $string;
    }
}
