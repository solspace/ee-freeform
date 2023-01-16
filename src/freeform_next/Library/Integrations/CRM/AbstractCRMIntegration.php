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

namespace Solspace\Addons\FreeformNext\Library\Integrations\CRM;

use Solspace\Addons\FreeformNext\Library\Configuration\ConfigurationInterface;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationInterface;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

abstract class AbstractCRMIntegration extends AbstractIntegration implements CRMIntegrationInterface, IntegrationInterface, \JsonSerializable
{
    /** @var CRMHandlerInterface */
    private $crmHandler;

    /**
     * AbstractMailingList constructor.
     *
     * @param int                    $id
     * @param string                 $name
     * @param \DateTime              $lastUpdate
     * @param string                 $accessToken
     * @param array|null             $settings
     * @param LoggerInterface        $logger
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface    $translator
     * @param CRMHandlerInterface    $crmHandler
     */
    public final function __construct(
        $id,
        $name,
        \DateTime $lastUpdate,
        $accessToken,
        $settings,
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        TranslatorInterface $translator,
        CRMHandlerInterface $crmHandler
    ) {
        parent::__construct(
            $id,
            $name,
            $lastUpdate,
            $accessToken,
            $settings,
            $logger,
            $configuration,
            $translator,
            $crmHandler
        );

        $this->crmHandler = $crmHandler;
    }

    /**
     * @return FieldObject[]
     */
    public final function getFields()
    {
        if ($this->isForceUpdate()) {
            $fields = $this->fetchFields();
            $this->crmHandler->updateFields($this, $fields);
        } else {
            $fields = $this->crmHandler->getFields($this);
        }

        return $fields;
    }

    /**
     * Fetch the custom fields from the integration
     *
     * @return FieldObject[]
     */
    abstract public function fetchFields();

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
	{
        try {
            $fields = $this->getFields();
        } catch (\Exception $e) {
            $this->getLogger()->log(LoggerInterface::LEVEL_ERROR, $e->getMessage());

            $fields = [];
        }

        return [
            "id"     => (int)$this->getId(),
            "name"   => $this->getName(),
            "fields" => $fields,
        ];
    }
}
