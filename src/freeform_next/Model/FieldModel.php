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

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;

/**
 * Class FieldModel
 *
 * @property int    $id
 * @property int    $siteId
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
class FieldModel extends Model implements \JsonSerializable
{
    const MODEL = 'freeform_next:FieldModel';
    const TABLE = 'freeform_next_fields';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $type;
    protected $handle;
    protected $label;
    protected $required;
    protected $value;
    protected $checked;
    protected $placeholder;
    protected $instructions;
    protected $values;
    protected $options;
    protected $notificationId;
    protected $assetSourceId;
    protected $rows;
    protected $fileKinds;
    protected $maxFileSizeKB;

    protected static $_events = ['afterSave', 'afterDelete'];

    /**
     * Creates a Field object with default settings
     *
     * @return FieldModel
     */
    public static function create()
    {
        /** @var FieldModel $field */
        $field = ee('Model')->make(
            self::MODEL,
            [
                'siteId'   => ee()->config->item('site_id'),
                'required' => false,
            ]
        );

        return $field;
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
            'id'           => (int) $this->id,
            'hash'         => $this->getHash(),
            'type'         => $this->type,
            'handle'       => $this->handle,
            'label'        => $this->label,
            'required'     => (bool) $this->required,
            'instructions' => (string) $this->instructions,
        ];

        if (in_array(
            $this->type,
            [
                FieldInterface::TYPE_TEXT,
                FieldInterface::TYPE_TEXTAREA,
                FieldInterface::TYPE_HIDDEN,
            ],
            true
        )) {
            $returnArray['value']       = $this->value ?: '';
            $returnArray['placeholder'] = $this->placeholder ?: '';
        }

        if ($this->type === FieldInterface::TYPE_TEXTAREA) {
            $returnArray['rows'] = (int) $this->rows ?: 2;
        }

        if ($this->type === FieldInterface::TYPE_CHECKBOX) {
            $returnArray['value']   = $this->value ?: 'Yes';
            $returnArray['checked'] = (bool) $this->checked;
        }

        if ($this->type === FieldInterface::TYPE_EMAIL) {
            $returnArray['notificationId'] = 0;
            $returnArray['values']         = [];
            $returnArray['placeholder']    = $this->placeholder ?: '';
        }

        if ($this->type === FieldInterface::TYPE_DYNAMIC_RECIPIENTS) {
            $returnArray['notificationId'] = 0;
            $returnArray['value']          = 0;
            $returnArray['options']        = $this->options ?: [];
            $returnArray['showAsRadio']    = false;
        }

        if ($this->type === FieldInterface::TYPE_CHECKBOX_GROUP) {
            $returnArray['showCustomValues'] = $this->hasCustomOptionValues();
            $returnArray['values']           = $this->values ?: [];
            $returnArray['options']          = $this->options ?: [];
        }

        if ($this->type === FieldInterface::TYPE_FILE) {
            $returnArray['assetSourceId'] = (int) $this->assetSourceId ?: 0;
            $returnArray['maxFileSizeKB'] = (int) $this->maxFileSizeKB ?: FileUploadField::DEFAULT_MAX_FILESIZE_KB;
            $returnArray['fileKinds']     = $this->fileKinds ?: ['image', 'pdf'];
        }

        if (in_array($this->type, [FieldInterface::TYPE_RADIO_GROUP, FieldInterface::TYPE_SELECT], true)) {
            $returnArray['showCustomValues'] = $this->hasCustomOptionValues();
            $returnArray['value']            = $this->value ?: '';
            $returnArray['options']          = $this->options ?: [];
        }

        if (in_array(
            $this->type,
            [FieldInterface::TYPE_HIDDEN, FieldInterface::TYPE_HTML, FieldInterface::TYPE_SUBMIT],
            true
        )) {
            unset($returnArray['instructions']);
        }

        return $returnArray;
    }

    /**
     * @param array $postValues
     */
    public function setPostValues(array $postValues)
    {
        $labels           = $postValues['labels'];
        $values           = $postValues['values'];
        $checkedByDefault = $postValues['checked'];

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

            $isChecked = (bool) $checkedByDefault[$index];
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

        $this->set(
            [
                'options' => !empty($savableOptions) ? $savableOptions : null,
                'values'  => !empty($savableValues) ? $savableValues : null,
                'value'   => !empty($savableValue) ? $savableValue : null,
                'checked' => null,
            ]
        );
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
                $valueData = (array) $valueData;
            }

            if ($valueData['value'] !== $valueData['label']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Depending on the field type - return its column type for the database
     *
     * @return string
     */
    public function getColumnType()
    {
        $columnType = 'VARCHAR(255)';

        switch ($this->type) {
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_EMAIL:
            case FieldInterface::TYPE_TEXTAREA:
                $columnType = 'TEXT';

                break;
        }

        return $columnType;
    }

    /**
     * @return string
     */
    public function getEEColumnType()
    {
        $columnType = 'string';

        switch ($this->type) {
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_DYNAMIC_RECIPIENTS:
            case FieldInterface::TYPE_EMAIL:
            case FieldInterface::TYPE_RADIO_GROUP:
                $columnType = 'json';

                break;
        }

        return $columnType;
    }

    /**
     * Add a new column in the submissions table for this field
     */
    public function onAfterSave()
    {
        $columnName = SubmissionModel::getFieldColumnName($this->id);
        $type       = $this->getColumnType();

        ee()->db->query("ALTER TABLE exp_freeform_next_submissions ADD COLUMN $columnName $type NULL DEFAULT NULL");
    }

    /**
     * Drop the associated field column in submissions
     */
    public function onAfterDelete()
    {
        $columnName = SubmissionModel::getFieldColumnName($this->id);

        ee()->db->query("ALTER TABLE exp_freeform_next_submissions DROP COLUMN $columnName");
    }
}
