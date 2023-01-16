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
