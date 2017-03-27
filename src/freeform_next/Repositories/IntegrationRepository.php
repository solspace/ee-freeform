<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Model\IntegrationModel;

class IntegrationRepository extends Repository
{
    /**
     * @return IntegrationRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param $id
     *
     * @return null|
     */
    public function getIntegrationById($id)
    {
        /** @var IntegrationModel $model */
        $model = ee('Model')
            ->get(IntegrationModel::MODEL)
            ->filter('id', $id)
            ->first();

        if ($model) {
            return $model->getIntegrationObject();
        }

        return null;
    }
}
