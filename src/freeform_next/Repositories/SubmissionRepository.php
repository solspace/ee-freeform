<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
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
     * @param SubmissionAttributes $attributes
     *
     * @return SubmissionModel[]
     */
    public function getAllSubmissionsFor(SubmissionAttributes $attributes) {
        /** @var array $result */
        ee()->db->where($attributes->getFilters());

        foreach ($attributes->getInFilters() as $key => $value) {
            ee()->db->where_in($key, $value);
        }

        foreach ($attributes->getNotInFilters() as $key => $value) {
            ee()->db->where_not_in($key, $value);
        }

        ee()->db->order_by($attributes->getOrderBy(), $attributes->getSort());

        if ($attributes->getLimit()) {
            ee()->db->limit($attributes->getLimit());
        }

        if (null !== $attributes->getOffset()) {
            ee()->db->offset($attributes->getOffset());
        }

        $result = ee()->db
            ->select('exp_s.*, exp_stat.name AS statusName, exp_stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS exp_s')
            ->join(StatusModel::TABLE . ' AS exp_stat', 'exp_s.statusId = stat.id')
            ->get()
            ->result_array();

        $submissions = [];
        foreach ($result as $row) {
            $model = SubmissionModel::createFromDatabase($attributes->getForm(), $row);

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
