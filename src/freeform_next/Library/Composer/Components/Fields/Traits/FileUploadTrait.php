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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait FileUploadTrait
{
    /** @var int */
    protected $assetSourceId;

    /**
     * @return int
     */
    public function getAssetSourceId()
    {
        return $this->assetSourceId;
    }
}
