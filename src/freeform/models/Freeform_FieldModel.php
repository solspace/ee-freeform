<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Craft;

use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Freeform\Library\Helpers\HashHelper;

/**
 * Class Freeform_FieldModel
 *
 * @property int    $id
 * @property string $type
 * @property string $handle
 * @property string $label
 * @property bool   $required
 * @property string $groupValueType
 * @property string $value
 * @property bool   $checked
 * @property string $placeholder
 * @property string $instructions
 * @property array  $values
 * @property array  $options
 * @property int    $notificationId
 * @property int    $assetSourceId
 * @property int    $rows
 * @property array  $fileKinds
 * @property int    $maxFileSizeKB
 */
class Freeform_FieldModel extends BaseModel implements \JsonSerializable
{
    const SMALL_DATA_STORAGE_LENGTH  = 100;

    public static function create()
    {
        $field = new static();

        return $field;
    }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return HashHelper::hash($this->id);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $returnArray = [
            "id"           => (int)$this->id,
            "hash"         => $this->getHash(),
            "type"         => $this->type,
            "handle"       => $this->handle,
            "label"        => $this->label,
            "required"     => (bool)$this->required,
            "instructions" => (string)$this->instructions,
        ];

        if (in_array(
            $this->type,
            [
                FieldInterface::TYPE_TEXT,
                FieldInterface::TYPE_TEXTAREA,
                FieldInterface::TYPE_HIDDEN,
            ]
        )) {
            $returnArray["value"]       = $this->value ?: "";
            $returnArray["placeholder"] = $this->placeholder ?: "";
        }

        if ($this->type === FieldInterface::TYPE_TEXTAREA) {
            $returnArray["rows"] = (int)$this->rows ?: 2;
        }

        if ($this->type === FieldInterface::TYPE_CHECKBOX) {
            $returnArray["value"]   = $this->value ?: "Yes";
            $returnArray["checked"] = (bool)$this->checked;
        }

        if ($this->type === FieldInterface::TYPE_EMAIL) {
            $returnArray["notificationId"] = 0;
            $returnArray["values"]         = [];
            $returnArray["placeholder"]    = $this->placeholder ?: "";
        }

        if ($this->type === FieldInterface::TYPE_DYNAMIC_RECIPIENTS) {
            $returnArray["notificationId"] = 0;
            $returnArray["value"]          = 0;
            $returnArray["options"]        = $this->options ?: [];
            $returnArray["showAsRadio"]    = false;
        }

        if ($this->type === FieldInterface::TYPE_CHECKBOX_GROUP) {
            $returnArray["showCustomValues"] = $this->hasCustomOptionValues();
            $returnArray["values"]           = $this->values ?: [];
            $returnArray["options"]          = $this->options ?: [];
        }

        if ($this->type === FieldInterface::TYPE_FILE) {
            $returnArray["assetSourceId"] = (int)$this->assetSourceId ?: 0;
            $returnArray["maxFileSizeKB"] = (int)$this->maxFileSizeKB ?: FileUploadField::DEFAULT_MAX_FILESIZE_KB;
            $returnArray["fileKinds"]     = $this->fileKinds ?: ["image", "pdf"];
        }

        if (in_array($this->type, [FieldInterface::TYPE_RADIO_GROUP, FieldInterface::TYPE_SELECT])) {
            $returnArray["showCustomValues"] = $this->hasCustomOptionValues();
            $returnArray["value"]            = $this->value ?: "";
            $returnArray["options"]          = $this->options ?: [];
        }

        if (in_array(
            $this->type,
            [FieldInterface::TYPE_HIDDEN, FieldInterface::TYPE_HTML, FieldInterface::TYPE_SUBMIT]
        )) {
            unset($returnArray["instructions"]);
        }

        return $returnArray;
    }

    /**
     * @param array $postValues
     */
    public function setPostValues(array $postValues)
    {
        $labels           = $postValues["labels"];
        $values           = $postValues["values"];
        $checkedByDefault = $postValues["checked"];

        $savableValue   = null;
        $savableValues  = [];
        $savableOptions = [];
        foreach ($labels as $index => $label) {
            $value = $values[$index];

            if (empty($label) && empty($value)) {
                continue;
            }

            if (empty($label) || empty($value)) {
                $fieldValue = $fieldLabel = (!empty($label) ? $label : $value);
            } else {
                $fieldValue = $value;
                $fieldLabel = $label;
            }

            $isChecked = (bool)$checkedByDefault[$index];
            if ($isChecked) {
                switch ($this->type) {
                    case FieldInterface::TYPE_CHECKBOX_GROUP:
                        $savableValues[] = $fieldValue;
                        break;

                    case FieldInterface::TYPE_RADIO_GROUP:
                    case FieldInterface::TYPE_SELECT:
                    case FieldInterface::TYPE_DYNAMIC_RECIPIENTS:
                        $savableValue = $fieldValue;
                        break;
                }
            }

            $item        = new \stdClass();
            $item->value = $fieldValue;
            $item->label = $fieldLabel;

            $savableOptions[] = $item;
        }

        $this->options = !empty($savableOptions) ? $savableOptions : null;
        $this->values  = !empty($savableValues) ? $savableValues : null;
        $this->value   = !empty($savableValue) ? $savableValue : null;
        $this->checked = null;
    }

    /**
     * @return bool
     */
    public function hasCustomOptionValues()
    {
        $options = $this->options;
        if (empty($options)) {
            return false;
        }

        foreach ($options as $valueData) {
            if (is_object($valueData)) {
                $valueData = (array)$valueData;
            }

            if ($valueData["value"] !== $valueData["label"]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Depending on the field type - return its attribute type for the model and record
     *
     * @return string
     */
    public function getAttributeType()
    {
        $attributeType = AttributeType::String;

        switch ($this->type) {
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_EMAIL:
                $attributeType = AttributeType::Mixed;

                break;
        }

        return $attributeType;
    }

    /**
     * Depending on the field type - return its column type for the database
     *
     * @return string
     */
    public function getColumnType()
    {
        $columnType = [ColumnType::Varchar, "length" => self::SMALL_DATA_STORAGE_LENGTH];

        switch ($this->type) {
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_EMAIL:
            case FieldInterface::TYPE_TEXTAREA:
                $columnType = [ColumnType::Text];

                break;
        }

        return $columnType;
    }

    /**
     * @return array
     */
    protected function defineAttributes()
    {
        return [
            "id"             => AttributeType::Number,
            "type"           => AttributeType::String,
            "handle"         => AttributeType::Handle,
            "label"          => AttributeType::String,
            "required"       => AttributeType::Bool,
            "value"          => AttributeType::String,
            "values"         => AttributeType::Mixed,
            "placeholder"    => AttributeType::String,
            "instructions"   => AttributeType::String,
            "options"        => AttributeType::Mixed,
            "checked"        => AttributeType::Bool,
            "notificationId" => AttributeType::Number,
            "assetSourceId"  => AttributeType::Number,
            "rows"           => AttributeType::Number,
            "fileKinds"      => AttributeType::Mixed,
            "maxFileSizeKB"  => AttributeType::Number,
        ];
    }
}
