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
     * @return FormModel[]
     */
    public function getAllForms()
    {
        return ee('Model')
            ->get(FormModel::MODEL)
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
     * @return mixed
     */
    public function getFormSubmissionCount(array $formIds)
    {
        $data = ee()->db
            ->select('formId, COUNT(id) as total')
            ->group_by('formId')
            ->get(SubmissionModel::TABLE)
            ->result_array();

        return array_column($data, 'total', 'formId');
    }
}
