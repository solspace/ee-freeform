<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations;

interface IntegrationInterface
{
    /**
     * Setting this to true will force re-fetching of all lists
     *
     * @param bool $value
     */
    public function setForceUpdate($value);

    /**
     * Check if it's possible to connect to the API
     *
     * @return bool
     */
    public function checkConnection();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \DateTime
     */
    public function getLastUpdate();

    /**
     * Returns the integration service provider short name
     * i.e. - MailChimp, Constant Contact, Salesforce, etc...
     *
     * @return string
     */
    public function getServiceProvider();

    /**
     * Initiates the authentication process
     */
    public function initiateAuthentication();

    /**
     * Authorizes the application and fetches the access token
     *
     * @return string - access token
     */
    public function fetchAccessToken();

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @return boolean
     */
    public function isAccessTokenUpdated();

    /**
     * @param boolean $accessTokenUpdated
     *
     * @return $this
     */
    public function setAccessTokenUpdated($accessTokenUpdated);

    /**
     * @return array
     */
    public function getSettings();
}
