<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\DataObjects;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Layout;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListIntegrationInterface;

class ListObject implements \JsonSerializable
{
    /** @var MailingListIntegrationInterface */
    private $mailingList;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $memberCount;

    /** @var FieldObject[] */
    private $fields;

    /**
     * ListObject constructor.
     *
     * @param MailingListIntegrationInterface $mailingList
     * @param string                          $id
     * @param string                          $name
     * @param FieldObject[]                   $fields
     * @param int                             $memberCount
     */
    public function __construct(
        MailingListIntegrationInterface $mailingList,
        $id,
        $name,
        array $fields = [],
        $memberCount = 0
    ) {
        $this->id          = $id;
        $this->name        = $name;
        $this->fields      = $fields;
        $this->memberCount = $memberCount;
        $this->mailingList = $mailingList;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FieldObject[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getMemberCount()
    {
        return $this->memberCount;
    }

    /**
     * @param array  $emails
     * @param array  $mappedValues
     *
     * @return bool
     */
    public function pushEmailsToList(array $emails, array $mappedValues)
    {
        return $this->mailingList->pushEmails($this, $emails, $mappedValues);
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'fields'      => $this->getFields(),
            'memberCount' => $this->getMemberCount(),
        ];
    }
}
