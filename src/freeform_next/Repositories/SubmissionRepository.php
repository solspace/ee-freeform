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
            ->select('s.*, stat.name AS statusName, stat.handle AS statusHandle, stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS s')
            ->join(StatusModel::TABLE . ' AS stat', 's.statusId = stat.id')
            ->where(
                [
                    's.id'   => $submissionId,
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
            ->select('s.*, stat.name AS statusName, stat.handle AS statusHandle, stat.color AS statusColor')
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
     * @throws \Exception
     */
    public function getAllSubmissionsFor(SubmissionAttributes $attributes)
    {
        $submissionTable = SubmissionModel::TABLE;
        $statusTable     = StatusModel::TABLE;

        foreach ($attributes->getLikeFilters() as $key => $value) {
            ee()->db->like($key, $value);
        }

        foreach ($attributes->getOrLikeFilters() as $key => $value) {
            if ($key == 'id') {
                ee()->db->or_like($submissionTable . '.id', $value);
            } else {
                ee()->db->or_like($key, $value);
            }
        }

        foreach ($attributes->getFilters() as $key => $value) {
            ee()->db->where($key, $value);
        }

        foreach ($attributes->getInFilters() as $key => $value) {
            ee()->db->where_in($key, $value);
        }

        foreach ($attributes->getNotInFilters() as $key => $value) {
            ee()->db->where_not_in($key, $value);
        }

        foreach ($attributes->getIdFilters() as $key => $value) {
            ee()->db->where($submissionTable . '.id', $value);
        }

        ee()->db->order_by($attributes->getOrderBy(), $attributes->getSort());

        if ($attributes->getLimit()) {
            ee()->db->limit($attributes->getLimit());
        }

        if (null !== $attributes->getOffset()) {
            ee()->db->offset($attributes->getOffset());
        }

        try {


            $query = ee()->db
                ->select("$submissionTable.*, $submissionTable.id AS submissionId, $statusTable.name AS statusName, $statusTable.handle AS statusHandle, $statusTable.color AS statusColor")
                ->from($submissionTable)
                ->join($statusTable, "$submissionTable.statusId = $statusTable.id")
                ->get();

            $result = $query->result_array();

        } catch (\Exception $e) {
            if (preg_match("/Column not found: 1054.*in 'order clause'/", $e->getMessage())) {
                throw new \Exception(sprintf('Cannot order by %s', $attributes->getOrderBy()));
            }

            return [];
        }

        $submissions = [];
        foreach ($result as $row) {
            $model = SubmissionModel::createFromDatabase($attributes->getForm(), $row);

            $submissions[] = $model;
        }

        return $submissions;
    }

    /**
     * @param SubmissionAttributes $attributes
     *
     * @return int
     */
    public function getAllSubmissionCountFor(SubmissionAttributes $attributes)
    {
        foreach ($attributes->getFilters() as $key => $value) {
            ee()->db->where($key, $value);
        }

        foreach ($attributes->getInFilters() as $key => $value) {
            ee()->db->where_in($key, $value);
        }

        foreach ($attributes->getNotInFilters() as $key => $value) {
            ee()->db->where_not_in($key, $value);
        }

        $prefix = ee()->db->dbprefix;
        $submissionTable = SubmissionModel::TABLE;
        $statusTable = StatusModel::TABLE;

        return ee()->db
            ->select("COUNT({$prefix}{$submissionTable}.id) AS total")
            ->from($submissionTable)
            ->join($statusTable, "$submissionTable.statusId = $statusTable.id")
            ->get()
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

    private function groupLike($values) {

        $where = '';

        if (is_array($values))
        {
            $first	= TRUE;
            $and	= "\nAND ";
            $or		= "\nOR ";
            $where	.= '(';

            //a LIKE or LIKE for every shown column
            foreach ($values as $name => $value)
            {
                $joiner = $or;
                $start	= $or;

                if ($first)
                {
                    $first	= FALSE;
                    $start	= '';
                    $joiner	= $and;
                }

                $where .= $start . implode(
                        $joiner,
                        call_user_func_array(
                            array($this, '_like'),
                            array($name, $value)
                        )
                    );
            }

            $where .= ')';
        }

        if ($return_sql)
        {
            return $where;
        }
        else
        {
            if ($where)
            {
                $this->where($where);
            }

            return $this;
        }
    }
}
