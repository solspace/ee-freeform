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

namespace Solspace\Addons\FreeformNext\Library\Factories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FieldProperties;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Stringy\Stringy;

class ComposerFieldFactory
{
    private static $defaultFieldNamespace = 'Solspace\Addons\FreeformNext\Library\Composer\Components\Fields';
    private static $proFieldNamespace     = 'Solspace\Addons\FreeformNext\Library\Pro\Fields';

    /**
     * @param Form             $form
     * @param FieldProperties  $properties
     * @param FormValueContext $formValueContext
     * @param int              $pageIndex
     *
     * @return AbstractField
     * @throws ComposerException
     */
    public static function createFromProperties(
        Form $form,
        FieldProperties $properties,
        FormValueContext $formValueContext,
        $pageIndex
    ) {

        /** @var AbstractField $className */
        $className = $properties->getType();
        if ($className === FieldInterface::TYPE_DYNAMIC_RECIPIENTS) {
            $className = 'dynamic_recipient';
        }

        if ($className === FieldInterface::TYPE_FILE) {
            $className = 'file_upload';
        }

        $className = (string) Stringy::create($className)->upperCamelize();
        $className .= 'Field';

        if (class_exists(self::$defaultFieldNamespace . '\\' . $className)) {
            $className = self::$defaultFieldNamespace . '\\' . $className;
        } else if (class_exists(self::$proFieldNamespace . '\\' . $className)) {
            $className = self::$proFieldNamespace . '\\' . $className;
        } else {
            throw new ComposerException(
                $form->getTranslator()->translate(
                    'Could not create a field of type {type}',
                    ['type' => $properties->getType()]
                )
            );
        }

        return $className::createFromProperties($form, $properties, $formValueContext, $pageIndex);
    }
}
