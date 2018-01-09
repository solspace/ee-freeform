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

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

class FreeformStatistics
{
    /** @var int */
    private $submissionCount;

    /** @var int */
    private $spamBlockCount;

    /**
     * FreeformStatistics constructor.
     *
     * @param int $submissionCount
     * @param int $spamBlockCount
     */
    public function __construct($submissionCount, $spamBlockCount)
    {
        $this->submissionCount           = $submissionCount;
        $this->spamBlockCount            = $spamBlockCount;
    }

    /**
     * @return int
     */
    public function getSubmissionCount()
    {
        return $this->submissionCount;
    }

    /**
     * @return int
     */
    public function getSpamBlockCount()
    {
        return $this->spamBlockCount;
    }
}
