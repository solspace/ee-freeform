<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations;

use DateTime;
use ReflectionClass;
use Solspace\Addons\FreeformNext\Library\Configuration\ConfigurationInterface;
use Solspace\Addons\FreeformNext\Library\Database\IntegrationHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

abstract class AbstractIntegration implements IntegrationInterface
{
    private ?bool $accessTokenUpdated = null;

    private ?bool $forceUpdate = null;

    /**
     * Returns a list of additional settings for this integration
     * Could be used for anything, like - AccessTokens
     *
     * @return SettingBlueprint[]
     */
    public static function getSettingBlueprints()
    {
        return [];
    }

    /**
     * @param int                    $id
     * @param string                 $name
     * @param string                 $accessToken
     * @param array|null             $settings
     */
    public function __construct(private $id, private $name, private DateTime $lastUpdate, private $accessToken, private $settings, private LoggerInterface $logger, private ConfigurationInterface $configuration, private TranslatorInterface $translator, private IntegrationHandlerInterface $handler)
    {
    }

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    abstract public function checkConnection();

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Setting this to true will force re-fetching of all lists
     *
     * @param bool $value
     */
    final public function setForceUpdate($value): void
    {
        $this->forceUpdate = (bool)$value;
    }

    /**
     * @return bool
     */
    final public function isForceUpdate()
    {
        return (bool)$this->forceUpdate;
    }

    /**
     * Returns the MailingList service provider short name
     * i.e. - MailChimp, Constant Contact, etc...
     *
     * @return string
     */
    public function getServiceProvider()
    {
        $reflection = new ReflectionClass($this);

        return $reflection->getShortName();
    }

    /**
     * A method that initiates the authentication
     */
    abstract public function initiateAuthentication();

    /**
     * Authorizes the application
     * Returns the access_token
     *
     * @return string
     * @throws IntegrationException
     */
    abstract public function fetchAccessToken();

    /**
     * Perform anything necessary before this integration is saved
     */
    public function onBeforeSave(IntegrationStorageInterface $model): void
    {
    }

    /**
     * @return array
     */
    final public function getSettings()
    {
        return $this->settings ?: [];
    }

    /**
     * @return string
     */
    final public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return boolean
     */
    public function isAccessTokenUpdated()
    {
        return $this->accessTokenUpdated;
    }

    /**
     * @param boolean $accessTokenUpdated
     *
     * @return $this
     */
    public function setAccessTokenUpdated($accessTokenUpdated)
    {
        $this->accessTokenUpdated = (bool)$accessTokenUpdated;

        return $this;
    }

    /**
     * @param mixed|null  $value
     * @return bool|string
     */
    public function convertCustomFieldValue(FieldObject $fieldObject, mixed $value = null): bool|string
    {
        if (is_array($value) && $fieldObject->getType() !== FieldObject::TYPE_ARRAY) {
            $value = implode(', ', $value);
        }

        switch ($fieldObject->getType()) {
            case FieldObject::TYPE_NUMERIC:
                return (int)preg_replace('/\D/', '', $value) ?: '';

            case FieldObject::TYPE_BOOLEAN:
                return (bool)$value;

            case FieldObject::TYPE_ARRAY:
                if (!is_array($value)) {
                    $value = [$value];
                }

                return $value;

            case FieldObject::TYPE_STRING:
            default:
                return (string)$value;
        }
    }

    protected function getHandler(): IntegrationHandlerInterface
    {
        return $this->handler;
    }

    /**
     * @param string $accessToken
     */
    protected final function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return string
     */
    abstract protected function getApiRootUrl();

    /**
     * Returns a combined URL of api root + endpoint
     *
     * @param string $endpoint
     *
     * @return string
     */
    final protected function getEndpoint($endpoint)
    {
        $root     = rtrim($this->getApiRootUrl(), '/');
        $endpoint = ltrim($endpoint, '/');

        return "$root/$endpoint";
    }

    /**
     * Get settings by handle
     *
     * @param string $handle
     *
     * @return mixed|null
     * @throws IntegrationException
     */
    final protected function getSetting($handle)
    {
        $blueprint = $this->getSettingBlueprint($handle);

        if ($blueprint->getType() === SettingBlueprint::TYPE_CONFIG) {
            return $this->configuration->get($blueprint->getHandle());
        }

        if (in_array($blueprint->getType(), [SettingBlueprint::TYPE_CONFIG_TEXT, SettingBlueprint::TYPE_CONFIG_PASSWORD], true)) {
            $value = $this->settings[$handle] ?? null;
            if ($value === null || $value === '') {
                return $this->configuration->get($handle);
            }

            return $blueprint->isSecret() ? IntegrationSecret::decrypt($value) : $value;
        }

        if (isset($this->settings[$handle])) {
            if ($blueprint->getType() === SettingBlueprint::TYPE_BOOL) {
                if (is_bool($this->settings[$handle])) {
                    return $this->settings[$handle];
                }

                return strtolower($this->settings[$handle]) === "y";
            }

            return $this->settings[$handle];
        }

        if ($blueprint->isRequired()) {
            throw new IntegrationException(
                $this->getTranslator()->translate(
                    '{setting} setting not specified',
                    ['setting' => $blueprint->getLabel()]
                )
            );
        }

        return null;
    }

    /**
     * @param string $handle
     *
     * @return $this
     */
    final protected function setSetting($handle, mixed $value)
    {
        // Check for blueprint validity
        $this->getSettingBlueprint($handle);

        $this->settings[$handle] = $value;

        return $this;
    }

    /**
     * @param string $handle
     *
     * @return SettingBlueprint
     * @throws IntegrationException
     */
    private function getSettingBlueprint($handle)
    {
        foreach (static::getSettingBlueprints() as $blueprint) {
            if ($blueprint->getHandle() === $handle) {
                return $blueprint;
            }
        }

        throw new IntegrationException(
            $this->getTranslator()->translate(
                'Could not find setting blueprints for {handle}',
                ['handle' => $handle]
            )
        );
    }
}
