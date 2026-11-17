<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations;

class SettingBlueprint
{
    public const TYPE_INTERNAL = 'internal';
    public const TYPE_CONFIG   = 'config';
    public const TYPE_TEXT     = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_BOOL     = 'bool';
    public const TYPE_RADIO = 'radio';
    public const TYPE_CONFIG_TEXT = 'config_text';
    public const TYPE_CONFIG_PASSWORD = 'config_password';

    private bool $required;

    /**
     * @return array
     */
    public static function getEditableTypes(): array
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_PASSWORD,
            self::TYPE_BOOL,
            self::TYPE_RADIO,
            self::TYPE_CONFIG_TEXT,
            self::TYPE_CONFIG_PASSWORD,
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
     * @param string $attributes
     * @param string $value
     */
    public function __construct(
        private $type,
        private $handle,
        private $label,
        private $instructions,
        $required = false,
        private $attributes = "",
        private $value = "",
        private ?string $group = null,
        private array $groupToggle = [],
        private array $choices = [],
        private $existingValue = null
    ) {
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
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return string
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getValue(bool $isNew = true)
    {
        return !$isNew && $this->existingValue !== null ? $this->existingValue : $this->value;
    }

    /**
     * @return bool
     */
    public function isSecret(): bool
    {
        return in_array($this->type, [self::TYPE_PASSWORD, self::TYPE_CONFIG_PASSWORD], true);
    }

    public function getInputType(): string
    {
        if ($this->type === self::TYPE_RADIO) {
            return 'inline_radio';
        }

        return $this->type === self::TYPE_BOOL ? 'yes_no' : ($this->isSecret() ? 'password' : 'text');
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getGroupToggle(): array
    {
        return $this->groupToggle;
    }

    public function isEditable(): bool
    {
        return in_array($this->getType(), self::getEditableTypes(), true);
    }
}
