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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers;

class Option
{
    /** @var string */
    private $label;

    /** @var string */
    private $value;

    /** @var bool */
    private $checked;

    /**
     * Option constructor.
     *
     * @param string $label
     * @param string $value
     * @param bool   $checked
     */
    public function __construct($label, $value, $checked = false)
    {
        $this->label   = $label;
        $this->value   = $value;
        $this->checked = $checked;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->checked;
    }
}
