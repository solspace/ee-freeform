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
