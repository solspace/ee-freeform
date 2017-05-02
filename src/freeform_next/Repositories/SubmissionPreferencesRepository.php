<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Model\SubmissionPreferencesModel;

class SubmissionPreferencesRepository extends Repository
{
    /**
     * @return SubmissionPreferencesRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param Form $form
     * @param int  $memberId
     *
     * @return SubmissionPreferencesModel
     */
    public function getOrCreate(Form $form, $memberId)
    {
        $model = ee('Model')
            ->get(SubmissionPreferencesModel::MODEL)
            ->filter('siteId', ee()->config->item('site_id'))
            ->filter('memberId', $memberId)
            ->filter('formId', $form->getId())
            ->first();

        if (!$model) {
            $model = SubmissionPreferencesModel::create($form, $memberId);
        }

        return $model;
    }
}
