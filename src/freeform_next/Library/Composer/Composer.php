<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Context;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\StatusHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

class Composer
{
    const KEY_COMPOSER   = 'composer';
    const KEY_FORM       = 'form';
    const KEY_PROPERTIES = 'properties';
    const KEY_LAYOUT     = 'layout';
    const KEY_CONTEXT    = 'context';

    /** @var Form */
    private $form;

    /** @var Context */
    private $context;

    /** @var Properties */
    private $properties;

    /** @var array */
    private $composerState;

    /** @var FormHandlerInterface */
    private $formHandler;

    /** @var SubmissionHandlerInterface */
    private $submissionHandler;

    /** @var MailHandlerInterface */
    private $mailHandler;

    /** @var FileUploadHandlerInterface */
    private $fileUploadHandler;

    /** @var MailingListHandlerInterface */
    private $mailingListHandler;

    /** @var CRMHandlerInterface */
    private $crmHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var StatusHandlerInterface */
    private $statusHandler;

    /**
     * Composer constructor.
     *
     * @param array                       $composerState
     * @param FormAttributes              $formAttributes
     * @param FormHandlerInterface        $formHandler
     * @param SubmissionHandlerInterface  $submissionHandler
     * @param MailHandlerInterface        $mailHandler
     * @param FileUploadHandlerInterface  $fileUploadHandler
     * @param MailingListHandlerInterface $mailingListHandler
     * @param CRMHandlerInterface         $crmHandler
     * @param StatusHandlerInterface      $statusHandler
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        array $composerState = null,
        FormAttributes $formAttributes = null,
        FormHandlerInterface $formHandler,
        SubmissionHandlerInterface $submissionHandler,
        MailHandlerInterface $mailHandler,
        FileUploadHandlerInterface $fileUploadHandler,
        MailingListHandlerInterface $mailingListHandler,
        CRMHandlerInterface $crmHandler,
        StatusHandlerInterface $statusHandler,
        TranslatorInterface $translator
    ) {
        $this->formHandler        = $formHandler;
        $this->submissionHandler  = $submissionHandler;
        $this->mailHandler        = $mailHandler;
        $this->fileUploadHandler  = $fileUploadHandler;
        $this->mailingListHandler = $mailingListHandler;
        $this->crmHandler         = $crmHandler;
        $this->statusHandler      = $statusHandler;
        $this->translator         = $translator;

        $this->composerState = $composerState;
        $this->validateComposerData($formAttributes);
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getComposerStateJSON()
    {
        $jsonObject                       = new \stdClass();
        $jsonObject->composer             = new \stdClass();
        $jsonObject->composer->layout     = $this->form->getLayout();
        $jsonObject->composer->properties = $this->properties;
        $jsonObject->context              = $this->context;

        return json_encode($jsonObject, JSON_NUMERIC_CHECK);
    }

    /**
     * Removes the field from layout as well as from properties
     *
     * @param int $id
     */
    public function removeFieldById($id)
    {
        $field = $this->form->getLayout()->getFieldById($id);

        $this->form->getLayout()->removeFieldFromData($field);
        $this->properties->removeHash($field->getHash());
    }

    /**
     * Validates all components and hydrates respective objects
     *
     * @param FormAttributes $formAttributes
     *
     * @throws ComposerException
     */
    private function validateComposerData(FormAttributes $formAttributes)
    {
        $composerState = $this->composerState;

        if (is_null($composerState)) {
            $this->setDefaults();

            return;
        }

        if (!isset($composerState[self::KEY_COMPOSER])) {
            throw new ComposerException(
                $this->translator->translate('No composer data present')
            );
        }

        $composer = $composerState[self::KEY_COMPOSER];

        if (!isset($composer[self::KEY_PROPERTIES])) {
            throw new ComposerException(
                $this->translator->translate('Composer has no properties')
            );
        }

        $this->properties = new Properties($composer['properties'], $this->translator);

        if (!isset($composer[self::KEY_LAYOUT])) {
            $composer[self::KEY_LAYOUT] = [[]];
        }

        if (!isset($composerState[self::KEY_CONTEXT])) {
            throw new ComposerException(
                $this->translator->translate('No context specified')
            );
        }

        $this->context = new Context($composerState[self::KEY_CONTEXT]);

        if (!isset($composer[self::KEY_PROPERTIES])) {
            throw new ComposerException($this->translator->translate('No properties available'));
        }

        $properties = $composer[self::KEY_PROPERTIES];

        if (!isset($properties[self::KEY_FORM])) {
            throw new ComposerException($this->translator->translate('No form settings specified'));
        }

        $this->form = new Form(
            $this->properties,
            $formAttributes,
            $composer[self::KEY_LAYOUT],
            $this->formHandler,
            $this->submissionHandler,
            $this->mailHandler,
            $this->fileUploadHandler,
            $this->mailingListHandler,
            $this->crmHandler,
            $this->translator
        );
    }

    /**
     * This method sets defaults for all composer items
     * It happens if a new Form Model is created
     * @throws \Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException
     */
    private function setDefaults()
    {
        $this->properties = new Properties(
            [
                Properties::PAGE_PREFIX . '0'        => [
                    'type'  => Properties::PAGE_PREFIX,
                    'label' => 'Page 1',
                ],
                Properties::FORM_HASH                => [
                    'type'                  => Properties::FORM_HASH,
                    'name'                  => 'Composer Form',
                    'handle'                => 'composerForm',
                    'submissionTitleFormat' => '{current_time format="%D, %F %d, %Y - %g:%i:%s"}',
                    'description'           => '',
                    'formTemplate'          => 'flexbox.html',
                    'returnUrl'             => '/',
                    'storeData'             => true,
                    'defaultStatus'         => $this->statusHandler->getDefaultStatusId(),
                ],
                Properties::INTEGRATION_HASH         => [
                    'type'          => Properties::INTEGRATION_HASH,
                    'integrationId' => 0,
                    'mapping'       => new \stdClass(),
                ],
                Properties::ADMIN_NOTIFICATIONS_HASH => [
                    'type'           => Properties::ADMIN_NOTIFICATIONS_HASH,
                    'notificationId' => 0,
                    'recipients'     => '',
                ],
            ],
            $this->translator
        );

        $formAttributes = new FormAttributes(null, new EESession(), new EERequest());

        $this->context = new Context([]);
        $this->form    = new Form(
            $this->properties,
            $formAttributes,
            [[]],
            $this->formHandler,
            $this->submissionHandler,
            $this->mailHandler,
            $this->fileUploadHandler,
            $this->mailingListHandler,
            $this->crmHandler,
            $this->translator
        );
    }
}
