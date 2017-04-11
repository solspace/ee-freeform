<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationInterface;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;

class CrmRepository extends Repository
{
    /**
     * @return CrmRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return IntegrationModel[]
     */
    public function getAllIntegrations()
    {
        return ee('Model')
            ->get(IntegrationModel::MODEL)
            ->filter('type', IntegrationModel::TYPE_CRM)
            ->all()
            ->asArray();
    }

    /**
     * @return IntegrationInterface[]
     */
    public function getAllIntegrationObjects()
    {
        $models = $this->getAllIntegrations();

        $objects = [];
        foreach ($models as $model) {
            $objects[] = $model->getIntegrationObject();
        }

        return $objects;
    }

    /**
     * @param int $id
     *
     * @return IntegrationModel|null
     */
    public function getIntegrationById($id)
    {
        return ee('Model')
            ->get(IntegrationModel::MODEL)
            ->filter('id', $id)
            ->first();
    }

    /**
     * @param $id
     *
     * @return null|AbstractCRMIntegration
     */
    public function getIntegrationObjectById($id)
    {
        $model = $this->getIntegrationById($id);

        if ($model) {
            return $model->getIntegrationObject();
        }

        return null;
    }

    /**
     * @param array $ids
     *
     * @return IntegrationModel[]
     */
    public function getIntegrationsByIdList(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return ee('Model')
            ->get(IntegrationModel::MODEL)
            ->filter('type', IntegrationModel::TYPE_CRM)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }
}
