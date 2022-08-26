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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes;

class CustomFormAttributes extends AbstractAttributes
{
    /** @var string */
    protected $returnUrl;

    /** @var string - Alias of $returnUrl */
    protected $return;

    /** @var string */
    protected $inputClass;

    /** @var string */
    protected $submitClass;

    /** @var string */
    protected $rowClass;

    /** @var string */
    protected $columnClass;

    /** @var string */
    protected $labelClass;

    /** @var string */
    protected $errorClass;

    /** @var array of strings */
    protected $class;

    /** @var string */
    protected $instructionsClass;

    /** @var bool */
    protected $instructionsBelowField;

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $method;

    /** @var string */
    protected $action;

    /** @var array */
    protected $dynamicNotificationRecipients;

    /** @var string */
    protected $dynamicNotificationTemplate;

    /** @var array */
    protected $overrideValues;

    /** @var bool */
    protected $useRequiredAttribute;

    /** @var array */
    protected $formAttributes;

    /** @var array */
    protected $inputAttributes;

    /** @var bool */
    protected $useActionUrl;

    /** @var string */
    protected $fieldIdPrefix;

    /** @var string */
    protected $submissionToken;

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        if (null === $this->returnUrl && null !== $this->return) {
            return $this->return;
        }

        return $this->returnUrl;
    }

    /**
     * @return string
     */
    public function getInputClass()
    {
        return $this->extractClassValue($this->inputClass);
    }

    /**
     * @return string
     */
    public function getSubmitClass()
    {
        return $this->extractClassValue($this->submitClass);
    }

    /**
     * @return string
     */
    public function getRowClass()
    {
        return $this->extractClassValue($this->rowClass);
    }

    /**
     * @return string
     */
    public function getColumnClass()
    {
        return $this->extractClassValue($this->columnClass);
    }

    /**
     * @return string
     */
    public function getLabelClass()
    {
        return $this->extractClassValue($this->labelClass);
    }

    /**
     * @return string
     */
    public function getErrorClass()
    {
        return $this->extractClassValue($this->errorClass);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->extractClassValue($this->class);
    }

    /**
     * @return string
     */
    public function getInstructionsClass()
    {
        return $this->instructionsClass;
    }

    /**
     * @return boolean
     */
    public function isInstructionsBelowField()
    {
        return $this->instructionsBelowField;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getDynamicNotificationRecipients()
    {
        if (null === $this->dynamicNotificationRecipients) {
            return null;
        }

        return explode('|', $this->dynamicNotificationRecipients);
    }

    /**
     * @return string
     */
    public function getDynamicNotificationTemplate()
    {
        return $this->dynamicNotificationTemplate;
    }

    /**
     * @return array
     */
    public function getOverrideValues()
    {
        return $this->overrideValues;
    }

    /**
     * @return boolean
     */
    public function getUseRequiredAttribute()
    {
        return $this->useRequiredAttribute;
    }

    /**
     * @return string|null
     */
    public function getFieldIdPrefix()
    {
        return $this->fieldIdPrefix;
    }

    /**
     * @return string|null
     */
    public function getSubmissionToken()
    {
        return $this->submissionToken;
    }

    /**
     * @return bool
     */
    public function isUseActionUrl()
    {
        $val = $this->useActionUrl;
        if (
            $val === null ||
            $val === false ||
            strtolower($val) === 'n' ||
            strtolower($val) === 'no'
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getFormAttributes()
    {
        if (null === $this->formAttributes) {
            return $this->formAttributes;
        }

        if (!is_array($this->formAttributes)) {
            return [$this->formAttributes];
        }

        return $this->formAttributes;
    }

    /**
     * @return string
     */
    public function getFormAttributesAsString()
    {
        $formAttributes = $this->getFormAttributes() ?: [];

        return $this->getAttributeStringFromArray($formAttributes);
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
     * @return array
     */
    public function getManifest()
    {
        $manifest = array_keys(get_object_vars($this));

        return array_diff(
            $manifest, [
                'overrideValues',
                'inputAttributes',
                'formAttributes',
            ]
        );
    }
}
