<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Database;

use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\ListNotFoundException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\MailingListIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;

interface MailingListHandlerInterface extends IntegrationHandlerInterface
{
    /**
     * Updates the mailing lists of a given mailing list integration
     *
     * @param AbstractMailingListIntegration $integration
     * @param array                          $mailingLists
     *
     * @return bool
     */
    public function updateLists(AbstractMailingListIntegration $integration, array $mailingLists);

    /**
     * @return AbstractMailingListIntegration[]
     */
    public function getAllIntegrations();

    /**
     * @param int $id
     *
     * @return AbstractMailingListIntegration|null
     * @throws MailingListIntegrationNotFoundException
     */
    public function getIntegrationById($id);

    /**
     * Returns all ListObjects of a particular mailing list integration
     *
     * @param AbstractMailingListIntegration $integration
     *
     * @return ListObject[]
     */
    public function getLists(AbstractMailingListIntegration $integration);

    /**
     * @param AbstractMailingListIntegration $integration
     * @param int                            $id
     *
     * @return ListObject
     * @throws ListNotFoundException
     */
    public function getListById(AbstractMailingListIntegration $integration, $id);

    /**
     * Flag the given mailing list integration so that it's updated the next time it's accessed
     *
     * @param AbstractMailingListIntegration $integration
     */
    public function flagMailingListIntegrationForUpdating(AbstractMailingListIntegration $integration);
}
