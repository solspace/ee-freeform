<?php
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\TwigHelper;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Utilities\Plugin;

/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */
class Freeform_Next extends Plugin
{
    public function __construct()
    {
        $fileService = new \Solspace\Addons\FreeformNext\Services\FilesService();
        $fileService->cleanUpUnfinalizedAssets();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $handle = $this->getParam('form');
        $id     = $this->getParam('form_id');

        $hash       = $this->getPost(FormValueContext::FORM_HASH_KEY, null);
        $form       = null;
        if (null !== $hash) {
            $this->submitForm();
        }

        $formModel = FormRepository::getInstance()->getFormByIdOrHandle($id ? $id : $handle);
        $form = $formModel->getForm();

        if (!$form) {
            throw new FreeformException('Form not found');
        }

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/Templates/form');
        $twig   = new Twig_Environment($loader);

        return $twig->render('test.html', ['form' => $form]);

        return "Form: {$form->getName()}";
    }

    /**
     * @return Form|null
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Loader
     * @throws FreeformException
     */
    private function submitForm()
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
                $returnUrl = TwigHelper::renderString(
                    $returnUrl,
                    [
                        'form'       => $form,
                        'submission' => $submissionModel,
                    ]
                );

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
}
