<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations;

class SettingBlueprint
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_CONFIG   = 'config';
    const TYPE_TEXT     = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_BOOL     = 'bool';

    /** @var string */
    private $type;

    /** @var string */
    private $handle;

    /** @var string */
    private $label;

    /** @var string */
    private $instructions;

    /** @var bool */
    private $required;

    /**
     * @return array
     */
    public static function getEditableTypes()
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_PASSWORD,
            self::TYPE_BOOL,
        ];
    }

    /**
     * SettingObject constructor.
     *
     * @param string $type
     * @param string $handle
     * @param string $label
     * @param string $instructions
     * @param bool   $required
     */
    public function __construct(
        $type,
        $handle,
        $label,
        $instructions,
        $required = false
    ) {
        $this->type         = $type;
        $this->handle       = $handle;
        $this->label        = $label;
        $this->instructions = $instructions;
        $this->required     = (bool)$required;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return in_array($this->getType(), self::getEditableTypes(), true);
    }
}
