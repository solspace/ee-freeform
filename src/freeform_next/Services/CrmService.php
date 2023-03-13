<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Services;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Layout;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\IntegrationProperties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\CRMIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;
use Solspace\Addons\FreeformNext\Library\Logging\EELogger;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\CrmFieldModel;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\CrmRepository;
use Solspace\Addons\FreeformNext\Utilities\Extension\FreeformIntegrationExtension;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CrmService implements CRMHandlerInterface
{
    /** @var array */
    private static $integrations;

    /**
     * @return IntegrationInterface[]
     */
    public function getAllIntegrations()
    {
        return CrmRepository::getInstance()->getAllIntegrationObjects();
    }

    /**
     * @param int $id
     *
     * @return AbstractCRMIntegration
     * @throws CRMIntegrationNotFoundException
     */
    public function getIntegrationById($id)
    {
        $model = CrmRepository::getInstance()->getIntegrationById($id);

        if ($model && $model->type === IntegrationModel::TYPE_CRM) {
            return $model->getIntegrationObject();
        }

        $translator = new EETranslator();

        throw new CRMIntegrationNotFoundException(
            $translator->translate('CRM Integration with ID {id} not found', ['id' => $id])
        );
    }

    /**
     * @param AbstractCRMIntegration $integration
     * @param array                  $fields
     *
     * @return bool
     */
    public function updateFields(AbstractCRMIntegration $integration, array $fields)
    {
        $handles = [];
        foreach ($fields as $field) {
            $handles[] = $field->getHandle();
        }

        $id     = $integration->getId();
        $result = ee()
            ->db
            ->select('handle')
            ->from(CrmFieldModel::TABLE)
            ->where('integrationId', $id)
            ->get()
            ->result_array();

        $existingFields = [];
        foreach ($result as $item) {
            $existingFields[] = $item['handle'];
        }

        $removableHandles = array_diff($existingFields, $handles);
        $addableHandles   = array_diff($handles, $existingFields);
        $updatableHandles = array_intersect($handles, $existingFields);

        foreach ($removableHandles as $handle) {
            // PERFORM DELETE
            ee()
                ->db
                ->delete(
                    CrmFieldModel::TABLE,
                    [
                        'integrationId' => $id,
                        'handle'        => $handle,
                    ]
                );
        }

        foreach ($fields as $field) {
            // PERFORM INSERT
            if (in_array($field->getHandle(), $addableHandles, true)) {
                $record                = CrmFieldModel::create();
                $record->integrationId = $id;
                $record->handle        = $field->getHandle();
                $record->label         = $field->getLabel();
                $record->required      = $field->isRequired();
                $record->save();
            }

            // PERFORM UPDATE
            if (in_array($field->getHandle(), $updatableHandles, true)) {
                ee()
                    ->db
                    ->update(
                        CrmFieldModel::TABLE,
                        [
                            'label'    => $field->getLabel(),
                            'type'     => $field->getType(),
                            'required' => $field->isRequired() ? 1 : 0,
                        ],
                        [
                            'integrationId' => $id,
                            'handle'        => $field->getHandle(),
                        ]
                    );
            }
        }

        // Remove ForceUpdate flag
        ee()
            ->db
            ->update(
                IntegrationModel::TABLE,
                ['forceUpdate' => 0],
                ['id' => $id]
            );

        return true;
    }

    /**
     * Update the access token of an integration
     *
     * @param AbstractCRMIntegration $integration
     */
    public function updateAccessToken(AbstractCRMIntegration $integration)
    {
        $model              = CrmRepository::getInstance()->getIntegrationById($integration->getId());
        $model->accessToken = $integration->getAccessToken();
        $model->updateSettings($integration->getSettings());
        $model->save();
    }

    /**
     * @param AbstractCRMIntegration $integration
     *
     * @return FieldObject[]
     */
    public function getFields(AbstractCRMIntegration $integration)
    {
        /** @var array $fieldData */
        $fieldData = ee()
            ->db
            ->select('handle, label, type, required')
            ->from(CrmFieldModel::TABLE)
            ->where('integrationId', $integration->getId())
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

        return $fields;
    }

    /**
     * @param AbstractCRMIntegration $integration
     */
    public function flagIntegrationForUpdating(AbstractCRMIntegration $integration)
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
     * @param IntegrationProperties $properties
     * @param Layout                $layout
     *
     * @return bool
     */
    public function pushObject(IntegrationProperties $properties, Layout $layout)
    {
        try {
            $integration = $this->getIntegrationById($properties->getIntegrationId());
        } catch (\Exception $e) {
            return false;
        }

        $logger     = new EELogger();
        $translator = new EETranslator();

        $mapping = $properties->getMapping();
        if (empty($mapping)) {
            $logger->log(
                LoggerInterface::LEVEL_ERROR,
                $translator->translate(
                    "No field mapping specified for '{integration}' integration",
                    ['integration' => $integration->getName()]
                )
            );

            return false;
        }

        /** @var FieldObject[] $crmFieldsByHandle */
        $crmFieldsByHandle = [];
        foreach ($integration->getFields() as $field) {
            $crmFieldsByHandle[$field->getHandle()] = $field;
        }

        $objectValues = [];
        $formFields = [];
        foreach ($mapping as $crmHandle => $fieldHandle) {
            try {
                $crmField  = $crmFieldsByHandle[$crmHandle];
                $formField = $layout->getFieldByHandle($fieldHandle);

                $formFields[$crmHandle] = $formField;

                if ($crmField->getType() === FieldObject::TYPE_ARRAY) {
                    $value = $formField->getValue();
                } else {
                    $value = $formField->getValueAsString(false);
                }

                $objectValues[$crmHandle] = $integration->convertCustomFieldValue($crmField, $value);
            } catch (FreeformException $e) {
                $logger->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());
            }
        }

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_CRM_BEFORE_PUSH, $integration, $objectValues)) {
            return false;
        }

        if (!empty($objectValues)) {
            try {
                $result = $integration->pushObject($objectValues, $formFields);

                ExtensionHelper::call(ExtensionHelper::HOOK_CRM_AFTER_PUSH, $integration, $objectValues);

                return $result;
            } catch (BadResponseException $e) {
                if ($integration instanceof TokenRefreshInterface) {
                    if ($integration->refreshToken() && $integration->isAccessTokenUpdated()) {
                        try {
                            $this->updateAccessToken($integration);

                            try {
                                $result = $integration->pushObject($objectValues, $formFields);

                                ExtensionHelper::call(ExtensionHelper::HOOK_CRM_AFTER_PUSH, $integration, $objectValues);

                                return $result;
                            } catch (\Exception $e) {
                                $logger->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());
                            }
                        } catch (\Exception $e) {
                            $logger->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());
                        }
                    }
                }

                $logger->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());
            } catch (\Exception $e) {
                $logger->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllCrmServiceProviders()
    {
        if (null === self::$integrations) {
            $interface    = 'Solspace\Addons\FreeformNext\Library\Integrations\CRM\CRMIntegrationInterface';
            $integrations = $validIntegrations = [];

            $addonIntegrations = [];
            if (ee()->extensions->active_hook(FreeformIntegrationExtension::HOOK_REGISTER_INTEGRATIONS) === true) {
                $addonIntegrations = ee()->extensions->call(FreeformIntegrationExtension::HOOK_REGISTER_INTEGRATIONS);
            }

            $integrations = array_merge($integrations, $addonIntegrations);

            $finder      = new Finder();
            $crmListPath = __DIR__ . '/../Integrations/CRM';
            if (file_exists($crmListPath) && is_dir($crmListPath)) {
                $files         = $finder->depth(0)->files()->in($crmListPath)->name('*.php');
                $baseNamespace = 'Solspace\Addons\FreeformNext\Integrations\CRM\\';

                /** @var SplFileInfo $file */
                foreach ($files as $file) {
                    $fileName = str_replace('/', '\\', $file->getRelativePathname());
                    $baseName = substr(
                        $fileName,
                        0,
                        strpos($fileName, '.')
                    );

                    $className = $baseNamespace . $baseName;

                    $integrations[$className] = $baseName;
                }
            }


            $validIntegrations = [];
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
    public function getAllCrmSettingBlueprints()
    {
        $serviceProviderTypes = $this->getAllCrmServiceProviders();

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
     * Get all setting blueprints for a specific crm integration
     *
     * @param string $class
     *
     * @return SettingBlueprint[]
     * @throws IntegrationException
     */
    public function getCrmSettingBlueprints($class)
    {
        $serviceProviderTypes = $this->getAllCrmServiceProviders();

        /**
         * @var AbstractIntegration $providerClass
         * @var string              $name
         */
        foreach ($serviceProviderTypes as $providerClass => $name) {
            if ($providerClass === $class) {
                return $providerClass::getSettingBlueprints();
            }
        }

        throw new IntegrationException('Could not get Crm settings');
    }

    public function onAfterResponse(AbstractIntegration $integration, ResponseInterface $response)
    {
        //
    }
}
