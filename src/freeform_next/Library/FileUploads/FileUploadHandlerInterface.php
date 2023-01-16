<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\FileUploads;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;

interface FileUploadHandlerInterface
{
    /**
     * Uploads a file and flags it as "unfinalized"
     * It will be finalized only after the form has been submitted fully
     *
     * All unfinalized files will be deleted after a certain amount of time
     *
     * @param FileUploadField $field
     *
     * @return FileUploadResponse
     */
    public function uploadFile(FileUploadField $field);

    /**
     * Stores the unfinalized assetId in the database
     * So that it can be deleted later if the form hasn't been finalized
     *
     * @param int $assetId
     */
    public function markAssetUnfinalized($assetId);

    /**
     * Remove all unfinalized assets which are older than the TTL
     * specified in settings
     */
    public function cleanUpUnfinalizedAssets();

    /**
     * Returns an array of all file kinds
     * [type => [ext, ext, ..]
     * I.e. ["images" => ["gif", "png", "jpg", "jpeg", ..]
     *
     * @return array
     */
    public function getFileKinds();
}
