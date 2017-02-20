<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;

class FieldController extends Controller
{
    public function saveField($fieldId = null)
    {
        $view = new AjaxView();
        $field = FieldRepository::getInstance()->getOrCreateField($fieldId);

        $post = $_POST;
        $validValues = [];
        foreach ($post as $key => $value) {
            if (property_exists($field, $key)) {
                $validValues[$key] = $value;
            }
        }

        $fieldHasOptions = in_array(
            $field->type,
            [
                FieldInterface::TYPE_RADIO_GROUP,
                FieldInterface::TYPE_CHECKBOX_GROUP,
                FieldInterface::TYPE_SELECT,
                FieldInterface::TYPE_DYNAMIC_RECIPIENTS,
            ],
            true
        );

        if (isset($post['types'][$field->type])) {
            $fieldSpecificPost = $post['types'][$field->type];
            foreach ($fieldSpecificPost as $key => $value) {
                if (property_exists($field, $key)) {
                    $validValues[$key] = $value;
                }
            }

            $hasValues = isset($fieldSpecificPost["values"]) && is_array($fieldSpecificPost["values"]);

            if ($fieldHasOptions && $hasValues) {
                $field->setPostValues($fieldSpecificPost);
            } else {
                $validValues['values'] = null;
            }
        }

        $field->set($validValues);
        $field->save();

        $view->addVariable('success', true);

        return $view;
    }
}
