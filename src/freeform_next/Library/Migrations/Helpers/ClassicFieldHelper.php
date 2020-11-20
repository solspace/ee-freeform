<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use Solspace\Addons\Freeform\Library\Fields;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ClassicFieldHelper extends AddonBuilder
{
    public function getClassicFields()
    {
        $this->model('field')->order_by('field_label');

        $fields = $this->model('field')->get();

        $oldFieldsLibrary = new Fields();

        foreach ($fields as $key => $field) {

            if ($field['field_type'] === 'file_upload') {
                $instance =& $oldFieldsLibrary->get_field_instance($field['field_id']);
                $fields[$key]['file_count'] = (int) $instance->settings['allowed_upload_count'];
            }

            if ($field['field_type'] == 'country_select') {

                /** @var \Country_select_freeform_ft $instance */
                $instance =& $oldFieldsLibrary->get_field_instance($field['field_id']);

                if ($instance) {
                    $countries = $instance->get_countries();

                    if ($countries) {
                        $fields[$key]['countries'] = $countries;
                    }
                }
            }

            if ($field['field_type'] == 'state_select') {
                $allStates = [];

                $states 	= array_map(
                    'trim',
                    preg_split(
                        '/[\n\r]+/',
                        lang('list_of_us_states'),
                        -1,
                        PREG_SPLIT_NO_EMPTY
                    )
                );

                foreach ($states as $stateValue)
                {
                    $allStates[
                    preg_replace('/[\w|\s]+\(([a-zA-Z\-_]+)\)$/', "$1", $stateValue)
                    ] = preg_replace('/\s+\([a-zA-Z\-_]+\)$/', '', $stateValue);
                }

                if ($allStates) {
                    $fields[$key]['states'] = $allStates;
                }
            }

            if ($field['field_type'] == 'province_select') {
                $allProvinces = [];

                $provinces 	= array_map(
                    'trim',
                    preg_split(
                        '/[\n\r]+/',
                        lang('list_of_canadian_provinces'),
                        -1,
                        PREG_SPLIT_NO_EMPTY
                    )
                );

                //need matching key => value pairs for the select values to be correct
                //for the output value we are removing the ' (MB)' code for the value and the 'Manitoba' code for the key
                foreach ($provinces as $provinceValue)
                {
                    $allProvinces[
                    preg_replace('/[\w|\s]+\(([a-zA-Z\-_]+)\)$/', "$1", $provinceValue)
                    ] = preg_replace('/\s+\([a-zA-Z\-_]+\)$/', '', $provinceValue);
                }

                if ($allProvinces) {
                    $fields[$key]['provinces'] = $allProvinces;
                }
            }
        }

        return $fields;
    }

    public function isCustomValuesEnabledForSelect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('select_list_type', $settings) && $settings['select_list_type'] === 'value_label') {
            return true;
        }

        return false;
    }

    public function isCustomChannelForSelect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('select_list_type', $settings) && $settings['select_list_type'] === 'channel_field') {
            return true;
        }

        return false;
    }

    public function isCustomValuesTextAreaSelect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('select_list_type', $settings) && $settings['select_list_type'] === 'nld_textarea') {
            return true;
        }

        return false;
    }

    public function isCustomValuesEnabledForCheckboxGroup($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('checkbox_group_list_type', $settings) && $settings['checkbox_group_list_type'] === 'value_label') {
            return true;
        }

        return false;
    }

    public function isCustomValuesTextAreaCheckboxGroup($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('checkbox_group_list_type', $settings) && $settings['checkbox_group_list_type'] === 'nld_textarea') {
            return true;
        }

        return false;
    }

    public function isCustomValuesChannelCheckboxGroup($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('checkbox_group_list_type', $settings) && $settings['checkbox_group_list_type'] === 'channel_field') {
            return true;
        }

        return false;
    }

    public function isCustomValuesEnabledForMultiselect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('multiselect_list_type', $settings) && $settings['multiselect_list_type'] === 'value_label') {
            return true;
        }

        return false;
    }

    public function isCustomValuesTextAreaMultiselect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('multiselect_list_type', $settings) && $settings['multiselect_list_type'] === 'nld_textarea') {
            return true;
        }

        return false;
    }

    public function isCustomValuesChannelMultiselect($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('multiselect_list_type', $settings) && $settings['multiselect_list_type'] === 'channel_field') {
            return true;
        }

        return false;
    }

    public function isCustomValuesEnabledForRadio($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('radio_list_type', $settings) && $settings['radio_list_type'] === 'value_label') {
            return true;
        }

        return false;
    }

    public function isCustomValuesTextAreaRadio($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('radio_list_type', $settings) && $settings['radio_list_type'] === 'nld_textarea') {
            return true;
        }

        return false;
    }

    public function isCustomValuesChannelRadio($classicField)
    {
        $settings = $this->getSettings($classicField);

        if (array_key_exists('radio_list_type', $settings) && $settings['radio_list_type'] === 'channel_field') {
            return true;
        }

        return false;
    }

    public function getSettings($classicField)
    {
        return json_decode($classicField['settings'], true);
    }
}
