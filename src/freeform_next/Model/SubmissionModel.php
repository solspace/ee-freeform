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
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

/**
 * @property int       $id
 * @property int       $siteId
 * @property int       $statusId
 * @property string    $statusName
 * @property string    $statusHandle
 * @property string    $statusColor
 * @property int       $formId
 * @property string    $title
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class SubmissionModel extends Model
{
    const MODEL = 'freeform_next:SubmissionModel';
    const TABLE = 'freeform_next_submissions';

    const FIELD_COLUMN_PREFIX = 'field_';

    /** @var AbstractField[] */
    private static $fieldMetadata = [];

    /** @var array */
    private static $handleToFieldIdMap = [];

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $statusId;
    protected $statusName;
    protected $statusHandle;
    protected $statusColor;
    protected $formId;
    protected $title;
    protected $dateCreated;
    protected $dateUpdated;

    /** @var array */
    private $fieldValues;

    /**
     * Get the submission table field column name
     *
     * @param int $fieldId
     *
     * @return string
     */
    public static function getFieldColumnName($fieldId)
    {
        return self::FIELD_COLUMN_PREFIX . $fieldId;
    }

    /**
     * Creates a Field object with default settings
     *
     * @param Form  $form
     * @param array $fetchedValues
     *
     * @return SubmissionModel
     */
    public static function create(Form $form, array $fetchedValues)
    {
        /** @var SubmissionModel $submission */
        $submission = ee('Model')->make(
            self::MODEL,
            [
                'siteId'   => ee()->config->item('site_id'),
                'formId'   => $form->getId(),
                'statusId' => $form->getDefaultStatus(),
            ]
        );

        foreach ($fetchedValues as $key => $value) {
            if (property_exists(__CLASS__, $key)) {
                $submission->{$key} = $value;
            } else if (preg_match('/^' . SubmissionModel::FIELD_COLUMN_PREFIX . '(\d+)$/', $key, $matches)) {
                $fieldId = (int) $matches[1];
                $submission->setFieldColumnValue($fieldId, $value);
            } else {
                $submission->setFieldValue($key, $value);
            }
        }

        return $submission;
    }

    /**
     * Creates a Field object with default settings
     *
     * @param Form  $form
     * @param array $fetchedValues
     *
     * @return SubmissionModel
     */
    public static function createFromDatabase(Form $form, array $fetchedValues)
    {
        /** @var SubmissionModel $submission */
        $submission = ee('Model')->make(
            self::MODEL,
            [
                'id'           => $fetchedValues['id'],
                'siteId'       => $fetchedValues['siteId'],
                'formId'       => $fetchedValues['formId'],
                'statusId'     => $fetchedValues['statusId'],
                'title'        => $fetchedValues['title'],
                'dateCreated'  => $fetchedValues['dateCreated'],
                'dateUpdated'  => $fetchedValues['dateUpdated'],
                'statusName'   => $fetchedValues['statusName'],
                'statusHandle' => $fetchedValues['statusHandle'],
                'statusColor'  => $fetchedValues['statusColor'],
            ]
        );

        $submission->_new = false;

        foreach ($fetchedValues as $key => $value) {
            if (preg_match('/^' . SubmissionModel::FIELD_COLUMN_PREFIX . '(\d+)$/', $key, $matches)) {
                $fieldId = (int) $matches[1];
                $submission->setFieldColumnValue($fieldId, $value);
            }
        }

        return $submission;
    }

    /**
     * @param int $formId
     *
     * @return AbstractField
     */
    private static function getFieldMetadataByFormId($formId)
    {
        if (!isset(self::$fieldMetadata[$formId])) {
            $form   = FormRepository::getInstance()->getFormById($formId);
            $fields = $form->getComposer()->getForm()->getLayout()->getFieldsByHandle();

            $metadataArray      = [];
            $handleToFieldIdMap = [];
            foreach ($fields as $field) {
                $id     = $field->getId();
                $handle = $field->getHandle();

                $metadataArray[$id]          = $field;
                $handleToFieldIdMap[$handle] = $id;
            }

            self::$fieldMetadata[$formId]      = $metadataArray;
            self::$handleToFieldIdMap[$formId] = $handleToFieldIdMap;
        }

        return self::$fieldMetadata[$formId];
    }

    /**
     * @param int $formId
     * @param int $fieldId
     *
     * @return AbstractField
     */
    private static function getFieldMetadataById($formId, $fieldId)
    {
        $metadata = self::getFieldMetadataByFormId($formId);

        return isset($metadata[$fieldId]) ? $metadata[$fieldId] : null;
    }

    /**
     * @param String $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->fieldValues[$key])) {
            return $this->fieldValues[$key];
        }

        return parent::__get($key);
    }

    /**
     * @return null|FormModel
     */
    public function getForm()
    {
        return FormRepository::getInstance()->getFormById($this->formId);
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return HashHelper::hash($this->id);
    }

    /**
     * @param string $handle
     *
     * @return string
     * @throws FreeformException
     */
    public function getFieldValue($handle)
    {
        if (!array_key_exists($handle, $this->fieldValues)) {
            throw new FreeformException(sprintf('Field "%s" not in found in form', $handle));
        }

        return $this->fieldValues[$handle];
    }

    /**
     * @param string $handle
     *
     * @return string
     * @throws FreeformException
     */
    public function getFieldValueAsString($handle)
    {
        if (!array_key_exists($handle, $this->fieldValues)) {
            throw new FreeformException(sprintf('Field "%s" not in found in form', $handle));
        }

        $value = $this->fieldValues[$handle];

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return $value;
    }

    /**
     * @param string $handle
     * @param mixed  $value
     *
     * @return $this
     */
    public function setFieldValue($handle, $value)
    {
        $this->fieldValues[$handle] = $value;

        return $this;
    }

    /**
     * Overriding the SAVE method
     */
    public function save()
    {
        $insertData = [
            'siteId'      => $this->siteId,
            'formId'      => $this->formId,
            'statusId'    => $this->statusId,
            'title'       => $this->title,
            'dateUpdated' => date('Y-m-d H:i:s', time()),
        ];

        $insertData = array_merge($insertData, $this->assembleInsertData());

        if ($this->id) {
            ee()->db
                ->where(['id' => $this->id])
                ->update(
                    self::TABLE,
                    $insertData
                );
        } else {
            ee()->db
                ->insert(
                    self::TABLE,
                    $insertData
                );

            $this->id = ee()->db->insert_id();
        }
    }

    /**
     * @param int   $fieldId
     * @param mixed $value
     *
     * @return $this
     */
    private function setFieldColumnValue($fieldId, $value)
    {
        $field = self::getFieldMetadataById($this->formId, $fieldId);

        if (!$field) {
            return $this;
        }

        if ($field->isArrayValue()) {
            $value = json_decode($value, true);
        }

        $this->fieldValues[$field->getHandle()] = $value;

        return $this;
    }

    /**
     * @return array
     */
    private function assembleInsertData()
    {
        if (!isset(self::$handleToFieldIdMap[$this->formId])) {
            self::getFieldMetadataByFormId($this->formId);
        }

        $insertData = [];
        foreach ($this->fieldValues as $key => $value) {
            $id         = self::$handleToFieldIdMap[$this->formId][$key];
            $columnName = self::getFieldColumnName($id);

            $field = self::getFieldMetadataById($this->formId, $id);

            if ($field->isArrayValue()) {
                $value = json_encode($value);
            }

            $insertData[$columnName] = $value;
        }

        return $insertData;
    }
}
