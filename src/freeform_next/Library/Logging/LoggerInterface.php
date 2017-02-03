<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Logging;

interface LoggerInterface
{
    const LEVEL_INFO    = "info";
    const LEVEL_WARNING = "warning";
    const LEVEL_ERROR   = "error";

    public function log($level, $message);
}
