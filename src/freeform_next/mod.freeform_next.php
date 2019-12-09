<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\EETags\FormTagParamUtilities;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\SubmissionToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FormTransformer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\TemplateHelper;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\HoneypotService;
use Solspace\Addons\FreeformNext\Utilities\Plugin;

class Freeform_Next extends Plugin
{
    public function __construct()
    {
        // TODO: Prevent this from firing all the time
        $fileService = new \Solspace\Addons\FreeformNext\Services\FilesService();
        $fileService->cleanUpUnfinalizedAssets();

        $settingsService = new \Solspace\Addons\FreeformNext\Services\SettingsService();
        $settingsService->cleanUpDatabaseSessionData();

        $this->loadLanguageFiles();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $form = $this->assembleFormFromTag();

        if (!$form) {
            return $this->returnNoResults();
        }

        return $form->render();
    }

    /**
     * @return mixed
     */
    public function form()
    {
        $form = $this->assembleFormFromTag();

        if (!$form) {
            return $this->returnNoResults();
        }

        $tagdata     = ee()->TMPL->tagdata;
        $transformer = new FormToTagDataTransformer($form, $tagdata);

        $renderTags = !$this->getParam('no_form_tags', false);

        return $renderTags ? $transformer->getOutput() : $transformer->getOutputWithoutWrappingFormTags();
    }

    /**
     * @return mixed
     */
    public function forms()
    {
        $ids     = $this->getParam('form_id');
        $handles = $this->getParam('form');
        if (!$handles) {
            $handles = $this->getParam('form_name');
        }

        $transformer = new FormTransformer();
        $forms       = FormRepository::getInstance()->getAllForms($ids, $handles);

        $formIds = [];
        foreach ($forms as $form) {
            $formIds[] = $form->id;
        }

        $submissionCounts = FormRepository::getInstance()->getFormSubmissionCount($formIds);

        if (empty($forms)) {
            return $this->returnNoResults();
        }

        $data = [];
        foreach ($forms as $formModel) {
            $submissionCount = isset($submissionCounts[$formModel->id]) ? $submissionCounts[$formModel->id] : 0;
            $data[]          = $transformer->transformForm($formModel->getForm(), $submissionCount);
        }

        $output = ee()->TMPL->tagdata;
        $output = ee()->TMPL->parse_variables($output, $data);

        return $output;
    }

    /**
     * @return string
     */
    public function submissions()
    {
        ee()->load->library('pagination');
        $form = $this->assembleFormFromTag();

        if (!$form) {
            return $this->returnNoResults();
        }

        $limit          = $this->getParam('limit');
        $shouldPaginate = (bool) $this->getParam('paginate') && (bool) $limit;

        $attributes = new SubmissionAttributes($form);
        $attributes
            ->setStatus($this->getParam('status'))
            ->setDateRangeStart($this->getParam('date_range_start'))
            ->setDateRangeEnd($this->getParam('date_range_end'))
            ->setDateRange($this->getParam('date_range'))
            ->setSubmissionId($this->getParam('submission_id'))
            ->setToken($this->getParam('token'))
            ->setOrderBy($this->getParam('orderby'))
            ->setSort($this->getParam('sort'))
            ->setLimit($limit)
            ->setOffset($this->getParam('offset'));

        $this->findAndAttachSearchParams($form, $attributes);

        $total = SubmissionRepository::getInstance()->getAllSubmissionCountFor($attributes);

        /** @var \Pagination_object $pagination */
        $pagination = ee()->pagination->create();

        $search  = [LD . 'submission:switch', LD . 'submission:paginate', LD . '/submission:paginate'];
        $replace = [LD . 'switch', LD . 'paginate', LD . '/paginate'];

        $output = str_replace($search, $replace, ee()->TMPL->tagdata);
        $output = $pagination->prepare($output);

        if ($shouldPaginate) {
            $pagination->prefix = 'P';
            $pagination->build($total, (int) $limit);

            $attributes->setOffset($pagination->offset);
        }

        $submissions = SubmissionRepository::getInstance()->getAllSubmissionsFor($attributes);

        if (empty($submissions)) {
            return $this->returnNoResults();
        }

        $transformer = new SubmissionToTagDataTransformer($form, $output, $submissions);
        $output      = $transformer->getOutput($attributes);

        return $pagination->render($output);
    }

    /**
     * @param Form $form
     *
     * @throws FreeformException
     */
    public function submitForm(Form $form = null)
    {
        if (null === $form) {
            $hash = $this->getPost(FormValueContext::FORM_HASH_KEY, null);

            if (null !== $hash) {
                $postedId  = FormValueContext::getFormIdFromHash($hash);
                $formModel = FormRepository::getInstance()->getFormByIdOrHandle($postedId);
                if ($formModel) {
                    $form = $formModel->getForm();
                }
            }
        }

        if (!$form) {
            return;
        }

        $honeypotService = new HoneypotService();
        $isAjaxRequest   = AJAX_REQUEST;

        $honeypot = $honeypotService->getHoneypot($form);

        if ($form->isValid()) {
            /** @var SubmissionModel $submissionModel */
            $submissionModel = $form->submit();

            if ($form->isFormSaved()) {
                $postedReturnUrl = $this->getPost(Form::RETURN_URI_KEY);
                if ($postedReturnUrl) {
                    $postedReturnUrl = ee('Encrypt')->decrypt($postedReturnUrl);
                    $returnUrl = $postedReturnUrl ? $postedReturnUrl : $form->getReturnUrl();
                } else {
                    $returnUrl = $form->getReturnUrl();
                }

                $returnUrl = TemplateHelper::renderStringWithForm($returnUrl, $form, $submissionModel);
                if ($submissionModel) {
                    $returnUrl = str_replace(
                        ['SUBMISSION_ID', 'SUBMISSION_TOKEN'],
                        [$submissionModel->id, $submissionModel->token],
                        $returnUrl
                    );
                }

                if ($isAjaxRequest) {
                    $this->returnJson(
                        [
                            'success'      => true,
                            'finished'     => true,
                            'returnUrl'    => $returnUrl,
                            'submissionId' => $submissionModel ? $submissionModel->id : null,
                            'honeypot'     => [
                                'name' => $honeypot->getName(),
                                'hash' => $honeypot->getHash(),
                            ],
                        ]
                    );
                } else {
                    $this->redirect($returnUrl);
                }
            } else if ($isAjaxRequest) {
                $this->returnJson(
                    [
                        'success'  => true,
                        'finished' => false,
                        'honeypot' => [
                            'name' => $honeypot->getName(),
                            'hash' => $honeypot->getHash(),
                        ],
                    ]
                );
            }
        } else {
            if ($isAjaxRequest) {
                $fieldErrors = [];

                foreach ($form->getLayout()->getFields() as $field) {
                    if ($field->hasErrors()) {
                        $fieldErrors[$field->getHandle()] = $field->getErrors();
                    }
                }

                $this->returnJson(
                    [
                        'success'    => false,
                        'finished'   => false,
                        'formErrors' => $form->getErrors(),
                        'errors'     => $fieldErrors,
                        'honeypot'   => [
                            'name' => $honeypot->getName(),
                            'hash' => $honeypot->getHash(),
                        ],
                    ]
                );
            }
        }
    }

    /**
     * @return Form|null
     */
    private function assembleFormFromTag()
    {
        $id       = $this->getParam('form_id');
        $handle   = $this->getParam('form');
        $postedId = null;

        if (!$handle) {
            $handle = $this->getParam('form_name');
        }

        $hash = $this->getPost(FormValueContext::FORM_HASH_KEY, null);
        if (null !== $hash) {
            $postedId = FormValueContext::getFormIdFromHash($hash);
        }

        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($id ?: $handle);
        if (!$formModel) {
            return null;
        }

        $form = $formModel->getForm();
        if (null !== $hash && (int) $postedId === (int) $formModel->getId()) {
            $this->submitForm($form);
        }

        FormTagParamUtilities::setFormCustomAttributes($form);

        return $form;
    }

    /**
     * @param Form                 $form
     * @param SubmissionAttributes $attributes
     */
    private function findAndAttachSearchParams(Form $form, SubmissionAttributes $attributes)
    {
        $table = ee()->db->dbprefix('freeform_next_submissions');

        foreach (ee()->TMPL->tagparams as $key => $value) {
            if (preg_match("/^search:(\w+)$/", $key, $matches)) {
                list ($_, $handle) = $matches;

                $field = $form->get($handle);
                if (!$field) {
                    continue;
                }

                $column = SubmissionModel::getFieldColumnName($field->getId());
                $sql    = $this->field_search_sql($value, "`$table`.`$column`");

                $attributes->addWhere($sql);
            }
        }
    }

    /**
     * Generates SQL for a field search
     *
     * @param string    Search terms from search parameter
     * @param string    Database column name to search
     * @param int        Site ID
     *
     * @return    string    SQL to include in an existing query's WHERE clause
     */
    public function field_search_sql($terms, $col_name, $site_id = false)
    {
        $search_method = '_field_search';

        if (strncmp($terms, '=', 1) == 0) {
            // Remove the '=' sign that specified exact match.
            $terms = substr($terms, 1);

            $search_method = '_exact_field_search';
        } else if (strncmp($terms, '<', 1) == 0 ||
            strncmp($terms, '>', 1) == 0) {
            $search_method = '_numeric_comparison_search';
        }

        return $this->$search_method($terms, $col_name, $site_id);
    }

    /**
     * Generate the SQL for a numeric comparison search
     * <, >, <=, >= operators
     *
     * search:field='>=20'
     * search:field='>3|<5'
     */
    private function _numeric_comparison_search($terms, $col_name, $site_id)
    {
        preg_match_all('/([<>]=?)(\d+)/', $terms, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $this->_field_search($terms, $col_name, $site_id);
        }

        $terms = [];

        foreach ($matches as $match) {
            // col_name >= 20
            $terms[] = "{$col_name} {$match[1]} {$match[2]}";
        }

        $site_id = ($site_id !== false) ? "( wd.site_id = {$site_id} AND " : '(';

        return $site_id . implode(' AND ', $terms) . ')';
    }

    /**
     * Generate the SQL for an exact query in field search.
     *
     * search:field="=words|other words"
     */
    private function _exact_field_search($terms, $col_name, $site_id = false)
    {
        // Did this because I don't like repeatedly checking
        // the beginning of the string with strncmp for that
        // 'not', much prefer to do it once and then set a
        // boolean.  But.. [cont:1]
        $not     = false;
        $site_id = ($site_id !== false) ? 'wd.site_id=' . $site_id . ' AND ' : '';

        if (strncasecmp($terms, 'not ', 4) == 0) {
            $not   = true;
            $terms = substr($terms, 4);
        }

        // Trivial case, we don't have special IS_EMPTY handling.
        if (strpos($terms, 'IS_EMPTY') === false) {
            $no_is_empty = substr(ee()->functions->sql_andor_string(($not ? 'not ' . $terms : $terms), $col_name), 3) . ' ';

            if ($not) {
                $no_is_empty = '(' . $no_is_empty . ' OR (' . $site_id . $col_name . ' IS NULL)) ';
            }

            return $no_is_empty;
        }

        if (strpos($terms, '|') !== false) {
            $terms = str_replace('IS_EMPTY|', '', $terms);
        } else {
            $terms = str_replace('IS_EMPTY', '', $terms);
        }

        $add_search = '';
        $conj       = '';

        // If we have search terms, then we need to build the search.
        if (!empty($terms)) {
            // [cont:1]...it makes this a little hacky.  Gonna leave it for the moment,
            // but may come back to it.
            $add_search = ee()->functions->sql_andor_string(($not ? 'not ' . $terms : $terms), $col_name);
            // remove the first AND output by ee()->functions->sql_andor_string() so we can parenthesize this clause
            $add_search = '(' . $site_id . substr($add_search, 3) . ')';

            $conj = ($add_search != '' && !$not) ? 'OR' : 'AND';
        }

        // If we reach here, we have an IS_EMPTY in addition to possible search terms.
        // Add the empty check condition.
        if ($not) {
            return $add_search . ' ' . $conj . ' ((' . $site_id . $col_name . ' != "") AND (' . $site_id . $col_name . ' IS NOT NULL))';
        }

        return $add_search . ' ' . $conj . ' ((' . $site_id . $col_name . ' = "") OR (' . $site_id . $col_name . ' IS NULL))';
    }

    /**
     * Generate the SQL for a LIKE query in field search.
     *
     *        search:field="words|other words|IS_EMPTY"
     */
    private function _field_search($terms, $col_name, $site_id = false)
    {
        $not = '';
        if (strncasecmp($terms, 'not ', 4) == 0) {
            $terms = substr($terms, 4);
            $not   = 'NOT';
        }

        if (strpos($terms, '&&') !== false) {
            $terms = explode('&&', $terms);
            $andor = $not == 'NOT' ? 'OR' : 'AND';
        } else {
            $terms = explode('|', $terms);
            $andor = $not == 'NOT' ? 'AND' : 'OR';
        }

        $site_id = ($site_id !== false) ? 'wd.site_id=' . $site_id . ' AND ' : '';

        $search_sql = '';
        $col_name   = $site_id . $col_name;
        $empty      = false;
        foreach ($terms as $term) {
            if ($search_sql !== '') {
                $search_sql .= $andor;
            }
            if ($term == 'IS_EMPTY') {
                $empty = true;
                // Empty string
                $search_sql .= ' (' . $col_name . ($not ? '!' : '') . '=""';
                // IS (NOT) NULL
                $search_sql .= $not ? ' AND ' : ' OR ';
                $search_sql .= $col_name . ' IS ' . ($not ?: '') . ' NULL) ';
            } else if (strpos($term, '\W') !== false) // full word only, no partial matches
            {
                // Note: MySQL's nutty POSIX regex word boundary is [[:>:]]
                $term = '([[:<:]]|^)' . preg_quote(str_replace('\W', '', $term)) . '([[:>:]]|$)';

                $search_sql .= ' (' . $col_name . ' ' . $not . ' REGEXP "' . ee()->db->escape_str($term) . '") ';
            } else {
                $search_sql .= ' (' . $col_name . ' ' . $not . ' LIKE "%' . ee()->db->escape_like_str($term) . '%") ';
            }
        }

        if ($not && !$empty) {

            $search_sql = '(' . $search_sql . ') OR (' . $col_name . ' IS NULL) ';
        }

        return $search_sql;
    }
}
