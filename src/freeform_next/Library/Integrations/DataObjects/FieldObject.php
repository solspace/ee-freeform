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

namespace Solspace\Addons\FreeformNext\Library\Integrations\DataObjects;

class FieldObject implements \JsonSerializable
{
    const TYPE_STRING  = "string";
    const TYPE_ARRAY   = "array";
    const TYPE_NUMERIC = "numeric";
    const TYPE_BOOLEAN = "boolean";

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
     * Convert a given value to a type specific value
     *
     * @param mixed $value
     *
     * @return bool|int|string
     */
    public function convertValue($value)
    {
        switch ($this->type) {
            case self::TYPE_NUMERIC:
                return (int)preg_replace("/[^0-9]/", "", $value) ?: "";

            case self::TYPE_BOOLEAN:
                return (bool)$value;

            case self::TYPE_ARRAY:
                return implode(";", $value);

            case self::TYPE_STRING:
                if (is_array($value)) {
                    $value = implode(", ", $value);
                }

                return (string)$value;
        }

        return $value;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return [
            "handle"   => $this->getHandle(),
            "label"    => $this->getLabel(),
            "required" => $this->isRequired(),
        ];
    }
}
