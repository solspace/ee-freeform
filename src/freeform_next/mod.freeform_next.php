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
    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $idOrHandle = $this->getParam('form');
        $form       = FormRepository::getInstance()->getFormByIdOrHandle($idOrHandle);

        if (!$form) {
            throw new FreeformException('Form not found');
        }

        $form = $form->getComposer()->getForm();

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/Templates/form');
        $twig = new Twig_Environment($loader);

        $test = ee()->functions->fetch_action_id('Freeform_next', 'submitForm');

        return $twig->render("test.html", ['form' => $form]);

        return "Form: {$form->getName()}";
    }

    /**
     * @return mixed
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Loader
     * @throws \Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException
     */
    public function submitForm()
    {
        $hash   = $this->getPost(FormValueContext::FORM_HASH_KEY, null);
        $formId = FormValueContext::getFormIdFromHash($hash);

        $formModel = FormRepository::getInstance()->getFormById($formId);

        if (!$formModel) {
            throw new FreeformException(lang('Form with ID {id} not found', ['id' => $formId]));
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

    /**
     * @return FormsService
     */
    private function getFormService()
    {
        static $service;

        if (null === $service) {
            $service = new FormsService();
        }

        return $service;
    }
}
