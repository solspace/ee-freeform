<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Services\FieldsService;

/**
 * @property int       $id
 * @property int       $siteId
 * @property string    $type
 * @property string    $handle
 * @property string    $label
 * @property bool      $required
 * @property string    $groupValueType
 * @property string    $value
 * @property bool      $checked
 * @property string    $placeholder
 * @property string    $instructions
 * @property array     $values
 * @property array     $options
 * @property int       $notificationId
 * @property int       $assetSourceId
 * @property int       $rows
 * @property array     $fileKinds
 * @property int       $maxFileSizeKB
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property array     $additionalProperties
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
    protected $dateCreated;
    protected $dateUpdated;
    protected $additionalProperties;

    protected static $_events = ['afterSave', 'afterDelete', 'beforeInsert', 'beforeUpdate', 'beforeSave'];

    protected static $_typed_columns = [
        'values'               => 'json',
        'options'              => 'json',
        'fileKinds'            => 'json',
        'additionalProperties' => 'json',
    ];

    /**
     * @return array
     */
    public static function createValidationRules()
    {
        return [
            'label'  => 'required',
            'handle' => 'required',
        ];
    }

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

        if (in_array($this->type, [FieldInterface::TYPE_CHECKBOX_GROUP, FieldInterface::TYPE_MULTIPLE_SELECT], true)) {
            $returnArray['showCustomValues'] = $this->hasCustomOptionValues();
            $returnArray['values']           = $this->values ?: [];
            $returnArray['options']          = $this->options ?: [];
        }

        if ($this->type === FieldInterface::TYPE_FILE) {
            $returnArray['assetSourceId'] = (int) $this->assetSourceId ?: 0;
            $returnArray['maxFileSizeKB'] = (int) $this->maxFileSizeKB ?: FileUploadField::DEFAULT_MAX_FILESIZE_KB;
            $returnArray['fileCount']     = (int) $this->getAdditionalProperty('fileCount', FileUploadField::DEFAULT_FILE_COUNT);
            $returnArray['fileKinds']     = $this->fileKinds ?: ['image', 'pdf'];
        }

        if (in_array($this->type, [FieldInterface::TYPE_RADIO_GROUP, FieldInterface::TYPE_SELECT], true)) {
            $returnArray['showCustomValues'] = $this->hasCustomOptionValues();
            $returnArray['value']            = $this->value ?: '';
            $returnArray['options']          = $this->options ?: [];
        }

        if ($this->type === FieldInterface::TYPE_DATETIME) {
            $returnArray['value']               = $this->value ?: '';
            $returnArray['placeholder']         = $this->placeholder ?: '';
            $returnArray['initialValue']        = $this->getAdditionalProperty('initialValue');
            $returnArray['dateTimeType']        = $this->getAdditionalProperty('dateTimeType', 'both');
            $returnArray['generatePlaceholder'] = $this->getAdditionalProperty('generatePlaceholder', true);
            $returnArray['dateOrder']           = $this->getAdditionalProperty('dateOrder', 'ymd');
            $returnArray['date4DigitYear']      = $this->getAdditionalProperty('date4DigitYear', true);
            $returnArray['dateLeadingZero']     = $this->getAdditionalProperty('dateLeadingZero', true);
            $returnArray['dateSeparator']       = $this->getAdditionalProperty('dateSeparator', '/');
            $returnArray['clock24h']            = $this->getAdditionalProperty('clock24h', false);
            $returnArray['lowercaseAMPM']       = $this->getAdditionalProperty('lowercaseAMPM', true);
            $returnArray['clockSeparator']      = $this->getAdditionalProperty('clockSeparator', ':');
            $returnArray['clockAMPMSeparate']   = $this->getAdditionalProperty('clockAMPMSeparate', true);
            $returnArray['useDatepicker']       = $this->getAdditionalProperty('useDatepicker', true);
        }

        if ($this->type === FieldInterface::TYPE_TABLE) {
            $returnArray['layout']    = $this->getAdditionalProperty('layout', []);
            $returnArray['maxRows']   = $this->getAdditionalProperty('maxRows', 0);
            $returnArray['useScript'] = $this->getAdditionalProperty('useScript', true);
        }

        if ($this->type === FieldInterface::TYPE_NUMBER) {
            $returnArray['value']              = $this->value ?: '';
            $returnArray['placeholder']        = $this->placeholder ?: '';
            $returnArray['minLength']          = $this->getAdditionalProperty('minLength', '');
            $returnArray['maxLength']          = $this->getAdditionalProperty('maxLength', '');
            $returnArray['minValue']           = $this->getAdditionalProperty('minValue', '');
            $returnArray['maxValue']           = $this->getAdditionalProperty('maxValue', '');
            $returnArray['decimalCount']       = $this->getAdditionalProperty('decimalCount', 0);
            $returnArray['decimalSeparator']   = $this->getAdditionalProperty('decimalSeparator', '.');
            $returnArray['thousandsSeparator'] = $this->getAdditionalProperty('thousandsSeparator', ',');
            $returnArray['allowNegative']      = $this->getAdditionalProperty('allowNegative', false);
        }

        if ($this->type === FieldInterface::TYPE_RATING) {
            $returnArray['value']         = (int) $this->value;
            $returnArray['maxValue']      = $this->getAdditionalProperty('maxValue', 5);
            $returnArray['colorIdle']     = $this->getAdditionalProperty('colorIdle', '#ddd');
            $returnArray['colorHover']    = $this->getAdditionalProperty('colorHover', 'gold');
            $returnArray['colorSelected'] = $this->getAdditionalProperty('colorSelected', '#f70');
        }

        if ($this->type === FieldInterface::TYPE_REGEX) {
            $returnArray['value']       = $this->value ?: '';
            $returnArray['placeholder'] = $this->placeholder ?: '';
            $returnArray['pattern']     = $this->getAdditionalProperty('pattern', '');
            $returnArray['message']     = $this->getAdditionalProperty('message', '');
        }

        if ($this->type === FieldInterface::TYPE_CONFIRMATION) {
            $returnArray['value']         = $this->value ?: '';
            $returnArray['placeholder']   = $this->placeholder ?: '';
            $returnArray['targetFieldId'] = $this->getAdditionalProperty('targetFieldId');
        }

        if ($this->type === FieldInterface::TYPE_CONFIRMATION) {
            $returnArray['value']         = $this->value ?: '';
            $returnArray['placeholder']   = $this->placeholder ?: '';
            $returnArray['targetFieldId'] = $this->getAdditionalProperty('targetFieldId');
        }

        if ($this->type === FieldInterface::TYPE_PHONE) {
            $returnArray['value']       = $this->value ?: '';
            $returnArray['placeholder'] = $this->placeholder ?: '';
            $returnArray['pattern']     = $this->getAdditionalProperty('pattern');
        }

        if ($this->type === FieldInterface::TYPE_WEBSITE) {
            $returnArray['value']       = $this->value ?: '';
            $returnArray['placeholder'] = $this->placeholder ?: '';
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
     * @param bool  $forceLabelToValue
     */
    public function setPostValues(array $postValues, $forceLabelToValue = false)
    {
        $labels           = $postValues['labels'];
        $values           = $postValues['values'];
        $checkedByDefault = $postValues['checked_by_default'];

        $savableValue   = null;
        $savableValues  = [];
        $savableOptions = [];
        foreach ($labels as $index => $label) {
            $value = $values[$index];

            if (empty($label) && empty($value)) {
                continue;
            }

            $fieldValue = $value;
            if (empty($label)) {
                $fieldLabel = $value;
            } else {
                $fieldValue = $value;
                $fieldLabel = $label;
            }

            if ($forceLabelToValue) {
                $fieldValue = $fieldLabel;
            }

            $isChecked = (bool) $checkedByDefault[$index];
            if ($isChecked) {
                switch ($this->type) {
                    case FieldInterface::TYPE_CHECKBOX_GROUP:
                    case FieldInterface::TYPE_MULTIPLE_SELECT:
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
     * Determines if the submission table should get a column for this field or not
     *
     * @return bool
     */
    public function canStoreValues()
    {
        return $this->type !== FieldInterface::TYPE_CONFIRMATION;
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
            case FieldInterface::TYPE_FILE:
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_EMAIL:
            case FieldInterface::TYPE_TEXTAREA:
            case FieldInterface::TYPE_TABLE:
                $columnType = 'TEXT';

                break;
        }

        return $columnType;
    }

    /**
     * @return bool
     */
    public function isSerializable()
    {
        switch ($this->type) {
            case FieldInterface::TYPE_FILE:
            case FieldInterface::TYPE_CHECKBOX_GROUP:
            case FieldInterface::TYPE_DYNAMIC_RECIPIENTS:
            case FieldInterface::TYPE_EMAIL:
            case FieldInterface::TYPE_TABLE:
                return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    public function getAdditionalProperty($name, $defaultValue = null)
    {
        if (is_array($this->additionalProperties) && isset($this->additionalProperties[$name])) {
            $value = $this->additionalProperties[$name];

            if (null === $value) {
                return $defaultValue;
            }

            return $this->getCleanedPropertyValue($name, $value);
        }

        return $defaultValue;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAdditionalProperty($name, $value)
    {
        $props = $this->additionalProperties ?: [];

        $props[$name] = $this->getCleanedPropertyValue($name, $value);

        $this->set(['additionalProperties' => $props]);

        return $this;
    }

    /**
     * Add a new column in the submissions table for this field
     */
    public function onAfterSave()
    {
        if (!$this->canStoreValues()) {
            return;
        }

        $columnName = SubmissionModel::getFieldColumnName($this->id);
        $type       = $this->getColumnType();

        try {
            ee()->db->query("ALTER TABLE exp_freeform_next_submissions ADD COLUMN $columnName $type NULL DEFAULT NULL");
        } catch (\Exception $e) {
        }
    }

    /**
     * Drop the associated field column in submissions
     */
    public function onAfterDelete()
    {
        $columnName = SubmissionModel::getFieldColumnName($this->id);

        try {
            ee()->db->query("ALTER TABLE exp_freeform_next_submissions DROP COLUMN $columnName");
        } catch (\Exception $e) {
        }

        static $fieldsService;

        if (null === $fieldsService) {
            $fieldsService = new FieldsService();
        }

        $fieldsService->deleteFieldFromForms($this);
    }

    /**
     * Event beforeInsert sets the $dateCreated and $dateUpdated properties
     */
    public function onBeforeInsert()
    {
        $this->set(
            [
                'dateCreated' => $this->getTimestampableDate(),
                'dateUpdated' => $this->getTimestampableDate(),
            ]
        );
    }

    /**
     * Event beforeUpdate sets the $dateUpdated property
     */
    public function onBeforeUpdate()
    {
        $this->set(['dateUpdated' => $this->getTimestampableDate()]);
    }

    /**
     * @return \DateTime
     */
    private function getTimestampableDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Event beforeSave validates the form
     */
    public function onBeforeSave()
    {
        FreeformHelper::get('validate', $this);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    private function getCleanedPropertyValue($name, $value)
    {
        static $customTypes = [
            'generatePlaceholder' => 'bool',
            'date4DigitYear'      => 'bool',
            'dateLeadingZero'     => 'bool',
            'clock24h'            => 'bool',
            'clockLeadingZero'    => 'bool',
            'lowercaseAMPM'       => 'bool',
            'allowNegative'       => 'bool',
            'minLength'           => 'int',
            'maxLength'           => 'int',
            'minValue'            => 'int',
            'maxValue'            => 'int',
            'decimalCount'        => 'int',
            'fileCount'           => 'int',
        ];

        if (isset($customTypes[$name])) {
            switch ($customTypes[$name]) {
                case 'bool':
                    return (bool) $value ? true : false;

                case 'int':
                    return !empty($value) ? (int) $value : null;
            }
        }

        return $value;
    }
}
