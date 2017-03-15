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
     * @param Form $form
     *
     * @return SubmissionModel[]
     */
    public function getAllSubmissionsFor(Form $form)
    {
        /** @var array $result */
        $result = ee()->db
            ->where(['formId' => $form->getId()])
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
