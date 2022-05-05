<?php

namespace Solspace\Addons\FreeformNext\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\FreeformNext\Library\Configuration\EEPluginConfiguration;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\AbstractIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\CRM\AbstractCRMIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\IntegrationStorageInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Library\Logging\EELogger;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;

/**
 * @property int       $id
 * @property int       $siteId
 * @property string    $name
 * @property string    $handle
 * @property string    $type
 * @property string    $class
 * @property string    $accessToken
 * @property array     $settings
 * @property bool      $forceUpdate
 * @property \DateTime $lastUpdate
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 */
class IntegrationModel extends Model implements IntegrationStorageInterface
{
    use TimestampableTrait;

    const MODEL = 'freeform_next:IntegrationModel';
    const TABLE = 'freeform_next_integrations';

    const TYPE_MAILING_LIST = 'mailing_list';
    const TYPE_CRM          = 'crm';

    protected static $_primary_key = 'id';
    protected static $_table_name  = self::TABLE;

    protected $id;
    protected $siteId;
    protected $name;
    protected $handle;
    protected $type;
    protected $class;
    protected $accessToken;
    protected $settings;
    protected $forceUpdate;
    protected $lastUpdate;

    /**
     * @param string $type
     *
     * @return IntegrationModel
     * @throws IntegrationException
     */
    public static function create($type)
    {
        if (!in_array($type, [self::TYPE_MAILING_LIST, self::TYPE_CRM], true)) {
            throw new IntegrationException('Invalid integration type');
        }

        return ee('Model')
            ->make(
                self::MODEL,
                [
                    'siteId' => ee()->config->item('site_id'),
                    'type'   => $type,
                ]
            );
    }

    /**
     * Update the access token
     *
     * @param string $accessToken
     */
    public function updateAccessToken($accessToken)
    {
        $this->set(['accessToken' => $accessToken]);
    }

    /**
     * Update the settings that are to be stored
     *
     * @param array $settings
     */
    public function updateSettings(array $settings = [])
    {
        $this->set(['settings' => json_encode($settings)]);
    }

    /**
     * @return AbstractIntegration|AbstractCRMIntegration|AbstractMailingListIntegration
     * @throws IntegrationException
     */
    public function getIntegrationObject()
    {
        switch ($this->type) {
            case self::TYPE_MAILING_LIST:
                $handler = new MailingListsService();
                break;

            case self::TYPE_CRM:
                $handler = new CrmService();
                break;

            default:
                throw new IntegrationException(lang('Unknown integration type specified'));
        }

        $className = $this->class;

        /** @var AbstractIntegration $integration */
        $integration = new $className(
            $this->id,
            $this->name,
            new \DateTime($this->lastUpdate ?: ''),
            $this->accessToken,
            json_decode($this->settings ?: '', true),
            new EELogger(),
            new EEPluginConfiguration(),
            new EETranslator(),
            $handler
        );

        $integration->setForceUpdate($this->forceUpdate);

        return $integration;
    }
}
