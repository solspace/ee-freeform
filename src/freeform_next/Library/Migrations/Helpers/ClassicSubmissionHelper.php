<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ClassicSubmissionHelper extends AddonBuilder
{
    public $row_limit = 100;
    public $finished = false;
    public $formId;
    public $page;
    public $formPagesCount;

    public function getClassicSubmissions($formId, $page)
    {
        if (!$formId) {
            $formId = $this->getNextForm()->id;
        }

        if (!$page) {
            $page = 1;
        }

        $this->formId = (int) $formId;
        $this->page = (int) $page;

        $submissions = [];

        $form   = $this->getForm($formId);
        $result = $this->getSubmissions($form->legacyId, $page);

        $realEntries = [];

        foreach ($result['submissions'] as $entry) {

            $entryId = $entry['entry_id'];

            $realEntry = [];
            $realEntry['legacyId'] = $entryId;
            $realEntry['entryDate'] = $entry['entry_date'];
            $realEntry['status'] = $entry['status'];

            foreach ($entry as $columnName => $value) {
                $fieldName = array_search($columnName, $result['fieldsByName']);
                $fieldType = null;
                $fieldId = null;

                if (array_key_exists($columnName, $result['fieldsByType'])) {
                    $fieldType = $result['fieldsByType'][$columnName];
                }

                if (array_key_exists($columnName, $result['fieldsById'])) {
                    $fieldId = $result['fieldsById'][$columnName];
                }

                if ($fieldType == 'file_upload') {
                    $fileIds = $this->getFieldUpload($form->legacyId, $entryId, $fieldId);
                    $value = null;
                    if (is_array($fileIds) && !empty($fileIds)) {
                        $value = [];
                        foreach ($fileIds as $fileId) {
                            $value[] = $fileId['file_id'];
                        }
                    }
                }

                $realEntry[$fieldName] = $value;
            }

            $realEntries[] = $realEntry;
        }

        $submissions[$form->id] = $realEntries;


        return $submissions;
    }

    public function getNextForm($formId = null)
    {
        $forms = FormRepository::getInstance()->getAllForms();

        if (!$formId) {
            return $forms[0];
        }

        foreach ($forms as $key => $form) {
            if ($form->id == $formId && array_key_exists($key+1, $forms)) {
                return $forms[$key+1];
            }
        }

        return false;
    }

    public function getFormsCount()
    {
        $forms = FormRepository::getInstance()->getAllForms();

        return count($forms);
    }

    private function getForm($formId)
    {
        return FormRepository::getInstance()->getFormById($formId);
    }

    /**
     * @param int $formId
     * @param int $page
     *
     * @return array
     */
    private function getSubmissions($formId, $page)
    {
        $fieldsByName = $fieldsByType = $fieldsById = [];
        $form         = $this->model('form')->get_info($formId);

        foreach ($form['fields'] as $field) {
            $identificator = 'form_field_' . $field['field_id'];

            $fieldsByName[$field['field_name']] = $identificator;
            $fieldsByType[$identificator]       = $field['field_type'];
            $fieldsById[$identificator]         = $field['field_id'];
        }

        $table = 'exp_freeform_form_entries_' . $formId;

        $limit = $this->row_limit;
        $offset = ($page - 1) * $limit;

        $submissions = ee()->db
            ->query("SELECT * FROM $table ORDER BY entry_id ASC LIMIT $offset, $limit")
            ->result_array();

        $totalEntries = (int) ee()->db->query("SELECT COUNT(*) as `count` FROM $table")->row()->count;

        $this->formPagesCount = $this->getFormPages($totalEntries, $limit);
        $this->finished = false;

        if ((int) $page === (int)$this->formPagesCount || $totalEntries === 0) {
            $this->finished = true;
        }

        return [
            'submissions'  => $submissions,
            'fieldsByName' => $fieldsByName,
            'fieldsByType' => $fieldsByType,
            'fieldsById'   => $fieldsById,
        ];
    }

    /**
     * Visible Columns
     *
     * @access	protected
     * @param   $possible_columns possible columns
     * @return	array array of visible columns
     */

    protected function visible_columns($standard_columns = array(),
                                       $possible_columns = array())
    {
        // -------------------------------------
        //	get column settings
        // -------------------------------------

        $column_settings	= array();



        $field_layout_prefs = $this->model('preference')->preference('field_layout_prefs');
        $member_id			= ee()->session->userdata('member_id');
        $group_id			= ee()->session->userdata('group_id');
        $f_prefix			= $this->model('form')->form_field_prefix;

        //¿existe? Member? Group? all?
        if ($field_layout_prefs)
        {
            //$field_layout_prefs = json_decode($field_layout_prefs, TRUE);

            $entry_layout_prefs = (
            isset($field_layout_prefs['entry_layout_prefs']) ?
                $field_layout_prefs['entry_layout_prefs'] :
                FALSE
            );

            if ($entry_layout_prefs)
            {
                if (isset($entry_layout_prefs['member'][$member_id]))
                {
                    $column_settings = $entry_layout_prefs['member'][$member_id];
                }
                else if (isset($entry_layout_prefs['all']))
                {
                    $column_settings = $entry_layout_prefs['all'];
                }
                else if (isset($entry_layout_prefs['group'][$group_id]))
                {
                    $column_settings = $entry_layout_prefs['group'][$group_id];
                }
            }
        }

        //if a column is missing, we don't want to error
        //and if its newer than the settings, show it by default
        //settings are also in order of appearence here.

        //we also store the field ids without the prefix
        //in case someone changed it. That would probably
        //hose everything, but who knows? ;)
        if ( ! empty($column_settings))
        {
            $to_sort = array();

            //we are going over possible instead of settings in case something
            //is new or an old column is missing
            foreach ($possible_columns as $cid)
            {
                //if these are new, put them at the end
                if ( ! in_array($cid, $column_settings['visible']) AND
                    ! in_array($cid, $column_settings['hidden'])
                )
                {
                    $to_sort[$cid] = $cid;
                }
            }

            //now we want columns from the settings order to go first
            //this way stuff thats not been removed gets to keep settings
            foreach ($column_settings['visible'] as $ecid)
            {
                if (in_array($ecid, $possible_columns))
                {
                    //since we are getting our real results now
                    //we can add the prefixes
                    if ( ! in_array($ecid, $standard_columns) )
                    {
                        $ecid = $f_prefix . $ecid;
                    }

                    $visible_columns[] = $ecid;
                }
            }

            //and if we have anything left over (new fields probably)
            //its at the end
            if ( ! empty($to_sort))
            {
                foreach ($to_sort as $tsid)
                {
                    //since we are getting our real results now
                    //we can add the prefixes
                    if ( ! in_array($tsid, $standard_columns) )
                    {
                        $tsid = $f_prefix . $tsid;
                    }

                    $visible_columns[] = $tsid;
                }
            }
        }
        //if we don't have any settings, just toss it all in in order
        else
        {
            foreach ($possible_columns as $pcid)
            {
                if ( ! in_array($pcid, $standard_columns) )
                {
                    $pcid = $f_prefix . $pcid;
                }

                $visible_columns[] = $pcid;
            }

            //in theory it should always be there if prefs are empty ...

            $default_hide = array('site_id', 'entry_id', 'complete');

            foreach ($default_hide as $hide_me_seymour)
            {
                if (in_array($hide_me_seymour, $visible_columns))
                {
                    unset(
                        $visible_columns[
                        array_search(
                            $hide_me_seymour,
                            $visible_columns
                        )
                        ]
                    );
                }
            }

            //fix keys, but preserve order
            $visible_columns = array_merge(array(), $visible_columns);
        }

        return $visible_columns;
    }

    /**
     * Format CP date
     *
     * @access	public
     * @param	mixed	$date	unix time
     * @return	string			unit time formatted to cp date formatting pref
     */

    public function format_cp_date($date)
    {
        return $this->lib('Utils')->format_cp_date($date);
    }

    /**
     * Setup Select Fields
     *
     * Builds a field selection snootching the view and JS from
     * the relationship fieldtype
     *
     * @access	protected
     * @param	array	$available_fields	available choices (value => label)
     * @param	array 	$order				choices in order of appearance (value)
     * @return	string						html view for field in form
     */

    protected function setup_select_fields($available_fields, $order = array())
    {
        //---------------------------------------------
        //  Dependencies
        //---------------------------------------------

        $multiple	= true;
        $channels	= array();
        $field_name = 'form_fields';
        $settings   = '';
        $selected	= $order;
        //make order array keys with blank entries
        //because the related field is weird like that
        $related	= count($order) ?
            array_combine($order, array_fill(0, count($order), '')) :
            array();
        $entries	= array();

        sort($selected);

        ee()->cp->add_js_script(array(
            'plugin'	=> 'ee_interact.event',
            'file'		=> 'fields/relationship/cp',
            'ui'		=> 'sortable'
        ));

        // -------------------------------------
        //	fields ('entries')
        // -------------------------------------

        if ( ! empty($available_fields))
        {
            foreach ($available_fields as $field_id => $field_label)
            {
                $new					= new \stdClass();
                $channel				= new \stdClass();
                $channel->channel_id	= 0;
                $channel->channel_title	= '';
                $new->Channel			= $channel;
                $new->title				= $field_label;
                $new->entry_id			= $field_id;

                if (isset($related[$field_id]))
                {
                    $related[$field_id] = $new;
                }

                $entries[]	= $new;
            }
        }

        //---------------------------------------------
        //  Field view
        //---------------------------------------------

        $field_view	= ee('View')->make('relationship:publish')
            ->render(compact(
                'field_name',
                'entries',
                'selected',
                'settings',
                'related',
                'multiple',
                'channels'
            ));

        //---------------------------------------------
        //  Change references to 'items' to 'authors'
        //---------------------------------------------
        //	We change references to be 'author' oriented
        //	and we also hide the reorder handles on the
        //	related authors so that they cannot be drag &
        //	drop reordered.
        //---------------------------------------------

        $field_view	= str_replace(
            array(
                lang('item_to_relate_with'),
                lang('items_to_relate_with'),
                lang('items_related_to'),
                lang('no_entry_related'),
                lang('search_avilable_entries'),
                lang('search_available_entries'),
                lang('search_related_entries'),
                lang('no_entries_found'),
                lang('no_entries_related'),
                lang('items_related_to'),
                '<div class="filters">',
                'class="relate-actions"',
                //last because its generic and can affect
                //other items
                lang('items'),
            ),
            array(
                lang('available_fields'),
                lang('available_fields'),
                lang('selected_fields'),
                '',
                '',
                '',
                '',
                lang('no_fields'),
                lang('no_fields_chosen'),
                '',
                '<div class="filters" style="display:none">',
                'class="relate-actions" style="display:none"',
                lang('fields'),
            ),
            $field_view
        );

        //---------------------------------------------
        //  Return
        //---------------------------------------------

        //this has to be wrapped so the JS works
        return '<div class="publish">' . $field_view . '</div>';
    }

    private function getFormPages($total_entries, $rowLimit)
    {
        return ceil($total_entries / $rowLimit);
    }

    private function getFieldUpload($formId, $entryId, $fieldId)
    {
        ee()->load->model('freeform_file_upload_model');

        return ee()->freeform_file_upload_model
            ->select('file_id')
            ->where('entry_id', $entryId)
            ->where('field_id', $fieldId)
            ->where('form_id', $formId)
            ->get();
    }

    //END setup_select_fields
}
