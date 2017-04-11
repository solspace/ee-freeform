<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;

class MailingListRepository extends Repository
{
    /**
     * @return MailingListRepository
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
            ->filter('type', IntegrationModel::TYPE_MAILING_LIST)
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
     * @return null|AbstractMailingListIntegration
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
            ->filter('type', IntegrationModel::TYPE_MAILING_LIST)
            ->filter('id', 'IN', $ids)
            ->all()
            ->asArray();
    }
}
