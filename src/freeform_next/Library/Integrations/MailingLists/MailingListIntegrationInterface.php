<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations\MailingLists;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Layout;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects\ListObject;

interface MailingListIntegrationInterface
{
    /**
     * @return ListObject[]
     */
    public function getLists();

    /**
     * @param string $listId
     *
     * @return ListObject
     */
    public function getListById($listId);

    /**
     * Push emails to a specific mailing list for the service provider
     *
     * @param ListObject $mailingList
     * @param array      $emails
     * @param array      $mappedValues - key => value pairs of integrations fields against form fields
     *
     * @return bool
     */
    public function pushEmails(ListObject $mailingList, array $emails, array $mappedValues);
}
