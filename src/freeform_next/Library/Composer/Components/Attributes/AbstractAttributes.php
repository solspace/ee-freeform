<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;

abstract class AbstractAttributes
{
    /**
     * CustomFormAttributes constructor.
     *
     * @param array|null $attributes
     *
     * @throws FreeformException
     */
    public function __construct(array $attributes = null)
    {
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                } else {
                    throw new FreeformException(sprintf("Invalid attribute '%s' provided", $key));
                }
            }
        }
    }

    /**
     * Merges the passed attributes into the existing ones
     *
     * @param array|null $attributes
     *
     * @throws FreeformException
     */
    public function mergeAttributes(array $attributes = null)
    {
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                } else {
                    throw new FreeformException(sprintf("Invalid attribute '%s' provided", $key));
                }
            }
        }
    }

    /**
     * Walk through the array and create an attribute string
     *
     * @param array $array
     *
     * @return string
     */
    final protected function getAttributeStringFromArray(array $array)
    {
        $attributeString = '';

        foreach ($array as $key => $value) {
            if (is_bool($value) && $value) {
                $attributeString .= "$key ";
            } else if (!is_bool($value)) {
                $attributeString .= "$key=\"$value\" ";
            }
        }

        return $attributeString ? ' ' . $attributeString : '';
    }

    /**
     * @param array|string $value
     *
     * @return string
     */
    final protected function extractClassValue($value)
    {
        if (empty($value)) {
            return '';
        }

        if (is_array($value)) {
            $value = implode(' ', $value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool|null
     */
    final protected function getBooleanValue($value = null)
    {
        if ($value !== null) {
            switch (strtolower($value)) {
                case 'yes':
                case '1':
                case 'y':
                    return true;

                default:
                    return false;
            }
        }

        return null;
    }
}
