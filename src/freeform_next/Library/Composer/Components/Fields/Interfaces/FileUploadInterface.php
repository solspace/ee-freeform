<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces;

use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FileUploadException;

interface FileUploadInterface
{
    /**
     * @return int
     */
    public function getAssetSourceId();

    /**
     * Attempt to upload the file to its respective location
     *
     * @return int - asset ID
     * @throws FileUploadException
     */
    public function uploadFile();
}
