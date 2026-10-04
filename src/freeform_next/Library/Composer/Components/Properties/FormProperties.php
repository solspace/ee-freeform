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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

class FormProperties extends AbstractProperties
{
    public const SUCCESS_BEHAVIOR_RETURN_URL = 'returnUrl';
    public const SUCCESS_BEHAVIOR_MESSAGE = 'message';
    public const DEFAULT_SUCCESS_MESSAGE = 'Thank you! Your submission has been received.';
    public const DEFAULT_ERROR_MESSAGE = 'Please correct the errors below.';

    /** @var string */
    protected $name;

    /** @var string */
    protected $handle;

    /** @var string */
    protected $color;

    /** @var string */
    protected $submissionTitleFormat;

    /** @var string */
    protected $description;

    /** @var string */
    protected $returnUrl;

    protected $successBehavior;

    protected $successMessage;

    protected $errorMessage;

    /** @var bool */
    protected $storeData;

    /** @var bool */
    protected $useAjax;

    /** @var int */
    protected $defaultStatus;

    /** @var string */
    protected $formTemplate;

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
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getSubmissionTitleFormat()
    {
        return $this->submissionTitleFormat;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function getSuccessBehavior(): string
    {
        return $this->successBehavior === self::SUCCESS_BEHAVIOR_MESSAGE
            ? self::SUCCESS_BEHAVIOR_MESSAGE
            : self::SUCCESS_BEHAVIOR_RETURN_URL;
    }

    public function getSuccessMessage(): string
    {
        return trim((string) $this->successMessage) ?: self::DEFAULT_SUCCESS_MESSAGE;
    }

    public function getErrorMessage(): string
    {
        return trim((string) $this->errorMessage) ?: self::DEFAULT_ERROR_MESSAGE;
    }

    /**
     * @return boolean
     */
    public function isStoreData(): bool
    {
        return null !== $this->storeData ? (bool)$this->storeData : true;
    }

    public function isUseAjax(): bool
    {
        return null !== $this->useAjax ? (bool) $this->useAjax : true;
    }

    /**
     * @return int
     */
    public function getDefaultStatus()
    {
        return $this->defaultStatus;
    }

    /**
     * @return string
     */
    public function getFormTemplate()
    {
        return $this->formTemplate;
    }

    /**
     * Return a list of all property fields and their type
     *
     * [propertyKey => propertyType, ..]
     * E.g. ["name" => "string", ..]
     *
     * @return array
     */
    protected function getPropertyManifest(): array
    {
        return [
            'name'                  => self::TYPE_STRING,
            'handle'                => self::TYPE_STRING,
            'color'                 => self::TYPE_STRING,
            'submissionTitleFormat' => self::TYPE_STRING,
            'description'           => self::TYPE_STRING,
            'returnUrl'             => self::TYPE_STRING,
            'successBehavior'       => self::TYPE_STRING,
            'successMessage'        => self::TYPE_STRING,
            'errorMessage'          => self::TYPE_STRING,
            'storeData'             => self::TYPE_BOOLEAN,
            'useAjax'               => self::TYPE_BOOLEAN,
            'defaultStatus'         => self::TYPE_INTEGER,
            'formTemplate'          => self::TYPE_STRING,
        ];
    }
}
