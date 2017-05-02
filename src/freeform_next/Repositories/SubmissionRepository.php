<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionRepository extends Repository
{
    /**
     * @return SubmissionRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param Form $form
     * @param int  $submissionId
     *
     * @return SubmissionModel
     */
    public function getSubmission(Form $form, $submissionId)
    {
        /** @var array $result */
        $result = ee()->db
            ->where(
                [
                    'id'     => $submissionId,
                    'formId' => $form->getId(),
                ]
            )
            ->get(SubmissionModel::TABLE)
            ->result_array();

        if (count($result) > 0) {
            return SubmissionModel::createFromDatabase($form, $result[0]);
        }

        return null;
    }

    /**
     * @param array $ids
     *
     * @return SubmissionModel[]
     */
    public function getSubmissionsByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(SubmissionModel::MODEL)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }

    /**
     * @param Form   $form
     * @param array  $filters
     * @param null   $orderBy
     * @param string $sort
     * @param null   $limit
     * @param null   $offset
     *
     * @return SubmissionModel[]
     */
    public function getAllSubmissionsFor(
        Form $form,
        array $filters = [],
        $orderBy = null,
        $sort = 'asc',
        $limit = null,
        $offset = null
    ) {
        $filters['formId'] = $form->getId();

        /** @var array $result */
        ee()->db->where($filters);

        if (null !== $orderBy) {
            ee()->db->order_by($orderBy, strtolower($sort) === 'asc' ? 'ASC' : 'DESC');
        }

        if (null !== $limit) {
            ee()->db->limit($limit);
        }

        if (null !== $offset) {
            ee()->db->offset($offset);
        }

        $result = ee()->db
            ->get(SubmissionModel::TABLE)
            ->result_array();

        $submissions = [];
        foreach ($result as $row) {
            $model = SubmissionModel::createFromDatabase($form, $row);

            $submissions[] = $model;
        }

        return $submissions;
    }

    /**
     * @param Form  $form
     * @param array $filters
     *
     * @return int
     */
    public function getAllSubmissionCountFor(Form $form, array $filters = [])
    {
        $filters['formId'] = $form->getId();

        return ee()->db
            ->select('COUNT(id) AS total')
            ->where($filters)
            ->get(SubmissionModel::TABLE)
            ->row('total') ?: 0;
    }

    /**
     * @return array
     */
    public function getSubmissionTotalsPerForm()
    {
        $result = ee()->db
            ->select('COUNT(id) as total, formId')
            ->group_by('formId')
            ->from(SubmissionModel::TABLE)
            ->get()
            ->result_array();

        $totals = [];
        foreach ($result as $row) {
            $totals[$row['formId']] = (int) $row['total'];
        }

        return $totals;
    }
}
