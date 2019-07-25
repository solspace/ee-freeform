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

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option;

trait OptionsTrait
{
    /** @var Option[] */
    protected $options;

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptionsAsArray()
    {
        $data = [];
        foreach ($this->options as $option) {
            $data[$option->getValue()] = $option->getLabel();
        }

        return $data;
    }
}
