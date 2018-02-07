<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ClassicFormHelper extends AddonBuilder
{
    public function getClassicForms()
    {
        $this->model('form')->order_by('form_label');
        $rows = $this->model('form')->get();

        if ($rows !== FALSE)
        {
            // -------------------------------------
            //	check for composer for each form
            // -------------------------------------

            $form_ids = array();

            $potential_composer_ids = array();

            foreach ($rows as $row)
            {
                $form_ids[] = $row['form_id'];

                if ($this->is_positive_intlike($row['composer_id']))
                {
                    $potential_composer_ids[$row['form_id']] = $row['composer_id'];
                }
            }

            $has_composer = array();

            if (!empty($potential_composer_ids))
            {
                $composer_ids = $this->model('composer')
                    ->key('composer_id', 'composer_id')
                    ->where('preview !=', 'y')
                    ->where_in(
                        'composer_id',
                        array_values($potential_composer_ids)
                    )
                    ->get();

                if ( ! empty($composer_ids))
                {
                    foreach ($potential_composer_ids as $form_id => $composer_id)
                    {
                        if (in_array($composer_id, $composer_ids))
                        {
                            $has_composer[$form_id] = $composer_id;
                        }
                    }
                }
            }

            foreach ($rows as $key => $row)
            {
                $rows[$key]['has_composer'] = false;

                if (isset($has_composer[$row['form_id']])) {
                    $rows[$key]['has_composer'] = true;
                }
            }
        }

        return $rows;
    }

    public function getFormComposerId($formId)
    {
        $form_data = $form_data = $this->model('form')->get_info($formId);

        return $form_data['composer_id'];
    }

    public function getComposerDataById($composerId)
    {
        $composer = $this->model('composer')->get_row($composerId);

        return json_decode($composer['composer_data'], TRUE);
    }
}
