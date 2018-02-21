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

namespace Solspace\Addons\FreeformNext\Library\FileUploads;

class FileUploadResponse
{
    /** @var int[] */
    private $assetIds;

    /** @var array */
    private $errors;

    /**
     * FileUploadResponse constructor.
     *
     * @param int[] $assetIds
     * @param array $errors
     */
    public function __construct(array $assetIds = null, array $errors = [])
    {
        $this->assetIds = $assetIds ?: [];
        $this->errors   = $errors;
    }

    /**
     * @return int[]
     */
    public function getAssetIds()
    {
        return $this->assetIds;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
