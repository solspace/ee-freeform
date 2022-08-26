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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers;

class Option implements \JsonSerializable
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
        return (string) $this->label;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return (string) $this->value;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->checked;
    }

	/**
	 * @return array
	 */
    public function jsonSerialize(): array
    {
        return [
            'label'   => $this->getLabel(),
            'value'   => $this->getValue(),
        ];
    }
}
