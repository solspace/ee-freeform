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

trait MultipleValueTrait
{
    /** @var array */
    protected $values;

    /**
     * @return array
     */
    public function getValue()
    {
        $valueOverride = $this->getValueOverride();
        if ($valueOverride) {
            if (!is_array($valueOverride)) {
                return [$valueOverride];
            }

            return $valueOverride;
        }

        $values = $this->values;

        if (empty($values)) {
            $values = [];
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        return $values;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->values = $value;

        return $this;
    }
}
