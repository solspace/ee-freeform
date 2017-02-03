<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\FormModel;

class FormRepository extends Repository
{
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
            return FormModel::create(ee()->config->item('site_id'));
        }

        return $model;
    }

    /**
     * @param int $id
     *
     * @return FormModel|null
     */
    public function getFormById($id)
    {
        return ee('Model')
            ->get('freeform_next:FormModel')
            ->filter('id', $id)
            ->first();
    }
}