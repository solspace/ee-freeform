<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
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
