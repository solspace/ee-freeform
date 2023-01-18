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
     * @param Form   $form
     * @param string $token
     *
     * @return SubmissionModel|null
     */
    public function getSubmissionByToken(Form $form, $token)
    {
        if (!$token) {
            return null;
        }

        /** @var array $result */
        $result = ee()->db
            ->select('s.*, stat.name AS statusName, stat.handle AS statusHandle, stat.color AS statusColor')
            ->from(SubmissionModel::TABLE . ' AS s')
            ->join(StatusModel::TABLE . ' AS stat', 's.statusId = stat.id')
            ->where(
                [
                    's.token' => $token,
                    'formId'  => $form->getId(),
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

        $orLikeGroup = [];
        foreach ($attributes->getOrLikeFilters() as $key => $value) {
            $orLikeGroup[] = ee()->db->dbprefix($submissionTable) . ".{$key} LIKE '%" . ee()->security->xss_clean($value) . "%'";
        }

        if(! empty($orLikeGroup))
        {
            $orLikeGroupString = '(' . implode(' OR ', $orLikeGroup) . ')';
            ee()->db->where($orLikeGroupString);
        }

        foreach ($attributes->getFilters() as $key => $value) {
            ee()->db->where($key, $value);
        }

        foreach ($attributes->getOrFilters() as $key => $value) {
            ee()->db->or_where($key, $value);
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
                ->join($statusTable, "$submissionTable.statusId = $statusTable.id");

            $sql = $query->_compile_select();

            foreach ($attributes->getWhere() as $value) {
                $sql = $this->addWhereToSql($sql, $value);
            }

            $result = $query->query($sql)->result_array();
            $query->_reset_select();
            ee()->db->_reset_select();

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
        $submissionTable = SubmissionModel::TABLE;

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

        if (null !== $attributes->getOffset()) {
            ee()->db->offset($attributes->getOffset());
        }

        $prefix          = ee()->db->dbprefix;
        $submissionTable = SubmissionModel::TABLE;
        $statusTable     = StatusModel::TABLE;

        $query = ee()->db
            ->select("COUNT({$prefix}{$submissionTable}.id) AS total")
            ->from($submissionTable)
            ->join($statusTable, "$submissionTable.statusId = $statusTable.id");

        $sql = $query->_compile_select();

        foreach ($attributes->getWhere() as $value) {
            $sql = $this->addWhereToSql($sql, $value, false);
        }

        $result = $query->query($sql)->row('total') ?: 0;
        $query->_reset_select();
        ee()->db->_reset_select();

        return $result;
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

    private function groupLike($values)
    {

        $where = '';

        if (is_array($values)) {
            $first = true;
            $and   = "\nAND ";
            $or    = "\nOR ";
            $where .= '(';

            //a LIKE or LIKE for every shown column
            foreach ($values as $name => $value) {
                $joiner = $or;
                $start  = $or;

                if ($first) {
                    $first  = false;
                    $start  = '';
                    $joiner = $and;
                }

                $where .= $start . implode(
                        $joiner,
                        call_user_func_array(
                            [$this, '_like'],
                            [$name, $value]
                        )
                    );
            }

            $where .= ')';
        }

        if ($return_sql) {
            return $where;
        } else {
            if ($where) {
                $this->where($where);
            }

            return $this;
        }
    }

    /**
     * @param string $sql
     * @param string $where
     *
     * @return string
     */
    private function addWhereToSql($sql, $where, $hasOrderBy = true)
    {
        $pattern = '/WHERE (.*?)\s(?=(ORDER BY|LIMIT))/s';

        preg_match($pattern, $sql, $matches);

        if ($matches) {
            list ($_, $existingRules) = $matches;

            $sql = str_replace(
                'WHERE ' . $existingRules,
                'WHERE ' . $existingRules . ' AND ' . $where,
                $sql
            );
        } else {

            $pattern = '/WHERE (.*)$/s';
            preg_match($pattern, $sql, $matches);

            if ($matches) {
                list ($_, $existingRules) = $matches;

                $sql = str_replace(
                    'WHERE ' . $existingRules,
                    'WHERE ' . $existingRules . ' AND ' . $where,
                    $sql
                );
            } else {
                $sql = $sql . ' WHERE' . $where;
            }
        }

        return $sql;
    }
}
