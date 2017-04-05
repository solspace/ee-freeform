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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;

class FieldProperties extends AbstractProperties
{
    /** @var string */
    protected $hash;

    /** @var int */
    protected $id;

    /** @var string */
    protected $handle;

    /** @var string */
    protected $label;

    /** @var boolean */
    protected $required;

    /** @var string */
    protected $placeholder;

    /** @var string */
    protected $instructions;

    /** @var string */
    protected $value;

    /** @var array */
    protected $values;

    /** @var array */
    protected $options;

    /** @var bool */
    protected $checked;

    /** @var bool */
    protected $showAsRadio;

    /** @var string */
    protected $notificationId;

    /** @var int */
    protected $assetSourceId;

    /** @var int */
    protected $integrationId;

    /** @var string */
    protected $resourceId;

    /** @var string */
    protected $emailFieldHash;

    /** @var string */
    protected $position;

    /** @var string */
    protected $labelNext;

    /** @var string */
    protected $labelPrev;

    /** @var bool */
    protected $disablePrev;

    /** @var array */
    protected $mapping;

    /** @var array */
    protected $fileKinds;

    /** @var int */
    protected $maxFileSizeKB;

    /** @var int */
    protected $rows;

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
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
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        $return = [];
        if (is_array($this->options)) {
            foreach ($this->options as $option) {
                $return[] = new Option($option["label"], $option["value"]);
            }
        }

        return $return;
    }

    /**
     * @return boolean
     */
    public function isChecked()
    {
        return (bool)$this->checked;
    }

    /**
     * @return boolean
     */
    public function isShowAsRadio()
    {
        return $this->showAsRadio;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * @return string
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @return int
     */
    public function getAssetSourceId()
    {
        return $this->assetSourceId;
    }

    /**
     * @return int
     */
    public function getIntegrationId()
    {
        return (int)$this->integrationId;
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return (string)$this->resourceId;
    }

    /**
     * @return string
     */
    public function getEmailFieldHash()
    {
        return (string)$this->emailFieldHash;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getLabelNext()
    {
        return $this->labelNext;
    }

    /**
     * @return string
     */
    public function getLabelPrev()
    {
        return $this->labelPrev;
    }

    /**
     * @return boolean
     */
    public function isDisablePrev()
    {
        return $this->disablePrev;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @return array
     */
    public function getFileKinds()
    {
        return $this->fileKinds;
    }

    /**
     * @return int
     */
    public function getMaxFileSizeKB()
    {
        return $this->maxFileSizeKB;
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Return a list of all property fields and their type
     *
     * [propertyKey => propertyType, ..]
     * E.g. ["name" => "string", ..]
     *
     * @return array
     */
    protected function getPropertyManifest()
    {
        return [
            'hash'           => self::TYPE_STRING,
            'id'             => self::TYPE_INTEGER,
            'handle'         => self::TYPE_STRING,
            'label'          => self::TYPE_STRING,
            'required'       => self::TYPE_BOOLEAN,
            'placeholder'    => self::TYPE_STRING,
            'instructions'   => self::TYPE_STRING,
            'value'          => self::TYPE_STRING,
            'values'         => self::TYPE_ARRAY,
            'options'        => self::TYPE_ARRAY,
            'checked'        => self::TYPE_BOOLEAN,
            'showAsRadio'    => self::TYPE_BOOLEAN,
            'notificationId' => self::TYPE_STRING,
            'assetSourceId'  => self::TYPE_INTEGER,
            'integrationId'  => self::TYPE_INTEGER,
            'resourceId'     => self::TYPE_STRING,
            'emailFieldHash' => self::TYPE_STRING,
            'position'       => self::TYPE_STRING,
            'labelNext'      => self::TYPE_STRING,
            'labelPrev'      => self::TYPE_STRING,
            'disablePrev'    => self::TYPE_BOOLEAN,
            'mapping'        => self::TYPE_ARRAY,
            'fileKinds'      => self::TYPE_ARRAY,
            'maxFileSizeKB'  => self::TYPE_INTEGER,
            'rows'           => self::TYPE_INTEGER,
        ];
    }
}
