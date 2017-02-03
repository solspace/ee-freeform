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

namespace Solspace\Addons\FreeformNext\Library\Factories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\EmailField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\HiddenField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\HtmlField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\MailingListField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\RadioGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SelectField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextareaField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FieldProperties;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;

class ComposerFieldFactory
{
    /**
     * @param Form             $form
     * @param FieldProperties  $properties
     * @param FormValueContext $formValueContext
     * @param int              $pageIndex
     *
     * @return HiddenField|TextField
     * @throws ComposerException
     */
    public static function createFromProperties(
        Form $form,
        FieldProperties $properties,
        FormValueContext $formValueContext,
        $pageIndex
    ) {
        switch ($properties->getType()) {
            case FieldInterface::TYPE_TEXT:
                return TextField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_TEXTAREA:
                return TextareaField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_EMAIL:
                return EmailField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_HIDDEN:
                return HiddenField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_CHECKBOX:
                return CheckboxField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_CHECKBOX_GROUP:
                return CheckboxGroupField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_RADIO_GROUP:
                return RadioGroupField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_SELECT:
                return SelectField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_HTML:
                return HtmlField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_SUBMIT:
                return SubmitField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_DYNAMIC_RECIPIENTS:
                return DynamicRecipientField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_FILE:
                return FileUploadField::createFromProperties($form, $properties, $formValueContext, $pageIndex);

            case FieldInterface::TYPE_MAILING_LIST:
                return MailingListField::createFromProperties($form, $properties, $formValueContext, $pageIndex);
        }

        throw new ComposerException(
            $form->getTranslator()->translate(
                "Could not create a field of type {type}",
                ["type" => $properties->getType()]
            )
        );
    }
}
