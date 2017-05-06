<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\StatusModel;
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
            ->select('s.*, stat.name AS statusName, stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS s')
            ->join(StatusModel::TABLE . ' AS stat', 's.statusId = stat.id')
            ->where(
                [
                    's.id'     => $submissionId,
                    'formId' => $form->getId(),
                ]
            )
            ->get()
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

        $result = ee()->db
            ->select('s.*, stat.name AS statusName, stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS s')
            ->join(StatusModel::TABLE . ' AS stat', 's.statusId = stat.id')
            ->where_in('s.id', $ids)
            ->get()
            ->result_array();

        $submissions = [];
        foreach ($result as $row) {
            static $form;

            if (null === $form) {
                $form = FormRepository::getInstance()->getFormById($row['formId'])->getForm();
            }

            $model = SubmissionModel::createFromDatabase($form, $row);

            $submissions[] = $model;
        }

        return $submissions;
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
            ->select('s.*, stat.name AS statusName, stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS s')
            ->join(StatusModel::TABLE . ' AS stat', 's.statusId = stat.id')
            ->get()
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
