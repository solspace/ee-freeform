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
use Solspace\Addons\FreeformNext\Services\CleanupService;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\EETags\FormTagParamUtilities;
use Solspace\Addons\FreeformNext\Library\EETags\FormToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\SubmissionToTagDataTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FormTransformer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\TemplateHelper;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Model\SpamReasonModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\HoneypotService;
use Solspace\Addons\FreeformNext\Utilities\Plugin;

require_once __DIR__ . '/vendor/autoload.php';

class Freeform_Next extends Plugin implements Strict_XID
{
    public function __construct()
    {
        (new CleanupService())->runIfDue();

        $this->loadLanguageFiles();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        $form = $this->assembleFormFromTag();

        if (!$form) {
            return $this->returnNoResults();
        }

        // Allow the demo simulator (and templates using this tag) to preview a
        // formatting template without changing the form's saved configuration.
        return $form->render(null, $this->getParam('formatting_template'));
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
        $spamCounts = FormRepository::getInstance()->getFormSpamCount($formIds);

        if (empty($forms)) {
            return $this->returnNoResults();
        }

        $data = [];
        foreach ($forms as $formModel) {
            $submissionCount = $submissionCounts[$formModel->id] ?? 0;
            $spamCount = $spamCounts[$formModel->id] ?? 0;
            $data[]          = $transformer->transformForm($formModel->getForm(), $submissionCount, $spamCount);
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
            ->setOffset($this->getParam('offset'))
            ->addFilter('isSpam', false);

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
     * @return string
     */
    public function spam()
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
            ->setOffset($this->getParam('offset'))
            ->addFilter('isSpam', true);

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
     * @throws FreeformException
     */
    public function submitForm(?Form $form = null): void
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
                    $crypt = ee('Encrypt');
                    $postedReturnUrl = $crypt->decode($postedReturnUrl);
                    $postedReturnUrl = $crypt->decrypt($postedReturnUrl);
                    $returnUrl = $postedReturnUrl ?: $form->getReturnUrl();
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

                    if ($submissionModel->isSpam) {
                        $returnUrl = str_replace('submissions', 'spam', $returnUrl);
                    }

                    if ($submissionModel instanceof SubmissionModel) {
                        $this->persistSpamReasons($form, $submissionModel);
                    }

                } else {
                    $returnUrl = str_replace('SUBMISSION_ID', '', $returnUrl);
                    $returnUrl = rtrim($returnUrl, '/');
                }

                if ($isAjaxRequest) {
                    $this->returnJson(
                        [
                            'success'      => true,
                            'finished'     => true,
                            'returnUrl'    => $returnUrl,
                            'submissionId' => $submissionModel?->id,
                            'csrfToken'    => CSRF_TOKEN,
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
                        'success'   => true,
                        'finished'  => false,
                        'formHash'  => $form->getHash(),
                        'csrfToken' => CSRF_TOKEN,
                        'honeypot' => [
                            'name' => $honeypot->getName(),
                            'hash' => $honeypot->getHash(),
                        ],
                    ]
                );
            }
        } else {
            $isActSubmission = (int) ee()->input->get('ACT') > 0;

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
                        'csrfToken'  => CSRF_TOKEN,
                        'honeypot'   => [
                            'name' => $honeypot->getName(),
                            'hash' => $honeypot->getHash(),
                        ],
                    ]
                );
            }

            // Only redirect for ACT submissions
            if ($isActSubmission) {
                $payload = [
                    'formErrors'  => $form->getErrors(),
                    'fieldErrors' => [],
                    'values'      => [],
                ];

                foreach ($form->getLayout()->getFields() as $field) {
                    $handle = $field->getHandle();
                    if (!$handle) {
                        continue;
                    }

                    if ($field->hasErrors()) {
                        $payload['fieldErrors'][$handle] = $field->getErrors();
                    }

                    $payload['values'][$handle] = $field->getValue();
                }

                // Store as flashdata (1 request)
                $flashKey = 'freeform_next_errors_' . $form->getId();
                ee()->session->set_flashdata($flashKey, $payload);

                // Redirect back to the form page (NOT return_url)
                $this->redirect(ee()->input->server('HTTP_REFERER') ?: '/');
            }

            // Standard same-page postback
            return;
        }
    }

    public function persistSpamReasons(Form $form, SubmissionModel $submissionModel): void
    {
        if (!$submissionModel->isSpam || !$form->isMarkedAsSpam()) {
            return;
        }

        $spamReasons = $form->getSpamReasons();
        foreach ($spamReasons as $reason) {
            $model = SpamReasonModel::create($submissionModel->id, $reason['type'], $reason['message'], $reason['value']);
            $model->save();
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

        // Normal postback flow (no use_action_url)
        if (null !== $hash && (int) $postedId === (int) $formModel->getId()) {
            $this->submitForm($form);
        }

        // ACT flow (use_action_url="yes") to rehydrate flashed errors once.
        // Only read flash data on a clean GET request.
        // Skip on POST or AJAX, (prevents stale values from a failed submission being reapplied when the corrected form is submitted).
        $isPostRequest = strtoupper((string) ee()->input->server('REQUEST_METHOD')) === 'POST';
        $flashKey = 'freeform_next_errors_' . $form->getId();
        $payload = (!$isPostRequest && !AJAX_REQUEST)
            ? ee()->session->flashdata($flashKey)
            : null;

        if (is_array($payload)) {
            $values = $payload['values'] ?? [];
            foreach ($values as $handle => $value) {
                $field = $form->get($handle);
                if (!$field) {
                    continue;
                }

                if (method_exists($field, 'getType') && $field->getType() === 'file') {
                    continue;
                }

                if (method_exists($field, 'setValue')) {
                    $field->setValue($value);
                }
            }

            $formErrors = $payload['formErrors'] ?? [];
            if (!empty($formErrors)) {
                $form->addErrors($formErrors);
            }

            $fieldErrors = $payload['fieldErrors'] ?? [];
            foreach ($fieldErrors as $handle => $messages) {
                $field = $form->get($handle);
                if (!$field || !is_array($messages)) {
                    continue;
                }

                if (method_exists($field, 'addErrors')) {
                    $field->addErrors($messages);
                } else if (method_exists($field, 'addError')) {
                    foreach ($messages as $msg) {
                        $field->addError($msg);
                    }
                } else {
                    foreach ($messages as $msg) {
                        $form->addError($msg);
                    }
                }
            }
        }

        FormTagParamUtilities::setFormCustomAttributes($form);

        return $form;
    }

    private function findAndAttachSearchParams(Form $form, SubmissionAttributes $attributes): void
    {
        $table = ee()->db->dbprefix('freeform_next_submissions');

        foreach (ee()->TMPL->tagparams as $key => $value) {
            if (preg_match("/^search:(\w+)$/", $key, $matches)) {
                [$_, $handle] = $matches;

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

        if (str_starts_with($terms, '=')) {
            // Remove the '=' sign that specified exact match.
            $terms = substr($terms, 1);

            $search_method = '_exact_field_search';
        } else if (str_starts_with($terms, '<') ||
            str_starts_with($terms, '>')) {
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
    private function _numeric_comparison_search($terms, $col_name, $site_id): string
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
    private function _exact_field_search($terms, string $col_name, $site_id = false): string
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
        if (!str_contains($terms, 'IS_EMPTY')) {
            $no_is_empty = substr(ee()->functions->sql_andor_string(($not ? 'not ' . $terms : $terms), $col_name), 3) . ' ';

            if ($not) {
                $no_is_empty = '(' . $no_is_empty . ' OR (' . $site_id . $col_name . ' IS NULL)) ';
            }

            return $no_is_empty;
        }

        if (str_contains($terms, '|')) {
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
    private function _field_search($terms, $col_name, $site_id = false): string
    {
        $not = '';
        if (strncasecmp($terms, 'not ', 4) == 0) {
            $terms = substr($terms, 4);
            $not   = 'NOT';
        }

        if (str_contains($terms, '&&')) {
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
            } else if (str_contains($term, '\W')) // full word only, no partial matches
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
