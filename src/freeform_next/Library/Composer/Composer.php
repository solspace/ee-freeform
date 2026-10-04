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

namespace Solspace\Addons\FreeformNext\Library\Composer;

use stdClass;
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Context;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FormProperties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FieldHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\StatusHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\ComposerState;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;
use Solspace\Addons\FreeformNext\Services\SettingsService;

class Composer
{
    public const KEY_COMPOSER   = 'composer';
    public const KEY_FORM       = 'form';
    public const KEY_PROPERTIES = 'properties';
    public const KEY_LAYOUT     = 'layout';
    public const KEY_CONTEXT    = 'context';

    private Form $form;

    private Context $context;

    private Properties $properties;

    /**
     * Composer constructor.
     *
     * @param array                       $composerState
     * @param ComposerState|null          $customComposerState
     *
     * @throws ComposerException
     */
    public function __construct(
        private FormHandlerInterface $formHandler,
        private FieldHandlerInterface $fieldHandler,
        private SubmissionHandlerInterface $submissionHandler,
        private MailHandlerInterface $mailHandler,
        private FileUploadHandlerInterface $fileUploadHandler,
        private MailingListHandlerInterface $mailingListHandler,
        private CRMHandlerInterface $crmHandler,
        private StatusHandlerInterface $statusHandler,
        private TranslatorInterface $translator,
        private ?array $composerState = null,
        ?FormAttributes $formAttributes = null,
        private ?ComposerState $customComposerState = null
    ) {
        $this->validateComposerData($formAttributes);
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getComposerStateJSON(int $flags = 0)
    {
        $jsonObject                       = new stdClass();
        $jsonObject->composer             = new stdClass();
        $jsonObject->composer->layout     = $this->form->getLayout();
        $jsonObject->composer->properties = $this->properties;
        $jsonObject->context              = $this->context;

        return json_encode($jsonObject, $flags);
    }

    /**
     * Removes the field from layout as well as from properties
     *
     * @param int $id
     */
    public function removeFieldById($id): void
    {
        $field = $this->form->getLayout()->getFieldById($id);

        $this->form->getLayout()->removeFieldFromData($field);
        $this->properties->removeHash($field->getHash());
    }

    /**
     * Validates all components and hydrates respective objects
     *
     *
     * @throws ComposerException
     */
    private function validateComposerData(FormAttributes $formAttributes)
    {
        $composerState = $this->composerState;

        if ($this->customComposerState) {
            $this->attachCustomComposerState();

            return;
        }

        if (null === $composerState) {
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
            $this->fieldHandler,
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
     */
    private function setDefaults(): void
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
                    'handle'                => 'composer_form',
                    'submissionTitleFormat' => '{current_time format="%D, %F %d, %Y - %g:%i:%s"}',
                    'description'           => '',
                    'formTemplate'          => 'basic-light.html',
                    'returnUrl'             => '/',
                    'successBehavior'       => FormProperties::SUCCESS_BEHAVIOR_RETURN_URL,
                    'successMessage'        => FormProperties::DEFAULT_SUCCESS_MESSAGE,
                    'errorMessage'          => FormProperties::DEFAULT_ERROR_MESSAGE,
                    'storeData'             => true,
                    'useAjax'               => true,
                    'defaultStatus'         => $this->statusHandler->getDefaultStatusId(),
                ],
                Properties::INTEGRATION_HASH         => [
                    'type'          => Properties::INTEGRATION_HASH,
                    'integrationId' => 0,
                    'mapping'       => new stdClass(),
                ],
                Properties::ADMIN_NOTIFICATIONS_HASH => [
                    'format'         => 'html',
                    'type'           => Properties::ADMIN_NOTIFICATIONS_HASH,
                    'notificationId' => 0,
                    'recipients'     => '',
                ],
            ],
            $this->translator
        );

        $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();

        $formAttributes = new FormAttributes(null, $sessionImplementation, new EERequest());

        $this->context = new Context([]);
        $this->form    = new Form(
            $this->properties,
            $formAttributes,
            [[]],
            $this->formHandler,
            $this->fieldHandler,
            $this->submissionHandler,
            $this->mailHandler,
            $this->fileUploadHandler,
            $this->mailingListHandler,
            $this->crmHandler,
            $this->translator
        );
    }

    private function attachCustomComposerState(): void
    {
        $properties = [
            Properties::PAGE_PREFIX . '0'        => [
                'type'  => Properties::PAGE_PREFIX,
                'label' => 'Page 1',
            ],
            Properties::FORM_HASH                => [
                'type'                  => Properties::FORM_HASH,
                'name'                  => $this->customComposerState->name,
                'handle'                => $this->customComposerState->handle,
                'submissionTitleFormat' => '{current_time format="%D, %F %d, %Y - %g:%i:%s"}',
                'description'           => $this->customComposerState->description,
                'formTemplate'          => 'basic-light.html',
                'returnUrl'             => '/',
                'successBehavior'       => FormProperties::SUCCESS_BEHAVIOR_RETURN_URL,
                'successMessage'        => FormProperties::DEFAULT_SUCCESS_MESSAGE,
                'errorMessage'          => FormProperties::DEFAULT_ERROR_MESSAGE,
                'storeData'             => true,
                'useAjax'               => true,
                'defaultStatus'         => $this->customComposerState->defaultStatus->id,
            ],
            Properties::INTEGRATION_HASH         => [
                'type'          => Properties::INTEGRATION_HASH,
                'integrationId' => 0,
                'mapping'       => new stdClass(),
            ],
            Properties::ADMIN_NOTIFICATIONS_HASH => [
                'format'         => 'html',
                'type'           => Properties::ADMIN_NOTIFICATIONS_HASH,
                'notificationId' => $this->customComposerState->notificationId,
                'recipients'     => $this->customComposerState->notificationEmails,
            ],
        ];

        $pageCounter = 1;

        foreach ($this->customComposerState->fields as $field) {
            $properties[$field['hash']]                         = $field;
            $properties[Properties::PAGE_PREFIX . $pageCounter] = [
                'type'  => Properties::PAGE_PREFIX,
                'label' => 'Page ' . ($pageCounter + 1),
            ];

            $pageCounter++;
        }

        $this->properties = new Properties(
            $properties,
            $this->translator
        );

        $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();

        $formAttributes = new FormAttributes(null, $sessionImplementation, new EERequest());

        $this->context = new Context([]);
        $this->form    = new Form(
            $this->properties,
            $formAttributes,
            $this->customComposerState->layout,
            $this->formHandler,
            $this->fieldHandler,
            $this->submissionHandler,
            $this->mailHandler,
            $this->fileUploadHandler,
            $this->mailingListHandler,
            $this->crmHandler,
            $this->translator
        );
    }
}
