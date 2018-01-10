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

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Egulias\EmailValidator\EmailValidator;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\PlaceholderInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\RecipientInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\PlaceholderTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\RecipientTrait;

class EmailField extends AbstractField implements RecipientInterface, MultipleValueInterface, PlaceholderInterface
{
    use PlaceholderTrait;
    use MultipleValueTrait;
    use RecipientTrait;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return FieldInterface::TYPE_EMAIL;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        $values = $this->getValue();
        if (empty($values)) {
            $values = [''];
        }

        $output = '';
        foreach ($values as $value) {
            $output .= '<input '
                . $this->getAttributeString('name', $this->getHandle())
                . $this->getAttributeString('type', $this->getType())
                . $this->getAttributeString('id', $this->getIdAttribute())
                . $this->getAttributeString('class', $attributes->getClass())
                . $this->getAttributeString(
                    'placeholder',
                    $this->getForm()->getTranslator()->translate(
                        $attributes->getPlaceholder() ?: $this->getPlaceholder()
                    )
                )
                . $this->getAttributeString('value', $value, false)
                . $this->getRequiredAttribute()
                . $attributes->getInputAttributesAsString()
                . '/>';
        }

        return $output;
    }

    /**
     * Returns an array value of all possible recipient Email addresses
     *
     * Either returns an ["email", "email"] array
     * Or an array with keys as recipient names, like ["Jon Doe" => "email", ..]
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->getValue();
    }

    /**
     * Validate the field and add error messages if any
     *
     * @return array
     */
    protected function validate()
    {
        $errors = parent::validate();

        $validator = new EmailValidator();
        foreach ($this->getValue() as $email) {
            if (empty($email)) {
                continue;
            }

            $hasDot = preg_match('/@.+\..+$/', $email);

            if (!$hasDot || !$validator->isValid($email)) {
                $errors[] = $this->translate('{email} is not a valid email address', ['email' => $email]);
            }
        }

        return $errors;
    }
}
