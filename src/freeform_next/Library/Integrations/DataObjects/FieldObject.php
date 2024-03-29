<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations\DataObjects;

class FieldObject implements \JsonSerializable
{
    const TYPE_STRING  = 'string';
    const TYPE_ARRAY   = 'array';
    const TYPE_NUMERIC = 'numeric';
    const TYPE_BOOLEAN = 'boolean';

    /** @var string */
    private $handle;

    /** @var string */
    private $label;

    /** @var bool */
    private $required;

    /** @var string */
    private $type;

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [self::TYPE_STRING, self::TYPE_NUMERIC, self::TYPE_BOOLEAN, self::TYPE_ARRAY];
    }

    /**
     * @return string
     */
    public static function getDefaultType()
    {
        return self::TYPE_STRING;
    }

    /**
     * @param string $handle
     * @param string $label
     * @param string $type
     * @param bool   $required
     */
    public function __construct($handle, $label, $type, $required = false)
    {
        $this->handle = $handle;
        $this->label = $label;
        $this->type = $type;
        $this->required = (bool)$required;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return (bool)$this->required;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return [
            'handle'   => $this->getHandle(),
            'label'    => $this->getLabel(),
            'required' => $this->isRequired(),
        ];
    }
}
