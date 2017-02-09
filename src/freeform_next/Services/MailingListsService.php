<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

/**
 * Created by PhpStorm.
 * User: gustavs
 * Date: 17.8.2
 * Time: 17:12
 */

namespace Solspace\Addons\FreeformNext\Services;


use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\ListNotFoundException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\MailingListIntegrationNotFoundException;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\AbstractMailingListIntegration;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;

class MailingListsService implements MailingListHandlerInterface
{
    public function updateLists(AbstractMailingListIntegration $integration, array $mailingLists)
    {
        // TODO: Implement updateLists() method.
    }

    public function getAllIntegrations()
    {
        // TODO: Implement getAllIntegrations() method.
    }

    public function getIntegrationById($id)
    {
        // TODO: Implement getIntegrationById() method.
    }

    public function getLists(AbstractMailingListIntegration $integration)
    {
        // TODO: Implement getLists() method.
    }

    public function getListById(AbstractMailingListIntegration $integration, $id)
    {
        // TODO: Implement getListById() method.
    }

    public function flagMailingListIntegrationForUpdating(AbstractMailingListIntegration $integration)
    {
        // TODO: Implement flagMailingListIntegrationForUpdating() method.
    }
}