<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FormProperties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FieldHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FileUploadException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;

class Form implements \JsonSerializable, \Iterator, \ArrayAccess
{
    const SUBMISSION_FLASH_KEY = 'freeform_submission_flash';

    const PAGE_INDEX_KEY     = 'page_index';
    const RETURN_URI_KEY     = 'formReturnUrl';
    const DEFAULT_PAGE_INDEX = 0;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $handle;

    /** @var string */
    private $color;

    /** @var string */
    private $submissionTitleFormat;

    /** @var string */
    private $description;

    /** @var string */
    private $returnUrl;

    /** @var bool */
    private $storeData;

    /** @var bool */
    private $ipCollectingEnabled;

    /** @var int */
    private $defaultStatus;

    /** @var string */
    private $formTemplate;

    /** @var Layout */
    private $layout;

    /** @var Row[] */
    private $currentPageRows;

    /** @var string */
    private $optInDataStorageTargetHash;

    /** @var FormAttributes */
    private $formAttributes;

    /** @var Properties */
    private $properties;

    /** @var string[] */
    private $errors;

    /** @var bool */
    private $formSaved;

    /** @var bool */
    private $valid;

    /** @var bool */
    private $markedAsSpam;

    /** @var SubmissionHandlerInterface */
    private $submissionHandler;

    /** @var FormHandlerInterface */
    private $formHandler;

    /** @var MailHandlerInterface */
    private $mailHandler;

    /** @var FileUploadHandlerInterface */
    private $fileUploadHandler;

    /** @var FieldHandlerInterface */
    private $fieldHandler;

    /** @var MailingListHandlerInterface */
    private $mailingListHandler;

    /** @var CRMHandlerInterface */
    private $crmHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var CustomFormAttributes */
    private $customAttributes;

    /** @var int */
    private $cachedPageIndex;

    /** @var bool */
    private $submitted;

    /** @var mixed */
    private $submitResult;

    /**
     * Form constructor.
     *
     * @param Properties                  $properties
     * @param FormAttributes              $formAttributes
     * @param array                       $layoutData
     * @param FormHandlerInterface        $formHandler
     * @param FieldHandlerInterface       $fieldHandler
     * @param SubmissionHandlerInterface  $submissionHandler
     * @param MailHandlerInterface        $mailHandler
     * @param FileUploadHandlerInterface  $fileUploadHandler
     * @param MailingListHandlerInterface $mailingListHandler
     * @param CRMHandlerInterface         $crmHandler
     * @param TranslatorInterface         $translator
     *
     * @throws FreeformException
     */
    public function __construct(
        Properties $properties,
        FormAttributes $formAttributes,
        array $layoutData,
        FormHandlerInterface $formHandler,
        FieldHandlerInterface $fieldHandler,
        SubmissionHandlerInterface $submissionHandler,
        MailHandlerInterface $mailHandler,
        FileUploadHandlerInterface $fileUploadHandler,
        MailingListHandlerInterface $mailingListHandler,
        CRMHandlerInterface $crmHandler,
        TranslatorInterface $translator
    ) {
        $this->properties          = $properties;
        $this->formHandler         = $formHandler;
        $this->fieldHandler        = $fieldHandler;
        $this->submissionHandler   = $submissionHandler;
        $this->mailHandler         = $mailHandler;
        $this->fileUploadHandler   = $fileUploadHandler;
        $this->mailingListHandler  = $mailingListHandler;
        $this->crmHandler          = $crmHandler;
        $this->translator          = $translator;
        $this->storeData           = true;
        $this->ipCollectingEnabled = true;
        $this->storeData           = true;
        $this->customAttributes    = new CustomFormAttributes();
        $this->errors              = [];
        $this->markedAsSpam        = false;
        $this->submitted           = false;

        $this->layout = new Layout(
            $this,
            $layoutData,
            $properties,
            $formAttributes->getFormValueContext(),
            $translator
        );
        $this->buildFromData($properties->getFormProperties());

        $this->id             = $formAttributes->getId();
        $this->formAttributes = $formAttributes;

        $this->getCurrentPage();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param string $fieldHandle
     *
     * @return null|AbstractField
     */
    public function get($fieldHandle)
    {
        try {
            return $this->getLayout()->getFieldByHandle($fieldHandle);
        } catch (FreeformException $e) {
            try {
                return $this->getLayout()->getFieldByHash($fieldHandle);
            } catch (FreeformException $e) {
                return null;
            }
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return string|null
     */
    public function getOptInDataStorageTargetHash()
    {
        return $this->optInDataStorageTargetHash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->getFormValueContext()->getLastHash();
    }

    /**
     * @return string
     */
    public function getSubmissionTitleFormat()
    {
        return $this->submissionTitleFormat;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Page
     */
    public function getCurrentPage()
    {
        static $page;

        $index = $this->getFormValueContext()->getCurrentPageIndex();

        if (null === $page || $this->cachedPageIndex !== $index) {
            if (!isset($this->layout->getPages()[$index])) {
                throw new FreeformException(
                    $this->getTranslator()->translate(
                        "The provided page index '{pageIndex}' does not exist in form '{formName}'",
                        ['pageIndex' => $index, 'formName' => $this->getName()]
                    )
                );
            }

            $page = $this->layout->getPages()[$index];

            $this->currentPageRows = $page->getRows();
            $this->cachedPageIndex = $index;
        }

        return $page;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl ?: '';
    }

    /**
     * @return string
     */
    public function getAnchor()
    {
        $hash = $this->getHash();
        $id   = substr(sha1($this->getId() . $this->getHandle()), 0, 6);

        return "$id-form-$hash";
    }

    /**
     * @return int
     */
    public function getDefaultStatus()
    {
        return $this->defaultStatus;
    }

    /**
     * @return int
     */
    public function isIpCollectingEnabled()
    {
        return (bool) $this->ipCollectingEnabled;
    }

    /**
     * @return bool
     */
    public function isFormSaved()
    {
        return (bool) $this->formSaved;
    }

    /**
     * @return Page[]
     */
    public function getPages()
    {
        return $this->layout->getPages();
    }

    /**
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $message
     *
     * @return Form
     */
    public function addError($message)
    {
        $this->errors[] = $message;

        return $this;
    }

    /**
     * @param array $messages
     *
     * @return Form
     */
    public function addErrors(array $messages)
    {
        $this->errors = array_merge($this->errors, $messages);

        return $this;
    }

    /**
     * @return bool
     */
    public function isMarkedAsSpam()
    {
        return $this->markedAsSpam;
    }

    /**
     * @param bool $markedAsSpam
     *
     * @return Form
     */
    public function setMarkedAsSpam($markedAsSpam)
    {
        $this->markedAsSpam = $markedAsSpam;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (null !== $this->valid) {
            return $this->valid;
        }

        if ($this->getFormValueContext()->shouldFormWalkToPreviousPage()) {
            $this->valid = true;

            return $this->valid;
        }

        if (!$this->isPagePosted()) {
            $this->valid = false;

            return $this->valid;
        }

        $currentPageFields = $this->getCurrentPage()->getFields();

        $this->formHandler->onFormValidate($this);

        $isFormValid = true;
        foreach ($this->getCurrentPage()->getFields() as $field) {
            if (!$field->isValid()) {
                $isFormValid = false;
            }
        }


        if ($isFormValid && $this->isMarkedAsSpam()) {
            $simulateSuccess = $this->formHandler->isSpamBehaviourSimulateSuccess();

            if ($simulateSuccess && $this->isLastPage()) {
                $this->formHandler->incrementSpamBlockCount($this);
            } else if (!$simulateSuccess) {
                $this->formHandler->incrementSpamBlockCount($this);
            }

            $this->valid = $simulateSuccess;

            return $this->valid;
        }

        if ($this->errors) {
            $isFormValid = false;
        }

        foreach ($currentPageFields as $field) {
            if ($field instanceof FileUploadInterface) {
                try {
                    $field->uploadFile();
                } catch (FileUploadException $e) {
                    $isFormValid = false;
                }

                if ($field->hasErrors()) {
                    $isFormValid = false;
                }
            }
        }

        $this->valid = $isFormValid;

        return $this->valid;
    }

    /**
     * @return bool
     */
    public function isPagePosted()
    {
        return $this->getFormValueContext()->hasPageBeenPosted();
    }

    /**
     * @return bool
     */
    public function isFormPosted()
    {
        return $this->getFormValueContext()->hasFormBeenPosted();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return ($this->isPagePosted() && !$this->isValid()) || count($this->getErrors()) != 0;
    }

    /**
     * @return bool
     */
    public function isSubmissionTitleFormatBlank()
    {
        $format = $this->getSubmissionTitleFormat();

        if (empty($format) || ctype_space($this->getSubmissionTitleFormat())) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSubmittedSuccessfully()
    {
        return $this->getSubmissionHandler()->wasFormFlashSubmitted($this);
    }

    /**
     * Submit and store the form values in either session or database
     * depending on the current form page
     *
     * @return bool - saved or not saved
     * @throws FreeformException
     */
    public function submit()
    {
        if ($this->submitted) {
            return $this->submitResult;
        }

        $this->submitted = true;

        $formValueContext = $this->getFormValueContext();
        $onBeforeSubmit   = ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_SUBMIT, $this);

        if ($this->isMarkedAsSpam()) {
            $this->formSaved = true;

            return null;
        }

        if ($formValueContext->shouldFormWalkToPreviousPage()) {
            $this->retreatFormToPreviousPage();
            $this->submitResult = false;

            return false;
        }

        $submittedValues = $this->getCurrentPage()->getStorableFieldValues();
        $formValueContext->appendStoredValues($submittedValues);

        if (!$this->isLastPage()) {
            $this->advanceFormToNextPage();
            $this->submitResult = false;

            return false;
        }

        if (!$onBeforeSubmit) {
            $this->submitResult = false;

            return false;
        }

        if ($this->storeData) {
            $submission = $this->saveStoredStateToDatabase();
        } else {
            $submission      = null;
            $this->formSaved = true;
        }
        $this->getSubmissionHandler()->markFormAsSubmitted($this);
        $this->sendOutEmailNotifications($submission);
        $this->pushToMailingLists();
        $this->pushToCRM();

        $formValueContext->cleanOutCurrentSession();

        ExtensionHelper::call(ExtensionHelper::HOOK_FORM_AFTER_SUBMIT, $this, $submission);
        $this->submitResult = $submission;

        return $submission;
    }

    /**
     * Render a predefined template
     *
     * @param array $customFormAttributes
     *
     * @return string
     * @throws FreeformException
     */
    public function render(array $customFormAttributes = null)
    {
        $this->setAttributes($customFormAttributes);

        return $this->formHandler->renderFormTemplate($this, $this->formTemplate);
    }

    /**
     * @param array $customFormAttributes
     *
     * @return string
     * @throws FreeformException
     */
    public function renderTag(array $customFormAttributes = null)
    {
        $this->setAttributes($customFormAttributes);

        $customAttributes = $this->getCustomAttributes();

        $encTypeAttribute = count($this->getLayout()->getFileUploadFields()) ? ' enctype="multipart/form-data"' : '';

        $idAttribute = $customAttributes->getId();
        $idAttribute = $idAttribute ? ' id="' . $idAttribute . '"' : '';

        $nameAttribute = $customAttributes->getName();
        $nameAttribute = $nameAttribute ? ' name="' . $nameAttribute . '"' : '';

        $methodAttribute = $customAttributes->getMethod() ?: $this->formAttributes->getMethod();
        $methodAttribute = $methodAttribute ? ' method="' . $methodAttribute . '"' : '';

        $classAttribute = $customAttributes->getClass();
        $classAttribute = $classAttribute ? ' class="' . $classAttribute . '"' : '';

        $actionAttribute = $customAttributes->getAction();
        if ($actionAttribute) {
            $actionAttribute = ' action="' . $actionAttribute . '"';
        } else if ($customAttributes->isUseActionUrl()) {
            $submitUrl = $this->formHandler->getSubmitUrl();
            if ($submitUrl) {
                $actionAttribute = ' action="' . $submitUrl . '"';
            }
        }

        $output = sprintf(
                '<form %s%s%s%s%s%s%s>',
                $idAttribute,
                $nameAttribute,
                $methodAttribute,
                $encTypeAttribute,
                $classAttribute,
                $actionAttribute,
                $customAttributes->getFormAttributesAsString()
            ) . PHP_EOL;

        if ($customAttributes->getReturnUrl()) {
            $output .= '<input type="hidden" '
                . 'name="' . self::RETURN_URI_KEY . '" '
                . 'value="' . $customAttributes->getReturnUrl() . '" '
                . '/>';
        }

        $output .= '<input '
            . 'type="hidden" '
            . 'name="csrf_token" '
            . 'value="' . CSRF_TOKEN . '" '
            . '/>';

        $output .= '<input '
            . 'type="hidden" '
            . 'name="' . FormValueContext::FORM_HASH_KEY . '" '
            . 'value="' . $this->getHash() . '" '
            . '/>';

        if ($this->formAttributes->isCsrfEnabled()) {
            $csrfTokenName = $this->formAttributes->getCsrfTokenName();
            $csrfToken     = $this->formAttributes->getCsrfToken();

            $output .= '<input type="hidden" name="' . $csrfTokenName . '" value="' . $csrfToken . '" />';
        }

        $hiddenFields = $this->layout->getHiddenFields();
        foreach ($hiddenFields as $field) {
            if ($field->getPageIndex() === $this->getCurrentPage()->getIndex()) {
                $output .= $field->renderInput();
            }
        }

        $output .= '<a id="' . $this->getAnchor() . '"></a>';
        $output .= $this->formHandler->onRenderOpeningTag($this);

        return $output;
    }

    /**
     * @return string
     */
    public function renderClosingTag()
    {
        $output = $this->formHandler->onRenderClosingTag($this);
        $output .= '</form>';

        return $output;
    }

    /**
     * @return FieldHandlerInterface
     */
    public function getFieldHandler()
    {
        return $this->fieldHandler;
    }

    /**
     * @return SubmissionHandlerInterface
     */
    public function getSubmissionHandler()
    {
        return $this->submissionHandler;
    }

    /**
     * @return MailHandlerInterface
     */
    public function getMailHandler()
    {
        return $this->mailHandler;
    }

    /**
     * @return FileUploadHandlerInterface
     */
    public function getFileUploadHandler()
    {
        return $this->fileUploadHandler;
    }

    /**
     * @return MailingListHandlerInterface
     */
    public function getMailingListHandler()
    {
        return $this->mailingListHandler;
    }

    /**
     * @return CustomFormAttributes
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @return null|string
     */
    public function getFieldPrefix()
    {
        if (null === $this->getFormValueContext()) {
            return $this->getCustomAttributes()->getFieldPrefix();
        }

        return $this->getFormValueContext()->getFieldPrefix();
    }

    /**
     * @param array|null $attributes
     *
     * @return $this
     * @throws FreeformException
     */
    public function setAttributes(array $attributes = null)
    {
        if (null !== $attributes) {
            $this->customAttributes->mergeAttributes($attributes);
            $this->setSessionCustomFormData();
            $this->populateFromSubmission($this->customAttributes->getSubmissionToken());
        }

        return $this;
    }

    /**
     * @param SubmissionModel|int|string|null $token
     *
     * @return Form
     */
    public function populateFromSubmission($token = null)
    {
        if (null === $token || FreeformHelper::get('version') !== FREEFORM_PRO) {
            return $this;
        }

        $submission = SubmissionRepository::getInstance()->getSubmissionByToken($this, $token);
        if ($submission instanceof SubmissionModel) {
            foreach ($this->getLayout()->getFields() as $field) {
                try {
                    if ($submission->getFieldValue($field->getHandle())) {
                        $field->setValue($submission->{$field->getHandle()});
                    }
                } catch (FreeformException $e) {
                }
            }
        }

        return $this;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Builds the form object based on $formData
     *
     * @param FormProperties $formProperties
     */
    private function buildFromData(FormProperties $formProperties)
    {
        $this->name                  = $formProperties->getName();
        $this->handle                = $formProperties->getHandle();
        $this->color                 = $formProperties->getColor();
        $this->submissionTitleFormat = $formProperties->getSubmissionTitleFormat();
        $this->description           = $formProperties->getDescription();
        $this->returnUrl             = $formProperties->getReturnUrl();
        $this->storeData             = $formProperties->isStoreData();
        $this->defaultStatus         = $formProperties->getDefaultStatus();
        $this->formTemplate          = $formProperties->getFormTemplate();
    }

    /**
     * Adds any custom form data items to the form value context session
     *
     * @return $this
     */
    private function setSessionCustomFormData()
    {
        $template        = $this->customAttributes->getDynamicNotificationTemplate();
        $recipients      = $this->customAttributes->getDynamicNotificationRecipients();
        $submissionToken = $this->customAttributes->getSubmissionToken();

        if (!empty($recipients) || !empty($template) || !empty($submissionToken)) {
            $this
                ->getFormValueContext()
                ->setCustomFormData(
                    [
                        FormValueContext::DATA_DYNAMIC_TEMPLATE_KEY   => $template,
                        FormValueContext::DATA_DYNAMIC_RECIPIENTS_KEY => $recipients,
                        FormValueContext::DATA_SUBMISSION_TOKEN       => $submissionToken,
                    ]
                )
                ->saveState();
        }

        return $this;
    }

    /**
     * Returns the assigned submission token
     *
     * @return string|null
     */
    public function getAssociatedSubmissionToken()
    {
        return $this->getFormValueContext()->getSubmissionIdentificator();
    }

    /**
     * @return FormValueContext
     */
    private function getFormValueContext()
    {
        return $this->formAttributes->getFormValueContext();
    }

    /**
     * Set the form to advance to next page and flush cached data
     */
    private function advanceFormToNextPage()
    {
        $formValueContext = $this->getFormValueContext();

        $formValueContext->advanceToNextPage();
        $formValueContext->saveState();

        $this->cachedPageIndex = null;
    }

    /**
     * Set the form to retreat to previous page and flush cached data
     */
    private function retreatFormToPreviousPage()
    {
        $formValueContext = $this->getFormValueContext();

        $formValueContext->retreatToPreviousPage();
        $formValueContext->saveState();

        $this->cachedPageIndex = null;
    }

    /**
     * Store the submitted state in the database
     *
     * @return bool|mixed
     */
    private function saveStoredStateToDatabase()
    {
        $submission = $this->getSubmissionHandler()->storeSubmission($this, $this->layout->getFields());

        if ($submission) {
            $this->formSaved = true;
        }

        return $submission;
    }

    /**
     * Send out any email notifications
     *
     * @param SubmissionModel $submission
     *
     * @throws ComposerException
     */
    private function sendOutEmailNotifications(SubmissionModel $submission = null)
    {
        $adminNotifications = $this->properties->getAdminNotificationProperties();
        if ($adminNotifications->getNotificationId()) {
            $this->getMailHandler()->sendEmail(
                $this,
                $adminNotifications->getRecipientArray(),
                $adminNotifications->getNotificationId(),
                $this->layout->getFields(),
                $submission
            );
        }

        $recipientFields = $this->layout->getRecipientFields();

        foreach ($recipientFields as $field) {
            $this->getMailHandler()->sendEmail(
                $this,
                $field->getRecipients(),
                $field->getNotificationId(),
                $this->layout->getFields(),
                $submission
            );
        }

        $dynamicRecipients = $this->getFormValueContext()->getDynamicNotificationData();
        if ($dynamicRecipients && $dynamicRecipients->getRecipients()) {
            $this->getMailHandler()->sendEmail(
                $this,
                $dynamicRecipients->getRecipients(),
                $dynamicRecipients->getTemplate(),
                $this->layout->getFields(),
                $submission
            );
        }
    }

    /**
     * Pushes all emails to their respective mailing lists, if applicable
     * Does nothing otherwise
     */
    private function pushToMailingLists()
    {
        foreach ($this->getLayout()->getMailingListFields() as $field) {
            if (!$field->getValue() || !$field->getEmailFieldHash() || !$field->getResourceId()) {
                continue;
            }

            $mailingListHandler = $this->getMailingListHandler();

            try {
                $emailField = $this->getLayout()->getFieldByHash($field->getEmailFieldHash());

                // TODO: Log any errors that happen
                $integration = $mailingListHandler->getIntegrationById($field->getIntegrationId());
                $mailingList = $mailingListHandler->getListById($integration, $field->getResourceId());

                /** @var FieldObject[] $mailingListFieldsByHandle */
                $mailingListFieldsByHandle = [];
                foreach ($mailingList->getFields() as $mailingListField) {
                    $mailingListFieldsByHandle[$mailingListField->getHandle()] = $mailingListField;
                }

                $emailList = $emailField->getValue();
                if ($emailList) {
                    $mappedValues = [];
                    if ($field->getMapping()) {
                        foreach ($field->getMapping() as $key => $handle) {
                            if (!isset($mailingListFieldsByHandle[$key])) {
                                continue;
                            }

                            $mailingListField = $mailingListFieldsByHandle[$key];

                            $convertedValue = $integration->convertCustomFieldValue(
                                $mailingListField,
                                $this->getLayout()->getFieldByHandle($handle)->getValue()
                            );

                            $mappedValues[$key] = $convertedValue;
                        }
                    }

                    $mailingList->pushEmailsToList($emailList, $mappedValues);
                    $mailingListHandler->flagMailingListIntegrationForUpdating($integration);
                }

            } catch (FreeformException $exception) {
                continue;
            }
        }
    }

    /**
     * Push the submitted data to the mapped fields of a CRM integration
     *
     * @throws ComposerException
     */
    private function pushToCRM()
    {
        $integrationProperties = $this->properties->getIntegrationProperties();

        $this->crmHandler->pushObject($integrationProperties, $this->getLayout());
    }

    // ==========================
    // INTERFACE IMPLEMENTATIONS
    // ==========================

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return [
            'name'          => $this->name,
            'handle'        => $this->handle,
            'color'         => $this->color,
            'description'   => $this->description,
            'returnUrl'     => $this->returnUrl,
            'storeData'     => (bool) $this->storeData,
            'defaultStatus' => $this->defaultStatus,
            'formTemplate'  => $this->formTemplate,
        ];
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->currentPageRows);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->currentPageRows);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->currentPageRows);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     */
    public function valid()
    {
        return null !== $this->key() && $this->key() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->currentPageRows);
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->currentPageRows[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->currentPageRows[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     * @throws FreeformException
     */
    public function offsetSet($offset, $value)
    {
        throw new FreeformException('Form ArrayAccess does not allow for setting values');
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     *
     * @return void
     * @throws FreeformException
     */
    public function offsetUnset($offset)
    {
        throw new FreeformException('Form ArrayAccess does not allow unsetting values');
    }

    /**
     * @return bool
     */
    private function isLastPage()
    {
        return $this->getFormValueContext()->getCurrentPageIndex() === (\count($this->getPages()) - 1);
    }
}
