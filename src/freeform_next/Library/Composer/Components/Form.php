<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components;

use Craft\Freeform_SubmissionModel;
use Craft\TemplateHelper;
use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\CustomFormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MailingListInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Properties\FormProperties;
use Solspace\Addons\FreeformNext\Library\Database\CRMHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\FormHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\MailingListHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Database\SubmissionHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FileUploadException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Integrations\DataObjects\FieldObject;
use Solspace\Addons\FreeformNext\Library\Mailing\MailHandlerInterface;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

class Form implements \JsonSerializable, \Iterator, \ArrayAccess
{
    const PAGE_INDEX_KEY     = "page_index";
    const RETURN_URI_KEY     = "formReturnUrl";
    const DEFAULT_PAGE_INDEX = 0;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $handle;

    /** @var string */
    private $submissionTitleFormat;

    /** @var string */
    private $description;

    /** @var string */
    private $returnUrl;

    /** @var bool */
    private $storeData;

    /** @var int */
    private $defaultStatus;

    /** @var string */
    private $formTemplate;

    /** @var Layout */
    private $layout;

    /** @var Row[] */
    private $currentPageRows;

    /** @var FormAttributes */
    private $formAttributes;

    /** @var Properties */
    private $properties;

    /** @var Page */
    private $currentPage;

    /** @var bool */
    private $formSaved;

    /** @var bool */
    private $valid;

    /** @var SubmissionHandlerInterface */
    private $submissionHandler;

    /** @var FormHandlerInterface */
    private $formHandler;

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

    /** @var CustomFormAttributes */
    private $customAttributes;

    /**
     * Form constructor.
     *
     * @param Properties                  $properties
     * @param FormAttributes              $formAttributes
     * @param array                       $layoutData
     * @param FormHandlerInterface        $formHandler
     * @param SubmissionHandlerInterface  $submissionHandler
     * @param MailHandlerInterface        $mailHandler
     * @param FileUploadHandlerInterface  $fileUploadHandler
     * @param MailingListHandlerInterface $mailingListHandler
     * @param CRMHandlerInterface         $crmHandler
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        Properties $properties,
        FormAttributes $formAttributes,
        array $layoutData,
        FormHandlerInterface $formHandler,
        SubmissionHandlerInterface $submissionHandler,
        MailHandlerInterface $mailHandler,
        FileUploadHandlerInterface $fileUploadHandler,
        MailingListHandlerInterface $mailingListHandler,
        CRMHandlerInterface $crmHandler,
        TranslatorInterface $translator
    ) {
        $this->properties         = $properties;
        $this->formHandler        = $formHandler;
        $this->submissionHandler  = $submissionHandler;
        $this->mailHandler        = $mailHandler;
        $this->fileUploadHandler  = $fileUploadHandler;
        $this->mailingListHandler = $mailingListHandler;
        $this->crmHandler         = $crmHandler;
        $this->translator         = $translator;
        $this->storeData          = true;
        $this->customAttributes   = new CustomFormAttributes();

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
        $this->setCurrentPage($this->getPageIndexFromContext());
        $this->currentPageRows = $this->currentPage->getRows();
        $this->isValid();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param $fieldHandle
     *
     * @return null|AbstractField
     */
    public function get($fieldHandle)
    {
        try {
            return $this->getLayout()->getFieldByHandle($fieldHandle);
        } catch (FreeformException $e) {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
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
        return $this->currentPage;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl ?: "";
    }

    /**
     * @return int
     */
    public function getDefaultStatus()
    {
        return $this->defaultStatus;
    }

    /**
     * @return boolean
     */
    public function isFormSaved()
    {
        return $this->formSaved;
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
     * @return bool
     */
    public function isValid()
    {
        if (!is_null($this->valid)) {
            return $this->valid;
        }

        if ($this->getFormValueContext()->shouldFormWalkToPreviousPage()) {
            $this->valid = true;

            return $this->valid;
        }

        $pageIsPosted = $this->getFormValueContext()->hasPageBeenPosted();
        if (!$pageIsPosted) {
            $this->valid = false;

            return $this->valid;
        }

        $currentPageFields = $this->currentPage->getFields();

        $isFormValid = true;
        foreach ($currentPageFields as $field) {
            if (!$field->isValid()) {
                $isFormValid = false;
            }
        }

        if (
            $isFormValid &&
            $this->formHandler->isSpamProtectionEnabled() &&
            !$this->getFormValueContext()->isHoneypotValid()
        ) {
            $this->formHandler->incrementSpamBlockCount($this);
            $this->valid = false;

            return $this->valid;
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
    public function hasErrors()
    {
        $pageIsPosted = $this->getFormValueContext()->hasPageBeenPosted();

        if ($pageIsPosted && !$this->isValid()) {
            // If the form isn' valid because of a honeypot, we pretend nothing was wrong
            if ($this->formHandler->isSpamProtectionEnabled() && !$this->getFormValueContext()->isHoneypotValid()) {
                return false;
            }

            return true;
        }

        return false;
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
        $formValueContext = $this->getFormValueContext();

        if ($formValueContext->shouldFormWalkToPreviousPage()) {
            $formValueContext->retreatToPreviousPage();
            $formValueContext->saveState();

            return true;
        }

        if (!$this->isValid()) {
            throw new FreeformException($this->translator->translate("Trying to post an invalid form"));
        }

        $submittedValues = [];
        foreach ($this->currentPage->getFields() as $field) {
            if ($field instanceof NoStorageInterface && !$field instanceof MailingListInterface) {
                continue;
            }

            $value = $field->getValue();
            if ($field instanceof StaticValueInterface) {
                if (!empty($value)) {
                    $value = $field->getStaticValue();
                }
            }

            $submittedValues[$field->getHandle()] = $value;
        }

        $formValueContext->appendStoredValues($submittedValues);

        if ($formValueContext->getCurrentPageIndex() === (count($this->getPages()) - 1)) {
            if ($this->storeData) {
                $submission = $this->saveStoredStateToDatabase();
            } else {
                $submission = null;
                $this->formSaved = true;
            }
            $this->sendOutEmailNotifications($submission);
            $this->pushToMailingLists();
            $this->pushToCRM();

            $formValueContext->cleanOutCurrentSession();

            return $submission;
        }

        $formValueContext->advanceToNextPage();
        $formValueContext->saveState();

        return true;
    }

    /**
     * Render a predefined template
     *
     * @param array $customFormAttributes
     *
     * @return string
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
     */
    public function renderTag(array $customFormAttributes = null)
    {
        $this->setAttributes($customFormAttributes);

        $customAttributes = $this->getCustomAttributes();

        $encTypeAttribute = count($this->getLayout()->getFileUploadFields()) ? ' enctype="multipart/form-data"' : '';

        $idAttribute = $customAttributes->getId();
        $idAttribute = $idAttribute ? ' id="' . $idAttribute . '"' : "";

        $nameAttribute = $customAttributes->getName();
        $nameAttribute = $nameAttribute ? ' name="' . $nameAttribute . '"' : "";

        $methodAttribute = $customAttributes->getMethod() ?: $this->formAttributes->getMethod();
        $methodAttribute = $methodAttribute ? ' method="' . $methodAttribute . '"' : '';

        $classAttribute = $customAttributes->getClass();
        $classAttribute = $classAttribute ? ' class="' . $classAttribute . '"' : '';

        $actionAttribute = $customAttributes->getAction();
        $actionAttribute = $actionAttribute ? ' action="' . $actionAttribute . '"' : "";

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

        if (!$customAttributes->getAction()) {
            $output .= '<input type="hidden" name="action" value="' . $this->formAttributes->getActionUrl() . '" />';
        }

        if ($customAttributes->getReturnUrl()) {
            $output .= '<input type="hidden" name="' . self::RETURN_URI_KEY . '" value="' . $customAttributes->getReturnUrl() . '" />';
        }

        $output .= '<input '
            . 'type="hidden" '
            . 'name="' . FormValueContext::FORM_HASH_KEY . '" '
            . 'value="' . $this->getFormValueContext()->getHash() . '" '
            . '/>';

        if ($this->formHandler->isSpamProtectionEnabled()) {
            $output .= $this->getHoneyPotInput();
        }

        if ($this->formAttributes->isCsrfEnabled()) {
            $csrfTokenName = $this->formAttributes->getCsrfTokenName();
            $csrfToken     = $this->formAttributes->getCsrfToken();

            $output .= '<input type="hidden" name="' . $csrfTokenName . '" value="' . $csrfToken . '" />';
        }

        $hiddenFields = $this->layout->getHiddenFields();
        foreach ($hiddenFields as $field) {
            if ($field->getPageIndex() === $this->currentPage->getIndex()) {
                $output .= $field->renderInput();
            }
        }

        return TemplateHelper::getRaw($output);
    }

    /**
     * @return string
     */
    public function renderClosingTag()
    {
        return TemplateHelper::getRaw('</form>');
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
     * @param array|null $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = null)
    {
        if (!is_null($attributes)) {
            $this->customAttributes->mergeAttributes($attributes);
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
     * @param int $index
     *
     * @throws FreeformException
     */
    private function setCurrentPage($index)
    {
        if (!isset($this->layout->getPages()[$index])) {
            throw new FreeformException(
                $this->getTranslator()->translate(
                    "The provided page index '{pageIndex}' does not exist in form '{formName}'",
                    ["pageIndex" => $index, "formName" => $this->getName()]
                )
            );
        }

        $this->currentPage = $this->layout->getPages()[$index];
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
        $this->submissionTitleFormat = $formProperties->getSubmissionTitleFormat();
        $this->description           = $formProperties->getDescription();
        $this->returnUrl             = $formProperties->getReturnUrl();
        $this->storeData             = $formProperties->isStoreData();
        $this->defaultStatus         = $formProperties->getDefaultStatus();
        $this->formTemplate          = $formProperties->getFormTemplate();
    }

    /**
     * @return int
     */
    private function getPageIndexFromContext()
    {
        return $this->getFormValueContext()->getCurrentPageIndex();
    }

    /**
     * @return FormValueContext
     */
    private function getFormValueContext()
    {
        return $this->formAttributes->getFormValueContext();
    }

    /**
     * Assembles a honeypot field
     *
     * @return string
     */
    private function getHoneyPotInput()
    {
        $random = time() . rand(111, 999) . (time() + 999);
        $hash   = substr(sha1($random), 0, 6);

        $honeypot = $this->getFormValueContext()->getNewHoneypot();
        $output   = '<input '
            . 'type="text" '
            . 'value="' . $hash . '"'
            . 'id="' . $honeypot->getName() . '"'
            . 'name="' . $honeypot->getName() . '" '
            . '/>';

        $output = '<div style="position: absolute !important; width: 0 !important; height: 0 !important; overflow: hidden !important;">'
            . "<label>Leave this field blank</label>"
            . $output
            . '</div>'
            . '<script type="text/javascript">document.getElementById("' . $honeypot->getName(
            ) . '").value = "' . $honeypot->getHash() . '";</script>';

        return $output;
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
     * @param Freeform_SubmissionModel $submission
     */
    private function sendOutEmailNotifications(Freeform_SubmissionModel $submission = null)
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
                            $mailingListField = $mailingListFieldsByHandle[$key];
                            $convertedValue   = $mailingListField->convertValue(
                                $this->getLayout()->getFieldByHandle($handle)->getValueAsString()
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
    function jsonSerialize()
    {
        return [
            "name"          => $this->name,
            "handle"        => $this->handle,
            "description"   => $this->description,
            "returnUrl"     => $this->returnUrl,
            "storeData"     => (bool)$this->storeData,
            "defaultStatus" => $this->defaultStatus,
            "formTemplate"  => $this->formTemplate,
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
        return !is_null($this->key()) && $this->key() !== false;
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
        throw new FreeformException("Form ArrayAccess does not allow for setting values");
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
        throw new FreeformException("Form ArrayAccess does not allow unsetting values");
    }
}
