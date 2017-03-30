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

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\FieldModel;

class FieldRepository extends Repository
{
    /**
     * @return FieldRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /** @var FieldModel[] */
    private static $fieldCache;

    /** @var bool */
    private static $allFieldsLoaded;

    /**
     * @param int|null $fieldId
     *
     * @return FieldModel
     */
    public function getOrCreateField($fieldId = null)
    {
        $field = null;
        if ($fieldId) {
            $field = $this->getFieldById($fieldId);
        }

        if (!$field) {
            $field = FieldModel::create();
        }

        return $field;
    }

    /**
     * @param int $fieldId
     *
     * @return FieldModel|null
     */
    public function getFieldById($fieldId)
    {
        return ee('Model')
            ->get(FieldModel::MODEL)
            ->filter('id', $fieldId)
            ->first();
    }

    /**
     * @param bool $indexById
     *
     * @return FieldModel[]
     */
    public function getAllFields($indexById = true)
    {
        if (null === self::$fieldCache || !self::$allFieldsLoaded) {
            //$fieldDisplayOrder = craft()->freeform_settings->getFieldDisplayOrder();

            $fieldModels = ee('Model')
                ->get(FieldModel::MODEL)
                ->all()
                ->asArray();

            self::$fieldCache = $fieldModels;

            self::$allFieldsLoaded = true;
        }

        if (!$indexById) {
            return array_values(self::$fieldCache);
        }

        return self::$fieldCache;
    }
}
