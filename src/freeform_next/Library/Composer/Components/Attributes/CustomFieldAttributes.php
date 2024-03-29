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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;

class CustomFieldAttributes extends AbstractAttributes
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $class;

    /** @var string */
    protected $labelClass;

    /** @var string */
    protected $errorClass;

    /** @var string */
    protected $instructionsClass;

    /** @var bool */
    protected $instructionsBelowField;

    /** @var string */
    protected $placeholder;

    /** @var mixed */
    protected $overrideValue;

    /** @var bool */
    protected $useRequiredAttribute;

    /** @var array */
    protected $inputAttributes;

    /** @var string */
    protected $addButtonLabel;

    /** @var string */
    protected $addButtonClass;

    /** @var string */
    protected $removeButtonLabel;

    /** @var string */
    protected $removeButtonClass;

    /** @var string */
    protected $tableTextInputClass;

    /** @var string */
    protected $tableCheckboxInputClass;

    /** @var string */
    protected $tableSelectInputClass;

    /** @var AbstractField */
    private $field;

    /** @var CustomFormAttributes */
    private $formAttributes;

    /**
     * CustomFieldAttributes constructor.
     *
     * @param AbstractField             $field
     * @param array|null                $attributes
     * @param CustomFormAttributes|null $formAttributes
     */
    public function __construct(
        AbstractField $field,
        array $attributes = null,
        CustomFormAttributes $formAttributes = null
    ) {
        parent::__construct($attributes);

        $this->field          = $field;
        $this->formAttributes = $formAttributes;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        $value = $this->class;
        if (null !== $this->formAttributes) {
            $value = $this->combineClassStrings($value, $this->formAttributes->getInputClass());
        }

        return $this->extractClassValue($value);
    }

    /**
     * @return string
     */
    public function getInputClassOnly()
    {
        return $this->extractClassValue($this->class);
    }

    /**
     * @return string
     */
    public function getLabelClass()
    {
        $value = $this->labelClass;
        if (null !== $this->formAttributes) {
            $value = $this->combineClassStrings($value, $this->formAttributes->getLabelClass());
        }

        return $this->extractClassValue($value);
    }

    /**
     * @return string
     */
    public function getErrorClass()
    {
        $value = $this->errorClass;
        if (null !== $this->formAttributes) {
            $value = $this->combineClassStrings($value, $this->formAttributes->getErrorClass());
        }

        return $this->extractClassValue($value);
    }

    /**
     * @return string
     */
    public function getInstructionsClass()
    {
        $value = $this->instructionsClass;
        if (null !== $this->formAttributes) {
            $value = $this->combineClassStrings($value, $this->formAttributes->getInstructionsClass());
        }

        return $this->extractClassValue($value);
    }

    /**
     * @return boolean
     */
    public function isInstructionsBelowField()
    {
        $value = $this->instructionsBelowField;
        if (!$value && null !== $this->formAttributes) {
            $value = $this->formAttributes->isInstructionsBelowField();
        }

        return $this->getBooleanValue($value);
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @return mixed
     */
    public function getOverrideValue()
    {
        $value = $this->overrideValue;

        if (null === $value && null !== $this->formAttributes) {
            $overrideValues = $this->formAttributes->getOverrideValues();

            if ($overrideValues && isset($overrideValues[$this->field->getHandle()])) {
                $value = $overrideValues[$this->field->getHandle()];
            }
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getInputAttributes()
    {
        if (null === $this->inputAttributes) {
            return $this->inputAttributes;
        }

        if (!is_array($this->inputAttributes)) {
            return [$this->inputAttributes];
        }

        return $this->inputAttributes;
    }

    /**
     * @return string
     */
    public function getInputAttributesAsString()
    {
        $formInputAttributes = $this->formAttributes ? $this->formAttributes->getInputAttributes() : [];
        $inputAttributes = $this->getInputAttributes();

        if ($formInputAttributes) {
            if ($inputAttributes) {
                $inputAttributes = array_merge($formInputAttributes, $inputAttributes);
            } else {
                $inputAttributes = $formInputAttributes;
            }
        }

        if (!is_array($inputAttributes)) {
            $inputAttributes = [];
        }

        return $this->getAttributeStringFromArray($inputAttributes);
    }

    /**
     * @return boolean
     */
    public function getUseRequiredAttribute()
    {
        $value = $this->useRequiredAttribute;

        if (null === $value && null !== $this->formAttributes) {
            $value = $this->formAttributes->getUseRequiredAttribute();
        }

        return $value;
    }

    /**
     * @return string|null
     */
    public function getFieldIdPrefix()
    {
        return $this->formAttributes->getFieldIdPrefix();
    }

    /**
     * @return string
     */
    public function getAddButtonLabel()
    {
        return $this->addButtonLabel;
    }

    /**
     * @return string
     */
    public function getAddButtonClass()
    {
        return $this->addButtonClass;
    }

    /**
     * @return string
     */
    public function getRemoveButtonLabel()
    {
        return $this->removeButtonLabel;
    }

    /**
     * @return string
     */
    public function getRemoveButtonClass()
    {
        return $this->removeButtonClass;
    }

    /**
     * @return string
     */
    public function getTableTextInputClass()
    {
        return $this->tableTextInputClass;
    }

    /**
     * @return string
     */
    public function getTableCheckboxInputClass()
    {
        return $this->tableCheckboxInputClass;
    }

    /**
     * @return string
     */
    public function getTableSelectInputClass()
    {
        return $this->tableSelectInputClass;
    }

    /**
     * Takes a two class strings, explodes them into arrays, merges, then returns a glued string
     *
     * @param string $classStringA
     * @param string $classStringB
     *
     * @return string
     */
    private function combineClassStrings($classStringA = null, $classStringB = null)
    {
        $classListA = explode(' ', $classStringA ?: '');
        $classListB = explode(' ', $classStringB ?: '');

        $combined = array_merge($classListA, $classListB);
        $combined = array_unique($combined);
        $combined = array_filter($combined);

        return implode(' ', $combined);
    }
}
