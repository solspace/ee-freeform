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


use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadResponse;

class FilesService implements FileUploadHandlerInterface
{
    public function uploadFile(FileUploadField $field)
    {
        // TODO: Implement uploadFile() method.
    }

    public function markAssetUnfinalized($assetId)
    {
        // TODO: Implement markAssetUnfinalized() method.
    }

    public function cleanUpUnfinalizedAssets()
    {
        // TODO: Implement cleanUpUnfinalizedAssets() method.
    }

    public function getFileKinds()
    {
        // TODO: Implement getFileKinds() method.
    }
}