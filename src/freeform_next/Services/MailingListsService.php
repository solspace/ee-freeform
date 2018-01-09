<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\ListNotFoundException;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Model\MailingListFieldModel;
use Solspace\Addons\FreeformNext\Model\MailingListModel;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use Solspace\Addons\FreeformNext\Utilities\Extension\FreeformIntegrationExtension;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MailingListsService implements MailingListHandlerInterface
{
    /** @var array */
    private static $integrations;

    /**
     * @param AbstractMailingListIntegration $integration
     * @param array                          $mailingLists
     *
     * @return bool
     */
    public function updateLists(AbstractMailingListIntegration $integration, array $mailingLists)
    {
        $resourceIds = [];
        foreach ($mailingLists as $mailingList) {
            $resourceIds[] = $mailingList->getId();
        }

        $id                  = $integration->getId();
        $existingResourceIds = ee()
            ->db
            ->select('resourceId')
            ->from(MailingListModel::TABLE)
            ->where('integrationId', $id)
            ->get()
            ->result_array();

        $existingResourceIds = array_column($existingResourceIds, 'resourceId');

        $removableResourceIds = array_diff($existingResourceIds, $resourceIds);
        $addableIds           = array_diff($resourceIds, $existingResourceIds);
        $updatableIds         = array_intersect($resourceIds, $existingResourceIds);

        foreach ($removableResourceIds as $resourceId) {
            // PERFORM DELETE
            ee()
                ->db
                ->delete(
                    MailingListModel::TABLE,
                    [
                        'integrationId' => $id,
                        'resourceId'    => $resourceId,
                    ]
                );
        }

        foreach ($mailingLists as $mailingList) {
            // PERFORM INSERT
            if (in_array($mailingList->getId(), $addableIds, false)) {
                $model                = MailingListModel::create();
                $model->integrationId = $id;
                $model->resourceId    = $mailingList->getId();
                $model->name          = $mailingList->getName();
                $model->memberCount   = $mailingList->getMemberCount();
                $model->save();
            }

            // PERFORM UPDATE
            if (in_array($mailingList->getId(), $updatableIds, false)) {
                ee()
                    ->db
                    ->where(
                        [
                            'integrationId' => $id,
                            'resourceId'    => $mailingList->getId(),
                        ]
                    )
                    ->update(
                        MailingListModel::TABLE,
                        [
                            'name'        => $mailingList->getName(),
                            'memberCount' => $mailingList->getMemberCount(),
                        ]
                    );
            }
        }

        $this->updateListFields($mailingLists);

        // Remove ForceUpdate flag
        ee()
            ->db
            ->where('id', $id)
            ->update(
                IntegrationModel::TABLE,
                ['forceUpdate' => 0]
            );

        return true;
    }

    /**
     * @param ListObject[] $mailingLists
     */
    private function updateListFields(array $mailingLists)
    {
        /** @var array $metadata */
        $metadata = ee()
            ->db
            ->select('id, resourceId')
            ->from(MailingListModel::TABLE)
            ->get()
            ->result_array();

        $mailingListIds = [];
        foreach ($metadata as $item) {
            $mailingListIds[$item['resourceId']] = $item['id'];
        }

        foreach ($mailingLists as $mailingList) {
            // Getting the database ID based on mailing list resource ID
            $mailingListId = $mailingListIds[$mailingList->getId()];

            $fields       = $mailingList->getFields();
            $fieldHandles = [];
            foreach ($fields as $field) {
                $fieldHandles[] = $field->getHandle();
            }

            $existingFieldHandles = ee()
                ->db
                ->select('handle')
                ->from(MailingListFieldModel::TABLE)
                ->where('mailingListId', $mailingListId)
                ->get()
                ->result_array();

            $existingFieldHandles = array_column($existingFieldHandles, 'handle');

            $removableFieldHandles = array_diff($existingFieldHandles, $fieldHandles);
            $addableFieldHandles   = array_diff($fieldHandles, $existingFieldHandles);
            $updatableFieldHandles = array_intersect($fieldHandles, $existingFieldHandles);

            foreach ($removableFieldHandles as $handle) {
                // PERFORM DELETE
                ee()
                    ->db
                    ->delete(
                        MailingListFieldModel::TABLE,
                        [
                            'mailingListId' => $mailingListId,
                            'handle'        => $handle,
                        ]
                    );
            }

            foreach ($fields as $field) {
                // PERFORM INSERT
                if (in_array($field->getHandle(), $addableFieldHandles, false)) {
                    $record                = MailingListFieldModel::create();
                    $record->mailingListId = $mailingListId;
                    $record->handle        = $field->getHandle();
                    $record->label         = $field->getLabel();
                    $record->type          = $field->getType();
                    $record->required      = $field->isRequired();
                    $record->save();
                }

                // PERFORM UPDATE
                if (in_array($field->getHandle(), $updatableFieldHandles, false)) {
                    ee()
                        ->db
                        ->where(
                            [
                                'mailingListId' => $mailingListId,
                                'handle'        => $field->getHandle(),
                            ]
                        )
                        ->update(
                            MailingListFieldModel::TABLE,
                            [
                                'handle'   => $field->getHandle(),
                                'label'    => $field->getLabel(),
                                'type'     => $field->getType(),
                                'required' => $field->isRequired() ? 1 : 0,
                            ]
                        );
                }
            }
        }
    }

    /**
     * @return IntegrationInterface[]
     */
    public function getAllIntegrations()
    {
        return MailingListRepository::getInstance()->getAllIntegrationObjects();
    }

    /**
     * @param int $id
     *
     * @return null|AbstractMailingListIntegration
     */
    public function getIntegrationById($id)
    {
        return MailingListRepository::getInstance()->getIntegrationObjectById($id);
    }

    /**
     * @param AbstractMailingListIntegration $integration
     *
     * @return ListObject[]
     */
    public function getLists(AbstractMailingListIntegration $integration)
    {
        /** @var array $data */
        $data = ee()
            ->db
            ->select('id, resourceId, name, memberCount')
            ->from(MailingListModel::TABLE)
            ->where('integrationId', $integration->getId())
            ->order_by('dateCreated ASC')
            ->get()
            ->result_array();

        $lists = [];
        foreach ($data as $item) {
            /** @var array $fieldData */
            $fieldData = ee()
                ->db
                ->select('handle, label, type, required')
                ->from(MailingListFieldModel::TABLE)
                ->where('mailingListId', $item['id'])
                ->order_by('dateCreated ASC')
                ->get()
                ->result_array();

            $fields = [];
            foreach ($fieldData as $fieldItem) {
                $fields[] = new FieldObject(
                    $fieldItem['handle'],
                    $fieldItem['label'],
                    $fieldItem['type'],
                    $fieldItem['required']
                );
            }

            $lists[] = new ListObject(
                $integration,
                $item['resourceId'],
                $item['name'],
                $fields,
                $item['memberCount']
            );
        }

        return $lists;
    }

    /**
     * @param AbstractMailingListIntegration $integration
     * @param int                            $id
     *
     * @return ListObject
     * @throws ListNotFoundException
     */
    public function getListById(AbstractMailingListIntegration $integration, $id)
    {
        /** @var MailingListModel $mailingList */
        $mailingList = ee('Model')
            ->get(MailingListModel::MODEL)
            ->filter('resourceId', $id)
            ->filter('integrationId', $integration->getId())
            ->first();

        if ($mailingList) {
            $listObject = new ListObject(
                $integration,
                $mailingList->resourceId,
                $mailingList->name,
                $mailingList->getFieldObjects(),
                $mailingList->memberCount
            );

            return $listObject;
        }

        throw new ListNotFoundException(
            sprintf(
                'Could not find a list by ID "%s" in %s',
                $id,
                $integration->getServiceProvider()
            )
        );
    }

    /**
     * Flag the given mailing list integration so that it's updated the next time it's accessed
     *
     * @param AbstractMailingListIntegration $integration
     */
    public function flagMailingListIntegrationForUpdating(AbstractMailingListIntegration $integration)
    {
        ee()
            ->db
            ->where(['id' => $integration->getId()])
            ->update(
                IntegrationModel::TABLE,
                ['forceUpdate' => 1]
            );
    }

    /**
     * @return array
     */
    public function getAllMailingListServiceProviders()
    {
        if (null === self::$integrations) {
            $interface = 'Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListIntegrationInterface';
            $integrations = $validIntegrations = [];

            $addonIntegrations = [];
            if (ee()->extensions->active_hook(FreeformIntegrationExtension::HOOK_REGISTER_INTEGRATIONS) === true) {
                $addonIntegrations = ee()->extensions->call(FreeformIntegrationExtension::HOOK_REGISTER_INTEGRATIONS);
            }

            $integrations = array_merge($integrations, $addonIntegrations);

            $finder          = new Finder();
            $mailingListPath = __DIR__ . '/../Integrations/MailingLists';
            if (file_exists($mailingListPath) && is_dir($mailingListPath)) {
                /** @var SplFileInfo[] $files */
                $files         = $finder->files()->in($mailingListPath)->name('*.php');
                $baseNamespace = 'Solspace\Addons\FreeformNext\Integrations\MailingLists\\';

                foreach ($files as $file) {
                    $fileName = $file->getFilename();
                    $baseName = substr(
                        $fileName,
                        0,
                        strpos($fileName, '.')
                    );

                    $className = $baseNamespace . $baseName;

                    $integrations[$className] = $baseName;
                }
            }


            foreach ($integrations as $class => $name) {
                $reflectionClass = new \ReflectionClass($class);

                if ($reflectionClass->implementsInterface($interface)) {
                    $validIntegrations[$class] = $reflectionClass->getConstant('TITLE');
                }
            }

            self::$integrations = $validIntegrations;
        }

        return self::$integrations;
    }

    /**
     * @return array
     */
    public function getAllMailingListSettingBlueprints()
    {
        $serviceProviderTypes = $this->getAllMailingListServiceProviders();

        // Get all blueprints per class
        $settingBlueprints = [];

        /**
         * @var AbstractIntegration $providerClass
         * @var string              $name
         */
        foreach ($serviceProviderTypes as $providerClass => $name) {
            $settingBlueprints[$providerClass] = $providerClass::getSettingBlueprints();
        }

        return $settingBlueprints;
    }

    /**
     * Get all setting blueprints for a specific mailing list integration
     *
     * @param string $class
     *
     * @return SettingBlueprint[]
     * @throws IntegrationException
     */
    public function getMailingListSettingBlueprints($class)
    {
        $serviceProviderTypes = $this->getAllMailingListServiceProviders();

        /**
         * @var AbstractIntegration $providerClass
         * @var string              $name
         */
        foreach ($serviceProviderTypes as $providerClass => $name) {
            if ($providerClass === $class) {
                return $providerClass::getSettingBlueprints();
            }
        }

        throw new IntegrationException('Could not get Mailing List settings');
    }
}
