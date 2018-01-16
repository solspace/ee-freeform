<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\FreeformNext\Library\Composer\Attributes\FormAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Composer;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;
use Solspace\Addons\FreeformNext\Library\Migrations\Objects\ComposerState;
use Solspace\Addons\FreeformNext\Library\Session\EERequest;
use Solspace\Addons\FreeformNext\Library\Translations\EETranslator;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\NotificationRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Services\CrmService;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Services\FormsService;
use Solspace\Addons\FreeformNext\Services\MailerService;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Services\SettingsService;
use Solspace\Addons\FreeformNext\Services\StatusesService;
use Solspace\Addons\FreeformNext\Services\SubmissionsService;

class NextFormHelper
{
    const STRICT_MODE = true;

    /** @var array */
    public $errors;

    /** @var array */
    private $currentNewFieldsByLegacyId;

    /**
     * @param array $classicForm
     *
     * @return bool
     * @throws FreeformException
     * @throws \Exception
     */
    public function saveForm(array $classicForm)
    {
        $this->setCurrentFieldsByLegacyId();
        $data = $this->convertData($classicForm);

        if (!isset($data['formId'])) {
            throw new FreeformException('No form ID specified');
        }

        if (!isset($data['composerState'])) {
            throw new FreeformException('No composer data present');
        }

        $formId        = $data['formId'];
        $form          = FormRepository::getInstance()->getOrCreateForm($formId);
        $composerState = json_decode($data['composerState'], true);

        $isNew = !$form->id;

        if (array_key_exists('duplicate', $data)) {
            $oldHandle = $composerState['composer']['properties']['form']['handle'];

            if (preg_match('/^([a-zA-Z0-9]*[a-zA-Z]+)(\d+)$/', $oldHandle, $matches)) {
                list($string, $mainPart, $iterator) = $matches;

                $newHandle = $mainPart . ((int) $iterator + 1);
            } else {
                $newHandle = $oldHandle . '1';
            }

            $composerState['composer']['properties']['form']['handle'] = $newHandle;
        }

        $formsService = new FormsService();

        $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();

        $formAttributes = new FormAttributes($formId, $sessionImplementation, new EERequest());
        $composer       = new Composer(
            $composerState,
            $formAttributes,
            $formsService,
            new SubmissionsService(),
            new MailerService(),
            new FilesService(),
            new MailingListsService(),
            new CrmService(),
            new StatusesService(),
            new EETranslator()
        );

        $form->setLegacyId($classicForm['form_id']);
        $form->setLayout($composer);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FORM_BEFORE_SAVE, $form, $isNew)) {
            throw new FreeformException(ExtensionHelper::getLastCallData());
        }

        $existing = FormRepository::getInstance()->getFormByIdOrHandle($form->handle);

        if ($existing && $existing->id !== $form->id) {
            throw new FreeformException(sprintf('Handle "%s" already taken', $form->handle));
        }

        $form->save();

        return true;
    }

    /**
     * @param array $classicForm
     *
     * @return array
     * @throws FreeformException
     */
    private function convertData(array $classicForm)
    {
        $sessionImplementation = (new SettingsService())->getSessionStorageImplementation();
        $formsService          = new FormsService();
        $formAttributes        = new FormAttributes('', $sessionImplementation, new EERequest());

        $notificationId     = $this->getNotificationId($classicForm);
        $notificationEmails = '';

        if ($notificationId) {
            $notificationEmails = $this->getNotificationEmails($classicForm);
        }

        $composerState                     = new ComposerState();
        $composerState->name               = $classicForm['form_label'];
        $composerState->defaultStatus      = StatusRepository::getInstance()->getStatusByHandle(
            $classicForm['default_status']
        );
        $composerState->notificationId     = $notificationId;
        $composerState->notificationEmails = $notificationEmails;
        $composerState->handle             = $classicForm['form_name'];
        $composerState->description        = $classicForm['form_description'];

        $nextFormFields = [];

        foreach ($classicForm['field_ids'] as $id) {
            if (array_key_exists($id, $this->currentNewFieldsByLegacyId)) {
                $nextFormFields[] = $this->currentNewFieldsByLegacyId[$id];
            }
        }

        $this->compareClassicAndNextFieldsCount($classicForm, $nextFormFields);

        $classicFormHelper = $this->getClassicFormHelper();
        $composerId        = $classicFormHelper->getFormComposerId($classicForm['form_id']);

        if (!$composerId) {
            $result = $this->getNormalFormData($nextFormFields);
        } else {
            $result = $this->getComposerFormData($classicForm, $composerId);
        }

        $composerState->layout    = $result['layout'];
        $composerState->fields    = $result['preparedFields'];
        $composerState->pageCount = count($result['layout']);

        $composer = new Composer(
            null,
            $formAttributes,
            $formsService,
            new SubmissionsService(),
            new MailerService(),
            new FilesService(),
            new MailingListsService(),
            new CrmService(),
            new StatusesService(),
            new EETranslator(),
            $composerState
        );

        $composerState = $composer->getComposerStateJSON();

        $data = [
            'formId'        => '',
            'composerState' => $composerState,
        ];

        return $data;
    }

    /**
     * @param array $classicForm
     *
     * @return int
     */
    private function getNotificationId(array $classicForm)
    {
        $notificationId = 0;

        if (strtolower($classicForm['notify_admin']) === 'y') {
            $notification = NotificationRepository::getInstance()->getNotificationByLegacyId(
                $classicForm['admin_notification_id']
            );

            if ($notification) {
                $notificationId = $notification->id;
            }
        }

        return $notificationId;
    }

    /**
     * @param array $classicForm
     *
     * @return mixed
     */
    private function getNotificationEmails(array $classicForm)
    {
        return str_replace('|', "\n", $classicForm['admin_notification_email']);
    }

    /**
     * @param array $nextFormFields
     *
     * @return array
     */
    private function getNormalFormData(array $nextFormFields)
    {
        $result = [
            'layout'         => [],
            'preparedFields' => [],
        ];

        $preparedFields = [];
        $rows           = [];

        $key = 0;
        foreach ($nextFormFields as $key => $nextFormField) {
            $rowHash = HashHelper::hash($key);

            $row['id']      = $rowHash;
            $row['columns'] = [];

            $preparedField = $this->getPreparedField($nextFormField);

            $preparedFields[] = $preparedField;
            $row['columns'][] = $preparedField['hash'];
            $rows[]           = $row;
        }

        $rowHash          = HashHelper::hash($key + 1);
        $row['id']        = $rowHash;
        $row['columns']   = [];
        $submitButton     = $this->getPreparedSubmitField();
        $preparedFields[] = $submitButton;
        $row['columns'][] = $submitButton['hash'];
        $rows[]           = $row;

        $result['layout']         = [$rows];
        $result['preparedFields'] = $preparedFields;

        return $result;
    }

    /**
     * @param array $classicForm
     * @param int   $composerId
     *
     * @return array
     */
    private function getComposerFormData(array $classicForm, $composerId)
    {
        $result = [
            'layout'         => [],
            'preparedFields' => [],
        ];

        $classicFormHelper = $this->getClassicFormHelper();
        $composerData      = $classicFormHelper->getComposerDataById($composerId);

        foreach ($classicForm['field_ids'] as $id) {
            if (array_key_exists($id, $this->currentNewFieldsByLegacyId)) {
                $nextFormFields[] = $this->currentNewFieldsByLegacyId[$id];
            }
        }

        $preparedFields = [];
        $rows           = [];
        $layout         = [];

        foreach ($composerData['rows'] as $key => $composerRow) {

            if (!is_array($composerRow) && $composerRow === "page_break") {
                $layout[] = $rows;
                $rows     = [];
                continue;
            }

            $rowHash = HashHelper::hash($key);

            $row['id']      = $rowHash;
            $row['columns'] = [];

            foreach ($composerRow as $rKey => $composerColumn) {

                foreach ($composerColumn as $composerField) {

                    if ($composerField['type'] === 'field') {

                        if (!array_key_exists('fieldId', $composerField)) {
                            continue;
                        }

                        $fieldId = $composerField['fieldId'];

                        if (array_key_exists($fieldId, $this->currentNewFieldsByLegacyId)) {

                            $nextFormField = $this->currentNewFieldsByLegacyId[$fieldId];
                            $preparedField = $this->getPreparedField($nextFormField);

                            $preparedFields[] = $preparedField;
                            $row['columns'][] = $preparedField['hash'];
                        }
                    }

                    if ($composerField['type'] === 'nonfield_submit') {

                        $previousComposerRow = null;

                        if (array_key_exists($rKey - 1, $composerRow)) {
                            $previousComposerRow = $composerRow[$rKey - 1];
                        }

                        $preparedField = $this->getPreparedSubmitField($composerField, $previousComposerRow);

                        $preparedFields[] = $preparedField;
                        $row['columns'][] = $preparedField['hash'];
                    }

                    if ($composerField['type'] === 'nonfield_paragraph') {
                        $preparedHtml = $this->getPreparedHtmlField($composerField);

                        $preparedFields[] = $preparedHtml;
                        $row['columns'][] = $preparedHtml['hash'];
                    }
                }
            }

            if (count($row['columns']) > 0) {
                $rows[] = $row;
            }
        }

        // Add last page to layout
        $layout[] = $rows;

        $result['layout']         = $layout;
        $result['preparedFields'] = $preparedFields;

        return $result;
    }

    /**
     * @param FieldModel $nextFormField
     *
     * @return array
     */
    private function getPreparedField(FieldModel $nextFormField)
    {
        $preparedField                 = [];
        $preparedField['hash']         = $nextFormField->getHash();
        $preparedField['id']           = $nextFormField->getId();
        $preparedField['type']         = $nextFormField->type;
        $preparedField['handle']       = $nextFormField->handle;
        $preparedField['label']        = $nextFormField->label;
        $preparedField['required']     = $nextFormField->required;
        $preparedField['instructions'] = $nextFormField->instructions;
        $preparedField['value']        = $nextFormField->value;
        $preparedField['placeholder']  = $nextFormField->placeholder;

        if ($nextFormField->options) {
            $preparedField['options'] = $nextFormField->options;
        }

        if ($nextFormField->fileKinds) {
            $preparedField['fileKinds'] = $nextFormField->fileKinds;
        }

        if ($nextFormField->maxFileSizeKB) {
            $preparedField['maxFileSizeKB'] = $nextFormField->maxFileSizeKB;
        }

        if ($nextFormField->assetSourceId) {
            $preparedField['assetSourceId'] = $nextFormField->assetSourceId;
        }

        if ($nextFormField->checked) {
            $preparedField['checked'] = $nextFormField->checked;
        }

        if ($nextFormField->rows) {
            $preparedField['rows'] = $nextFormField->rows;
        }

        $showCustomValues = false;

        if ($nextFormField->additionalProperties) {
            $additionalProperties = $nextFormField->additionalProperties;
            if (array_key_exists(
                    'custom_values',
                    $additionalProperties
                ) && $additionalProperties['custom_values'] == 1) {
                $showCustomValues = true;
            }
        }

        $preparedField['showCustomValues'] = $showCustomValues;

        return $preparedField;
    }

    /**
     * @param array|null $composerField
     * @param array|null $previousComposerRow
     *
     * @return array
     */
    private function getPreparedSubmitField(array $composerField = null, array $previousComposerRow = null)
    {
        /** @var FieldModel $nextFormField */

        $preparedField                = [];
        $preparedField['hash']        = HashHelper::hash($this->getNewId());
        $preparedField['type']        = FieldInterface::TYPE_SUBMIT;
        $preparedField['label']       = 'Submit';
        $preparedField['labelNext']   = 'Submit';
        $preparedField['labelPrev']   = 'Previous';
        $preparedField['position']    = 'left';
        $preparedField['disablePrev'] = true;

        if ($composerField) {
            $preparedField['label']     = $composerField['html'];
            $preparedField['labelNext'] = $composerField['html'];
        }

        if (!$previousComposerRow) {
            return $preparedField;
        }

        foreach ($previousComposerRow as $previousComposerField) {
            if ($previousComposerField && $previousComposerField['type'] === 'nonfield_submit_previous') {
                $preparedField['labelPrev']   = $previousComposerField['html'];
                $preparedField['disablePrev'] = false;
                break;
            }
        }

        return $preparedField;
    }

    /**
     * @param array $composerField
     *
     * @return array
     */
    private function getPreparedHtmlField(array $composerField)
    {
        /** @var FieldModel $nextFormField */

        $preparedField          = [];
        $preparedField['hash']  = HashHelper::hash($this->getNewId());
        $preparedField['type']  = FieldInterface::TYPE_HTML;
        $preparedField['value'] = $composerField['html'];
        $preparedField['label'] = 'HTML';

        return $preparedField;
    }

    /**
     * @return int
     */
    private function getNewId()
    {
        return mt_rand(10000, 99999999);
    }

    /**
     * @param array $classicForm
     * @param array $nextFormFields
     *
     * @throws FreeformException
     */
    private function compareClassicAndNextFieldsCount(array $classicForm, array $nextFormFields)
    {
        $classicFormName       = $classicForm['form_name'];
        $classicFormFieldCount = count($classicForm['field_ids']);
        $nextFormFieldCount    = count($nextFormFields);

        if ($nextFormFieldCount !== $classicFormFieldCount) {
            $missingCount = $classicFormFieldCount - $nextFormFieldCount;
            $message      = 'There are missing ' . $missingCount . ' fields for form ' . $classicFormName;

            $this->errors[] = $message;

            if (self::STRICT_MODE) {
                throw new FreeformException($message);
            }
        }
    }

    /**
     * @param array $classicField
     *
     * @return mixed
     */
    private function getClassicFieldType(array $classicField)
    {
        return $classicField['field_type'];
    }

    /**
     * @param array $classicField
     *
     * @return array
     */
    private function setTypes(array $classicField)
    {
        $nextTypeName = $this->getNextFieldTypeFromClassicFieldType($this->getClassicFieldType($classicField));

        $types = $this->getNextTypesArray();

        $valueFields = $types[$nextTypeName];

        foreach ($valueFields as $valueField) {
            $types[$nextTypeName][$valueField] = $this->getNextValueFromClassicValue($valueField, $classicField);
        }

        return $types;
    }

    /**
     * @param array $classicType
     *
     * @return bool|mixed
     */
    private function getNextFieldTypeFromClassicFieldType(array $classicType)
    {
        // Classic Field Type => Next Field Type
        $mapping = [
            'text' => 'text',
        ];

        if (array_key_exists($classicType, $mapping)) {
            return $mapping[$classicType];
        }

        return false;
    }

    /**
     * @param array $nextValueField
     * @param array $classicField
     *
     * @return bool|mixed
     */
    private function getNextValueFromClassicValue(array $nextValueField, array $classicField)
    {
        $mapping = $this->getNextValueFromClassicValueMapping();

        if (array_key_exists($nextValueField, $mapping)) {
            $classicValueName = $mapping[$nextValueField];

            if ($classicValueName && array_key_exists($classicValueName, $classicField)) {
                $value = $classicField[$classicValueName];

                switch ($classicValueName) {
                    case 'required':
                        $value = $this->formatClassicRequiredValue($value);
                        break;
                }

                return $value;
            }
        }

        return false;
    }

    /* Classic Field Formatting */

    /**
     * @param string $value
     *
     * @return bool
     */
    private function formatClassicRequiredValue($value)
    {
        return $value === 'y';
    }

    private function setCurrentFieldsByLegacyId()
    {
        $this->currentNewFieldsByLegacyId = FieldRepository::getInstance()->getAllFieldsByLegacyId();
    }

    /**
     * @return bool|ClassicFormHelper
     */
    private function getClassicFormHelper()
    {
        $formService = 'Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFormHelper';
        if (class_exists($formService)) {
            /** @var \Solspace\Addons\FreeformNext\Library\Migrations\Helpers\ClassicFormHelper $formService */
            $formService = new $formService();

            return $formService;
        }

        return false;
    }

    /* Classic Field Value Mapping */

    /**
     * Next Value Field Type => Classic Value Field Type
     *
     * @return array
     */
    private function getNextValueFromClassicValueMapping()
    {
        return [
            'label'        => 'field_label',
            'handle'       => 'field_name',
            'instructions' => 'field_description',
            'required'     => 'required',
            'type'         => 'field_type',
            'value'        => null,
            'placeholder'  => null,
        ];
    }

    /**
     * @return array
     */
    private function getNextTypesArray()
    {
        return [
            'text'         => [
                'value'       => '',
                'placeholder' => '',
            ],
            'textarea'     => [
                'value'       => '',
                'placeholder' => '',
                'rows'        => '',
            ],
            'email'        => [
                'placeholder' => '',
            ],
            'hidden'       => [
                'value' => '',
            ],
            'checkbox'     => [
                'value' => '',
            ],
            'file'         => [
                'assetSourceId' => '',
                'maxFileSizeKB' => '',
            ],
            'rating'       => [
                'value'         => '',
                'maxValue'      => '',
                'colorIdle'     => '',
                'colorHover'    => '',
                'colorSelected' => '',
            ],
            'datetime'     => [
                'dateTimeType'   => '',
                'initialValue'   => '',
                'placeholder'    => '',
                'dateOrder'      => '',
                'dateSeparator'  => '',
                'clockSeparator' => '',
            ],
            'website'      => [
                'value'       => '',
                'placeholder' => '',
            ],
            'number'       => [
                'value'              => '',
                'placeholder'        => '',
                'minValue'           => '',
                'maxValue'           => '',
                'minLength'          => '',
                'maxLength'          => '',
                'decimalCount'       => '',
                'decimalSeparator'   => '',
                'thousandsSeparator' => '',
            ],
            'phone'        => [
                'value'       => '',
                'placeholder' => '',
                'pattern'     => '',
            ],
            'confirmation' => [
                'value'       => '',
                'placeholder' => '',
            ],
            'regex'        => [
                'value'       => '',
                'placeholder' => '',
                'pattern'     => '',
                'message'     => '',
            ],
        ];
    }
}
