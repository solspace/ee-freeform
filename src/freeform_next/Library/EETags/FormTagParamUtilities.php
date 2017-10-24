<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Stringy\Stringy;

class FormTagParamUtilities
{
    const PATTERN_ARRAY_ATTRIBUTES  = '/(override_values|input_attributes|form_attributes):([a-zA-Z_\-0-9]+)=(?:"|\')([^\'"]*)(?:"|\')?/';
    const PATTERN_SINGLE_ATTRIBUTES = '/(input_attributes|form_attributes):([a-zA-Z_\-0-9]+)(?:\s|\})/';

    /**
     * @param Form $form
     */
    public static function setFormCustomAttributes(Form $form)
    {
        $new = [
            'overrideValues'  => [],
            'inputAttributes' => [],
            'formAttributes'  => [],
        ];

        $properties = $form->getCustomAttributes()->getManifest();
        foreach ($properties as $property) {
            $lowercase = (string) Stringy::create($property)->underscored();
            $value     = ee()->TMPL->fetch_param($lowercase);

            if ($value) {
                $new[$property] = $value;
            }
        }

        $tagproper = ee()->TMPL->tagproper;

        if (preg_match_all(self::PATTERN_ARRAY_ATTRIBUTES, $tagproper, $matches)) {
            /** @var array $keys */
            /** @var array $handles */
            /** @var array $values */
            list ($_, $keys, $handles, $values) = $matches;

            foreach ($handles as $index => $handle) {
                $key          = $keys[$index];
                $camelizedKey = (string) Stringy::create($key)->camelize();
                $value        = $values[$index];

                if ($camelizedKey !== 'overrideValues') {
                    $new[$camelizedKey][$handle] = $value;
                    continue;
                }

                $field = $form->get($handle);
                if (!$field) {
                    continue;
                }

                if ($field instanceof MultipleValueInterface) {
                    $value = explode('|', $value);
                }

                $new[$camelizedKey][$handle] = $value;
            }
        }

        if (preg_match_all(self::PATTERN_SINGLE_ATTRIBUTES, $tagproper, $matches)) {
            /** @var array $keys */
            /** @var array $handles */
            list ($_, $keys, $handles) = $matches;

            foreach ($handles as $index => $handle) {
                $key          = $keys[$index];
                $camelizedKey = (string) Stringy::create($key)->camelize();

                $new[$camelizedKey][$handle] = true;
            }
        }

        $form->setAttributes($new);
    }
}
