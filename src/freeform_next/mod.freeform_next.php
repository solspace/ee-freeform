<?php
/**
 * Freeform for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
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
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
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

        $transformer      = new FormTransformer();
        $forms            = FormRepository::getInstance()->getAllForms($ids, $handles);

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
            ->setOrderBy($this->getParam('orderby'))
            ->setSort($this->getParam('sort'))
            ->setLimit($limit)
            ->setOffset($this->getParam('offset'));

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
     * @return Form|null
     * @throws FreeformException
     */
    public function submitForm()
    {
        $hash   = $this->getPost(FormValueContext::FORM_HASH_KEY, null);
        $formId = FormValueContext::getFormIdFromHash($hash);

        $formModel = FormRepository::getInstance()->getFormById($formId);

        if (!$formModel) {
            return null;
        }

        $form          = $formModel->getForm();
        $isAjaxRequest = AJAX_REQUEST;
        if ($form->isValid()) {
            $submissionModel = $form->submit();

            if ($form->isFormSaved()) {
                $postedReturnUrl = $this->getPost(Form::RETURN_URI_KEY);

                $returnUrl = $postedReturnUrl ?: $form->getReturnUrl();
                $returnUrl = TemplateHelper::renderStringWithForm($returnUrl, $form, $submissionModel);
                if ($submissionModel) {
                    $returnUrl = str_replace('SUBMISSION_ID', $submissionModel->id, $returnUrl);
                }

                if ($isAjaxRequest) {
                    $this->returnJson(
                        [
                            'success'      => true,
                            'finished'     => true,
                            'returnUrl'    => $returnUrl,
                            'submissionId' => $submissionModel ? $submissionModel->id : null,
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
                        'success'  => false,
                        'finished' => false,
                        'errors'   => $fieldErrors,
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
        $id     = $this->getParam('form_id');
        $handle = $this->getParam('form');
        if (!$handle) {
            $handle = $this->getParam('form_name');
        }

        $hash = $this->getPost(FormValueContext::FORM_HASH_KEY, null);
        if (null !== $hash) {
            $this->submitForm();
        }

        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($id ? $id : $handle);
        if (!$formModel) {
            return null;
        }

        $form = $formModel->getForm();

        FormTagParamUtilities::setFormCustomAttributes($form);

        return $form;
    }
}
