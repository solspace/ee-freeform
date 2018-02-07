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
use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ClassicSubmissionHelper extends AddonBuilder
{
    public $row_limit = 2;
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

        $form = $this->getForm($formId);
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
                    $fileIds = $this->getFieldUpload($form->legacyId, $entryId, $fieldId, $value);
                    $value = null;

                    if ($fileIds && array_key_exists(0, $fileIds) && array_key_exists('file_id', $fileIds[0])) {
                        $value = $fileIds[0]['file_id'];
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

    private function getSubmissions($formId, $page)
    {
        $moderate = FALSE;

        // -------------------------------------
        //	moderate
        // -------------------------------------

        $search_status = ee()->input->get_post('search_status');

        $moderate = (
            $moderate AND
            ($search_status == 'pending' OR
                $search_status === FALSE
            )
        );

        //if moderate and search status was not submitted, fake into pending
        if ($moderate AND $search_status === FALSE)
        {
            $_POST['search_status'] = 'pending';
        }

        $this->cached_vars['moderate']	= $moderate;
        $this->cached_vars['method']	= $method = (
        $moderate ? 'moderate_entries' : 'entries'
        );

        // -------------------------------------
        //	form data? legit? GTFO?
        // -------------------------------------

        $form_id = $formId;

        //form data does all of the proper id validity checks for us
        $form_data = $this->model('form')->get_info($form_id);

        if ( ! $form_data)
        {
            throw new \Exception('Something is wrong');
        }

        $this->cached_vars['form_id']		= $form_id;
        $this->cached_vars['form_label']	= $form_data['form_label'];

        // -------------------------------------
        //	status prefs
        // -------------------------------------

        $form_statuses = $this->model('preference')->get_form_statuses();

        $this->cached_vars['form_statuses'] = $form_statuses;

        // -------------------------------------
        //	rest of models
        // -------------------------------------

        $this->model('entry')->id($form_id);

        // -------------------------------------
        //	custom field labels
        // -------------------------------------

        $standard_columns	= $this->get_standard_column_names();

        //we want author instead of author id until we get data
        $possible_columns	= $standard_columns;
        //key = value
        $all_columns		= array_combine($standard_columns, $standard_columns);
        $column_labels		= array();
        $field_column_names = array();
        $field_column_types = array();
        $field_column_ids   = array();
        $field_column_form_ids   = array();

        //field prefix
        $f_prefix			= $this->model('form')->form_field_prefix;

        //keyed labels for the front end
        foreach ($standard_columns as $column_name)
        {
            $column_labels[$column_name] = lang($column_name);
        }

        // -------------------------------------
        //	check for fields with custom views for entry tables
        // -------------------------------------

        $right_links = array();

        //fields in this form
        foreach ($form_data['fields'] as $field_id => $field_data)
        {
            // -------------------------------------
            //	add custom column names and labels
            // -------------------------------------

            //outputs form_field_1, form_field_2, etc for ->select()
            $field_id_name = $f_prefix . $field_id;

            $field_column_names[$field_id_name]			= $field_data['field_name'];
            $field_column_types[$field_id_name]			= $field_data['field_type'];
            $field_column_ids[$field_id_name]			= $field_data['field_id'];
            $all_columns[$field_id_name]				= $field_data['field_name'];

            $column_labels[$field_data['field_name']]	= $field_data['field_label'];
            $column_labels[$field_id_name]				= $field_data['field_label'];


            // -------------------------------------
            //	get field instance
            // -------------------------------------

            $fieldsInstance = $this->lib('Fields')->get_field_instance(
                array(
                    'field_id'   => $field_id,
                    'field_data' => $field_data
                )
            );
            $instance       =& $fieldsInstance;

            $showInSubmissions = $field_data["submissions_page"] == "y";
            $showInModeration  = $field_data["moderation_page"] == "y";
            if (($moderate && $showInModeration) || (!$moderate && $showInSubmissions)) {
                $possible_columns[] = $field_id;
            }

            // -------------------------------------
            //	do any fields have custom views
            //	to add?
            // -------------------------------------

            if ( ! empty($instance->entry_views))
            {
                foreach ($instance->entry_views as $e_lang => $e_method)
                {
                    $right_links[] = array(
                        'link'	=> $this->mcp_link(array(
                            'method'		=> 'field_method',
                            'field_id'		=> $field_id,
                            'field_method'	=> $e_method,
                            'form_id'		=> $form_id
                        )),
                        'title'	=> $e_lang
                    );
                }
            }
        }

        // -------------------------------------
        //	visible columns
        // -------------------------------------

        $visible_columns = $this->visible_columns($standard_columns, $possible_columns);

        $this->cached_vars['visible_columns']	= $visible_columns;
        $this->cached_vars['column_labels']		= $column_labels;
        $this->cached_vars['possible_columns']	= $possible_columns;
        $this->cached_vars['all_columns']		= $all_columns;

        // -------------------------------------
        //	prep unused from from possible
        // -------------------------------------

        //so so used
        $un_used = array();

        foreach ($possible_columns as $pcid)
        {
            $check = ($this->is_positive_intlike($pcid)) ?
                $f_prefix . $pcid :
                $pcid;

            if ( ! in_array($check, $visible_columns))
            {
                $un_used[] = $check;
            }
        }

        $this->cached_vars['unused_columns'] = $un_used;

        // -------------------------------------
        //	build query
        // -------------------------------------

        //base url for pagination
        $pag_url		= array(
            'method'	=> $method,
            'form_id'	=> $form_id
        );

        //cleans out blank keys from unset
        $find_columns	= array_merge(array(), $visible_columns);
        $must_haves		= array('entry_id');

        // -------------------------------------
        //	search criteria
        //	building query
        // -------------------------------------

        $has_search = FALSE;

        $search_vars = array(
            'search_keywords',
            'search_status',
            'search_date_range',
            'search_date_range_start',
            'search_date_range_end',
            'search_on_field'
        );

        foreach ($search_vars as $search_var)
        {
            $$search_var = ee()->input->get_post($search_var, TRUE);

            $$search_var = urldecode($$search_var);

            //set for output
            $this->cached_vars[$search_var] = (
            ($$search_var) ? trim($$search_var) : ''
            );
        }

        // -------------------------------------
        //	search keywords
        // -------------------------------------

        if ($search_keywords AND
            trim($search_keywords) !== '' AND
            $search_on_field AND
            in_array($search_on_field, $visible_columns))
        {
            $this->model('entry')->like(
                $search_on_field,
                $search_keywords
            );

            //pagination
            $pag_url['search_keywords'] = $search_keywords;
            $pag_url['search_on_field'] = $search_on_field;

            $has_search = TRUE;
        }
        //no search on field? guess we had better search it all *gulp*
        else if ($search_keywords AND trim($search_keywords) !== '')
        {
            $first = TRUE;

            $this->model('entry')->group_like(
                $search_keywords,
                array_values($visible_columns)
            );

            $pag_url['search_keywords'] = $search_keywords;

            $has_search = TRUE;
        }

        //status search?
        if ($moderate)
        {
            $this->model('entry')->where('status', 'pending');
        }
        else if ($search_status AND in_array($search_status, array_flip( $form_statuses)))
        {
            $this->model('entry')->where('status', $search_status);

            //pagination
            $pag_url['search_status'] = $search_status;

            $has_search = TRUE;
        }

        // -------------------------------------
        //	date range?
        // -------------------------------------

        //pagination
        if ($search_date_range == 'date_range')
        {


            if ($search_date_range_start !== FALSE)
            {
                $pag_url['search_date_range_start'] = $search_date_range_start;
            }

            if ($search_date_range_end !== FALSE)
            {
                $pag_url['search_date_range_end'] = $search_date_range_end;
            }

            //add timestamps so dates encompass from the beginning
            //to the end of said dates and not midnight am to midnight am
            $search_date_range_start .= ' 00:00';
            $search_date_range_end .= ' 23:59';

            //pagination
            if ($search_date_range_start OR $search_date_range_end)
            {
                $pag_url['search_date_range'] = 'date_range';
                $has_search = TRUE;
            }
        }
        else if ($search_date_range !== FALSE)
        {
            $pag_url['search_date_range'] = $search_date_range;
            $has_search = TRUE;
        }

        $this->model('entry')->date_where(
            $search_date_range,
            $search_date_range_start,
            $search_date_range_end
        );

        //we need the counts for exports and end results
        $total_entries		= $this->model('entry')->count(array(), FALSE);

        $order_by	= 'entry_date';
        $sort		= 'asc';

        $this->model('entry')->order_by($order_by, $sort);

        // -------------------------------------
        //	selects
        // -------------------------------------

        $needed_selects = array_unique(array_merge($must_haves, $find_columns));

        $this->model('entry')->select(implode(', ', $needed_selects));

        if ($total_entries > $this->row_limit)
        {
            $current_page				= $page;

            $this->model('entry')->limit(
                $this->row_limit,
                ($current_page - 1) * $this->row_limit
            );
        }

        $result_array	= $this->model('entry')->get();

        $this->formPagesCount = $this->getFormPages($total_entries, $this->row_limit);
        $this->finished = false;

        if ((int) $page == $this->formPagesCount) {
            $this->finished = true;
        }


        $fields_by_name = array_flip($field_column_names);
        $fields_by_type = $field_column_types;
        $fields_by_id = $field_column_ids;

        return [
            'submissions' => $result_array,
            'fieldsByName' => $fields_by_name,
            'fieldsByType' => $fields_by_type,
            'fieldsById' => $fields_by_id,
        ];
    }

    /**
     * get_standard_column_names
     *
     * gets the standard column names and replaces author_id with author
     *
     * @access	private
     * @return	null
     */

    private function get_standard_column_names()
    {
        $standard_columns	= array_keys(
            $this->model('form')->default_form_table_columns
        );

        array_splice(
            $standard_columns,
            array_search('author_id', $standard_columns),
            1,
            'author'
        );

        return $standard_columns;
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

    private function getFieldUpload($formId, $entryId, $fieldId, $fileName)
    {
        ee()->load->model('freeform_file_upload_model');

        return ee()->freeform_file_upload_model
            ->select('file_id')
            ->where('entry_id', $entryId)
            ->where('field_id', $fieldId)
            ->where('form_id', $formId)
            ->where('filename', $fileName)
            ->get();
    }

    //END setup_select_fields
}
