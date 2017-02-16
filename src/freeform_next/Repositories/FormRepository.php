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

class FormRepository extends Repository
{
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
        return ee('Model')
            ->get(FormModel::MODEL)
            ->filter('id', $id)
            ->first();
    }
}