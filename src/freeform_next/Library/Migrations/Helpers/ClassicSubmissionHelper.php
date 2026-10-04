<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use stdClass;
use Solspace\Addons\Freeform\Library\AddonBuilder;
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

    public function getNextForm(mixed $formId = null)
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

    public function getFormsCount(): int
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
    private function getSubmissions($formId, $page): array
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
     * Format CP date
     *
     * @access	public
     * @param	mixed	$date	unix time
     * @return	string			unit time formatted to cp date formatting pref
     */

    public function format_cp_date(mixed $date)
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

    protected function setup_select_fields($available_fields, $order = []): string
    {
        //---------------------------------------------
        //  Dependencies
        //---------------------------------------------

        $multiple	= true;
        $channels	= [];
        $field_name = 'form_fields';
        $settings   = '';
        $selected	= $order;
        //make order array keys with blank entries
        //because the related field is weird like that
        $related	= count($order) ?
            array_combine($order, array_fill(0, count($order), '')) :
            [];
        $entries	= [];

        sort($selected);

        ee()->cp->add_js_script(['plugin'	=> 'ee_interact.event', 'file'		=> 'fields/relationship/cp', 'ui'		=> 'sortable']);

        // -------------------------------------
        //	fields ('entries')
        // -------------------------------------

        if ( ! empty($available_fields))
        {
            foreach ($available_fields as $field_id => $field_label)
            {
                $new					= new stdClass();
                $channel				= new stdClass();
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
            [
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
            ],
            [lang('available_fields'), lang('available_fields'), lang('selected_fields'), '', '', '', '', lang('no_fields'), lang('no_fields_chosen'), '', '<div class="filters" style="display:none">', 'class="relate-actions" style="display:none"', lang('fields')],
            $field_view
        );

        //---------------------------------------------
        //  Return
        //---------------------------------------------

        //this has to be wrapped so the JS works
        return '<div class="publish">' . $field_view . '</div>';
    }

    private function getFormPages(int $total_entries, $rowLimit): float
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
