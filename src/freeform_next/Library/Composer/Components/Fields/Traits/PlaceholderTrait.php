<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait PlaceholderTrait
{
    /** @var string */
    protected $placeholder;

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }
}
