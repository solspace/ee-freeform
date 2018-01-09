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

namespace Solspace\Addons\FreeformNext\Library\FileUploads;

class FileUploadResponse
{
    /** @var int */
    private $assetId;

    /** @var array */
    private $errors;

    /**
     * FileUploadResponse constructor.
     *
     * @param null  $assetId
     * @param array $errors
     */
    public function __construct($assetId = null, $errors = [])
    {
        $this->assetId = $assetId;
        $this->errors  = $errors;
    }

    /**
     * @return int
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
