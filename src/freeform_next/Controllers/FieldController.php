<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\Validation\Result;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FieldException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Model\FieldModel;
use Solspace\Addons\FreeformNext\Repositories\FieldRepository;
use Solspace\Addons\FreeformNext\Repositories\FileRepository;
use Solspace\Addons\FreeformNext\Services\FilesService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

class FieldController extends Controller
{
    /**
     * @return CpView
     */
    public function index()
    {
        $canAccessFields = $this->getPermissionsService()->canAccessFields(ee()->session->userdata('group_id'));

        if (!$canAccessFields) {
            return new RedirectView($this->getLink('denied'));
        }

        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $table->setColumns(
            [
                'id'     => ['type' => Table::COL_ID],
                'label'  => ['type' => Table::COL_TEXT],
                'handle' => ['type' => Table::COL_TEXT],
                'type'   => ['type' => Table::COL_TEXT],
                'manage' => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $fields = FieldRepository::getInstance()->getAllFields();

        $tableData = [];
        foreach ($fields as $field) {
            $tableData[] = [
                $field->id,
                [
                    'content' => $field->label,
                    'href'    => $this->getLink('fields/' . $field->id),
                ],
                $field->handle,
                $field->type,
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => $this->getLink('fields/' . $field->id),
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                [
                    'name'  => 'id_list[]',
                    'value' => $field->id,
                    'data'  => [
                        'confirm' => lang('Field') . ': <b>' . htmlentities($field->label, ENT_QUOTES) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $template = [
			'table'            => $table->viewData(),
			'cp_page_title'    => lang('Fields'),
			'form_right_links' => FreeformHelper::get('right_links', $this),
			'footer' => [
				'submit_lang' => lang('submit'),
				'type'        => 'bulk_action_form',
			]
		];

        $view = new CpView('fields/listing', $template);

        $view->setHeading(lang('Fields'));
        $view->addModal((new ConfirmRemoveModal($this->getLink('fields/delete')))->setKind('Fields'));

        return $view;
    }

    /**
     * @param int|null    $id
     * @param Result|null $validation
     *
     * @return CpView
     * @throws FieldException
     */
    public function edit($id, Result $validation = null)
    {
        $canAccessFields = $this->getPermissionsService()->canAccessFields(ee()->session->userdata('group_id'));

        if (!$canAccessFields) {
            return new RedirectView($this->getLink('denied'));
        }

        if ($id === 'new') {
            $model = FieldModel::create();
        } else {
            $model = FieldRepository::getInstance()->getFieldById($id);
        }

        if (!$model) {
            throw new FieldException(sprintf('Field by ID "%d" not found', $id));
        }

        $fieldTypes = $this->getFieldsService()->getFieldTypes();

        $sections = [
            [
                [
                    'title'  => lang('Label'),
                    'desc'   => lang('The default label for this field.'),
                    'fields' => [
                        'label' => [
                            'type'     => 'text',
                            'value'    => $model->label,
                            'required' => true,
                            'attrs'    => 'data-generator-base',
                        ],
                    ],
                ],
                [
                    'title'  => lang('Handle'),
                    'desc'   => lang('How you’ll refer to this field in the templates.'),
                    'fields' => [
                        'handle' => [
                            'type'     => 'text',
                            'value'    => $model->handle,
                            'required' => true,
                            'attrs'    => 'data-generator-target',
                        ],
                    ],
                ],
                [
                    'title'  => lang('Instructions'),
                    'desc'   => lang('Default instructions / help text for this field.'),
                    'fields' => [
                        'instructions' => [
                            'type'  => 'textarea',
                            'value' => $model->instructions,
                        ],
                    ],
                ],
                [
                    'title'  => lang('Required'),
                    'desc'   => lang('Set this field as required by default.'),
                    'fields' => [
                        'required' => [
                            'type'  => 'yes_no',
                            'value' => $model->required ? 'y' : 'n',
                        ],
                    ],
                ],
                [
                    'title'  => lang('Type'),
                    'desc'   => lang('What type of field is this?'),
                    'fields' => [
                        'type' => [
                            'disabled'     => (bool) $model->id,
                            'type'         => 'select',
                            'value'        => $model->type,
                            'required'     => true,
                            'choices'      => $fieldTypes,
                            'group_toggle' => array_combine(
                                array_keys($fieldTypes),
                                array_keys($fieldTypes)
                            ),
                        ],
                    ],
                ],
            ],
            'Settings' => $this->getFieldSettingsByType($model),
        ];

        ee()->cp->add_js_script('plugin', 'minicolors');
        ee()->cp->add_js_script(['file' => ['cp/form_group']]);

        $view = new CpView(
            'fields/edit',
            [
                'errors'                => $validation,
                'sections'              => $sections,
                'cp_page_title'         => lang('Field'),
                'base_url'              => $this->getLink('fields/' . $id),
                'save_btn_text'         => lang('Save'),
                'save_btn_text_working' => lang('Saving...'),
            ]
        );
        $view
            ->setHeading($model->label ?: lang('New Field'))
            ->addBreadcrumb(new NavigationLink('Fields', 'fields'))
            ->addJavascript('fieldEditor');

        if (!$model->id) {
            $view->addJavascript('handleGenerator');
        }

        return $view;
    }

    /**
     * @param int|null $fieldId
     *
     * @return FieldModel
     */
    public function save($fieldId = null)
    {
        $field = FieldRepository::getInstance()->getOrCreateField($fieldId);

        $canAccessFields = $this->getPermissionsService()->canAccessFields(ee()->session->userdata('group_id'));

        if (!$canAccessFields) {
            return $field;
        }

        $isNew = !$field->id;

        $post        = $_POST;
        $type        = isset($_POST['type']) ? $_POST['type'] : $field->type;
        $validValues = $additionalProperties = [];
        foreach ($post as $key => $value) {
            if (property_exists($field, $key)) {
                $validValues[$key] = $value;
            }
        }

        $booleanValues = [
            'required',
            'checked',
            'generatePlaceholder',
            'date4DigitYear',
            'dateLeadingZero',
            'clock24h',
            'lowercaseAMPM',
            'clockAMPMSeparate',
            'allowNegative',
            'useScript',
        ];

        $integerValues = [
            'minValue',
            'maxValue',
            'minLength',
            'maxLength',
            'decimalCount',
            'maxValues',
        ];

        foreach ($validValues as $key => $value) {
            if (in_array($key, $booleanValues, true)) {
                $validValues[$key] = $value === 'y';
            }
        }

        $fieldHasOptions = in_array(
            $type,
            [
                FieldInterface::TYPE_RADIO_GROUP,
                FieldInterface::TYPE_CHECKBOX_GROUP,
                FieldInterface::TYPE_SELECT,
                FieldInterface::TYPE_MULTIPLE_SELECT,
                FieldInterface::TYPE_DYNAMIC_RECIPIENTS,
            ],
            true
        );

        $field->type = $type;

        if (isset($post['types'][$type])) {
            $fieldSpecificPost = $post['types'][$type];
            foreach ($fieldSpecificPost as $key => $value) {
                if (in_array($key, ['values', 'options'], true)) {
                    continue;
                }

                if ($key === 'checked') {
                    $value = $value === 'y';
                }

                if (property_exists($field, $key)) {
                    $validValues[$key] = $value;
                } else {
                    if (in_array($key, $booleanValues, true)) {
                        $value = $value === 'y';
                    }

                    $additionalProperties[$key] = $value;
                }
            }

            $hasValues         = isset($fieldSpecificPost['values']) && is_array($fieldSpecificPost['values']);
            $forceLabelOnValue = isset($fieldSpecificPost['custom_values']) && $fieldSpecificPost['custom_values'] !== '1';

            if ($fieldHasOptions && $hasValues) {
                $field->setPostValues($fieldSpecificPost, $forceLabelOnValue);
            } else {
                $validValues['values'] = null;
            }
        }

        if ($type === FieldInterface::TYPE_FILE) {
            if (!isset($validValues['fileKinds'])) {
                $validValues['fileKinds'] = [];
            }

            if (!isset($validValues['maxFileSizeKB']) || empty($validValues['maxFileSizeKB'])) {
                $validValues['maxFileSizeKB'] = 2048;
            }
        }

        $validValues['additionalProperties'] = empty($additionalProperties) ? null : $additionalProperties;

        $field->set($validValues);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_BEFORE_SAVE, $field, $isNew)) {
            return $field;
        }

        try {
            $field->save();

            ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_AFTER_SAVE, $field, $isNew);

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('Success'))
                ->defer();

            return $field;
        } catch (\Exception $e) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->withTitle($e->getMessage())
                ->defer();

            return $field;
        }
    }

    /**
     * @return RedirectView
     */
    public function batchDelete()
    {
        $canAccessFields = $this->getPermissionsService()->canAccessFields(ee()->session->userdata('group_id'));

        if (!$canAccessFields) {
            return new RedirectView($this->getLink('denied'));
        }

        if (isset($_POST['id_list'])) {
            $ids = [];
            foreach ($_POST['id_list'] as $id) {
                $ids[] = (int) $id;
            }

            $models = FieldRepository::getInstance()->getFieldsByIdList($ids);

            foreach ($models as $model) {
                if (!ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_AFTER_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink('fields'));
    }

    /**
     * @param FieldModel $model
     *
     * @return array
     */
    private function getFieldSettingsByType(FieldModel $model)
    {
        $fileKinds         = [];
        $fileKindsOriginal = $this->getFileService()->getFileKinds();

        foreach ($fileKindsOriginal as $kind => $data) {
            $fileKinds[$kind] = ucfirst($kind);
        }

        $byType = [
            FieldInterface::TYPE_TEXT               => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_TEXTAREA           => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
                [
                    'title'  => 'Rows',
                    'desc'   => 'The default number of rows this textarea should have.',
                    'fields' => [
                        'rows' => [
                            'type'  => 'text',
                            'value' => $model->rows,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_EMAIL              => [
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_HIDDEN             => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_SELECT             => [
                [
                    'title'  => 'Custom values',
                    'desc'   => 'Enable this to specify custom values for each option label.',
                    'fields' => [
                        'value_list' => [
                            'type'    => 'html',
                            'content' => $this->getFieldHtml(
                                $model,
                                'custom_values',
                                FieldInterface::TYPE_SELECT
                            ),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_CHECKBOX           => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Checked by default',
                    'desc'   => 'Enable this to check the checkbox by default.',
                    'fields' => [
                        'checked' => [
                            'type'  => 'yes_no',
                            'value' => $model->checked ? 'y' : 'n',
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_CHECKBOX_GROUP     => [
                [
                    'title'  => 'Custom values',
                    'desc'   => 'Enable this to specify custom values for each option label.',
                    'fields' => [
                        'value_list' => [
                            'type'    => 'html',
                            'content' => $this->getFieldHtml(
                                $model,
                                'custom_values',
                                FieldInterface::TYPE_CHECKBOX_GROUP
                            ),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_MULTIPLE_SELECT     => [
                [
                    'title'  => 'Custom values',
                    'desc'   => 'Enable this to specify custom values for each option label.',
                    'fields' => [
                        'value_list' => [
                            'type'    => 'html',
                            'content' => $this->getFieldHtml(
                                $model,
                                'custom_values',
                                FieldInterface::TYPE_MULTIPLE_SELECT
                            ),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_RADIO_GROUP        => [
                [
                    'title'  => 'Custom values',
                    'desc'   => 'Enable this to specify custom values for each option label.',
                    'fields' => [
                        'value_list' => [
                            'type'    => 'html',
                            'content' => $this->getFieldHtml(
                                $model,
                                'custom_values',
                                FieldInterface::TYPE_RADIO_GROUP
                            ),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_FILE               => [
                [
                    'title'  => 'Upload Directory',
                    'desc'   => 'Select a default upload directory source for uploaded files.',
                    'fields' => [
                        'assetSourceId' => [
                            'type'    => 'select',
                            'value'   => $model->assetSourceId,
                            'choices' => array_column(
                                FileRepository::getInstance()->getAllAssetSources(),
                                'name',
                                'id'
                            ),
                        ],
                    ],
                ],
                [
                    'title'  => 'File Count',
                    'desc'   => 'Specify the maximum uploadable file count',
                    'fields' => [
                        'fileCount' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('fileCount'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Maximum filesize',
                    'desc'   => 'Specify the default maximum file size, in KB.',
                    'fields' => [
                        'maxFileSizeKB' => [
                            'type'  => 'text',
                            'value' => $model->maxFileSizeKB,
                        ],
                    ],
                ],
                [
                    'title'  => 'Allowed File Types',
                    'desc'   => 'Select the file types to be allowed by default. Leaving all unchecked will allow all file types. Please be sure that the EE Upload Directory\'s <b>Allowed file types?</b> preference is set to <b>All file types</b>, even if you\'re only using images.',
                    'fields' => [
                        'fileKinds' => [
                            'type'    => 'checkbox',
                            'value'   => $model->fileKinds,
                            'choices' => $fileKinds,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_DYNAMIC_RECIPIENTS => [
                [
                    'title'  => 'Custom values',
                    'desc'   => 'Enable this to specify custom values for each option label.',
                    'fields' => [
                        'value_list' => [
                            'type'    => 'html',
                            'content' => $this->getFieldHtml(
                                $model,
                                'dynamic_recipients',
                                FieldInterface::TYPE_DYNAMIC_RECIPIENTS
                            ),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_RATING             => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'If present, this will be the value pre-populated when the form is rendered.',
                    'fields' => [
                        'value' => [
                            'type'    => 'select',
                            'value'   => $model->value,
                            'choices' => [
                                0  => 'None',
                                1  => 1,
                                2  => 2,
                                3  => 3,
                                4  => 4,
                                5  => 5,
                                6  => 6,
                                7  => 7,
                                8  => 8,
                                9  => 9,
                                10 => 10,
                            ],
                        ],
                    ],
                ],
                [
                    'title'  => 'Maximum Number of Stars',
                    'desc'   => 'Set how many stars there should be for this rating.',
                    'fields' => [
                        'maxValue' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('maxValue', 5),
                            'choices' => [
                                3  => 3,
                                4  => 4,
                                5  => 5,
                                6  => 6,
                                7  => 7,
                                8  => 8,
                                9  => 9,
                                10 => 10,
                            ],
                        ],
                    ],
                ],
                [
                    'title'  => 'Unselected Color',
                    'fields' => [
                        'colorIdle' => [
                            'type'  => 'text',
                            'attrs' => 'class="color-picker"',
                            'value' => $model->getAdditionalProperty('colorIdle', '#dddddd'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Hover Color',
                    'fields' => [
                        'colorHover' => [
                            'type'  => 'text',
                            'attrs' => 'class="color-picker"',
                            'value' => $model->getAdditionalProperty('colorHover', '#FFD700'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Selected Color',
                    'fields' => [
                        'colorSelected' => [
                            'type'  => 'text',
                            'attrs' => 'class="color-picker"',
                            'value' => $model->getAdditionalProperty('colorSelected', '#ff7700'),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_DATETIME           => [
                [
                    'title'  => 'Date Time Type',
                    'desc'   => 'Use only date, time or both.',
                    'fields' => [
                        'dateTimeType' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('dateTimeType', 'both'),
                            'choices' => [
                                'both' => 'Both',
                                'date' => 'Date',
                                'time' => 'Time',
                            ],
                            'attrs'   => 'id="dateTimeType"',
                        ],
                    ],
                ],
                [
                    'title'  => 'Default Value',
                    'desc'   => 'You can use \'now\', \'today\', \'5 days ago\', \'2017-01-01 20:00:00\', etc, which will format the default value according to the chosen format.',
                    'fields' => [
                        'initialValue' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('initialValue'),
                            'attrs' => 'data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Generate Placeholder',
                    'desc'   => 'Enable this to automatically generate a placeholder based on the given date format settings.',
                    'fields' => [
                        'generatePlaceholder' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('generatePlaceholder', true) ? 'y' : 'n',
                            'attrs' => 'data-toggle="placeholder" data-toggle-reverse data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                            'attrs' => 'data-toggle-group="placeholder" data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Date Order',
                    'desc'   => 'Choose the order in which to show day, month and year.',
                    'fields' => [
                        'dateOrder' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('dateOrder', 'ymd'),
                            'choices' => [
                                'ymd' => 'Year Month Day',
                                'mdy' => 'Month Day Year',
                                'dmy' => 'Day Month Year',
                            ],
                            'attrs'   => 'data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Four digit year?',
                    'fields' => [
                        'date4DigitYear' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('date4DigitYear', true) ? 'y' : 'n',
                            'attrs' => 'data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Date leading zero',
                    'desc'   => 'If enabled, a leading zero will be used for days and months.',
                    'fields' => [
                        'dateLeadingZero' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('dateLeadingZero', true) ? 'y' : 'n',
                            'attrs' => 'data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Date Separator',
                    'desc'   => 'Used to separate date values.',
                    'fields' => [
                        'dateSeparator' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('dateSeparator', '/'),
                            'choices' => [
                                ''  => 'None',
                                ' ' => 'Space',
                                '/' => '/',
                                '-' => '-',
                                '.' => '.',
                            ],
                            'attrs'   => 'data-datetime-date-group',
                        ],
                    ],
                ],
                [
                    'title'  => '24h clock?',
                    'fields' => [
                        'clock24h' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('clock24h', false) ? 'y' : 'n',
                            'attrs' => 'data-toggle="ampm" data-toggle-reverse data-datetime-time-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Clock Separator',
                    'desc'   => 'Used to separate hours and minutes.',
                    'fields' => [
                        'clockSeparator' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('clockSeparator', ':'),
                            'choices' => [
                                ''  => 'None',
                                ' ' => 'Space',
                                ':' => ':',
                                '-' => '-',
                                '.' => '.',
                            ],
                            'attrs'   => 'data-datetime-time-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Lowercase AM/PM?',
                    'fields' => [
                        'lowercaseAMPM' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('lowercaseAMPM', false) ? 'y' : 'n',
                            'attrs' => 'data-toggle-group="ampm" data-datetime-time-group',
                        ],
                    ],
                ],
                [
                    'title'  => 'Separate AM/PM with a space?',
                    'fields' => [
                        'clockAMPMSeparate' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('clockAMPMSeparate', true) ? 'y' : 'n',
                            'attrs' => 'data-toggle-group="ampm" data-datetime-time-group',
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_WEBSITE            => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_NUMBER             => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
                [
                    'title'  => 'Allow negative numbers?',
                    'fields' => [
                        'allowNegative' => [
                            'type'  => 'yes_no',
                            'value' => $model->getAdditionalProperty('allowNegative', false) ? 'y' : 'n',
                        ],
                    ],
                ],
                [
                    'title'  => 'Min Value',
                    'desc'   => '(Optional) The minimum numeric value this field is allowed to have.',
                    'fields' => [
                        'minValue' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('minValue'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Max Value',
                    'desc'   => '(Optional) The maximum numeric value this field is allowed to have.',
                    'fields' => [
                        'maxValue' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('maxValue'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Min Length',
                    'desc'   => '(Optional) The minimum length of characters the field is allowed to have.',
                    'fields' => [
                        'minLength' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('minLength'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Max Length',
                    'desc'   => '(Optional) The maximum length of characters the field is allowed to have.',
                    'fields' => [
                        'maxLength' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('maxLength'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Decimal Count',
                    'desc'   => 'The number of decimal places allowed.',
                    'fields' => [
                        'decimalCount' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('decimalCount', 0),
                        ],
                    ],
                ],
                [
                    'title'  => 'Decimal Separator',
                    'desc'   => 'Used to separate decimals.',
                    'fields' => [
                        'decimalSeparator' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('decimalSeparator', '.'),
                            'choices' => [
                                '.' => '.',
                                ',' => ',',
                            ],
                        ],
                    ],
                ],
                [
                    'title'  => 'Thousands Separator',
                    'desc'   => 'Used to separate thousands.',
                    'fields' => [
                        'thousandsSeparator' => [
                            'type'    => 'select',
                            'value'   => $model->getAdditionalProperty('thousandsSeparator', ''),
                            'choices' => [
                                ''  => 'None',
                                ' ' => 'Space',
                                ',' => ',',
                                '.' => '.',
                            ],
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_PHONE              => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
                [
                    'title'  => 'Pattern',
                    'desc'   => 'Custom phone pattern (i.e. \'(xxx) xxx-xxxx\'). The letter \'x\' stands for a digit between 0-9. If left blank, any number and dash, dot, space, parentheses and optional + at the beginning will be validated.',
                    'fields' => [
                        'pattern' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('pattern'),
                        ],
                    ],
                ],
            ],
            FieldInterface::TYPE_REGEX              => [
                [
                    'title'  => 'Default Value',
                    'desc'   => 'The default value for the field.',
                    'fields' => [
                        'value' => [
                            'type'  => 'text',
                            'value' => $model->value,
                        ],
                    ],
                ],
                [
                    'title'  => 'Placeholder',
                    'desc'   => 'The default text that will be shown if the field doesn’t have a value.',
                    'fields' => [
                        'placeholder' => [
                            'type'  => 'text',
                            'value' => $model->placeholder,
                        ],
                    ],
                ],
                [
                    'title'  => 'Pattern',
                    'desc'   => 'Enter any regex pattern here.',
                    'fields' => [
                        'pattern' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('pattern'),
                        ],
                    ],
                ],
                [
                    'title'  => 'Error Message',
                    'desc'   => 'The message a user should receive if an incorrect value is given. It will replace any occurrences of \'{pattern}\' with the supplied regex pattern inside the message if any are found.',
                    'fields' => [
                        'message' => [
                            'type'  => 'text',
                            'value' => $model->getAdditionalProperty('message'),
                        ],
                    ],
                ],
            ],
        ];

        if (!file_exists(__DIR__ . '/../Library/Pro')) {
            unset(
                $byType[FieldInterface::TYPE_CONFIRMATION],
                $byType[FieldInterface::TYPE_DATETIME],
                $byType[FieldInterface::TYPE_NUMBER],
                $byType[FieldInterface::TYPE_PHONE],
                $byType[FieldInterface::TYPE_RATING],
                $byType[FieldInterface::TYPE_REGEX],
                $byType[FieldInterface::TYPE_WEBSITE]
            );
        }

        $sectionData = [];
        foreach ($byType as $type => $items) {
            foreach ($items as $data) {
                $fields = [];
                foreach ($data['fields'] as $index => $fieldData) {
                    $fields["types[$type][$index]"] = $fieldData;
                }

                $sectionData[] = [
                    'group'  => $type,
                    'title'  => $data['title'],
                    'desc'   => isset($data['desc']) ? $data['desc'] : '',
                    'fields' => $fields,
                ];
            }
        }

        return $sectionData;
    }

    /**
     * @param FieldModel $model
     * @param string     $template
     *
     * @return string
     */
    private function getFieldHtml(FieldModel $model, $template, $type)
    {
        $singleValue = $type !== FieldInterface::TYPE_CHECKBOX_GROUP;

        ob_start();
        include PATH_THIRD . "freeform_next/Templates/fields/{$template}.php";

        return ob_get_clean();
    }

    /**
     * @return FilesService
     */
    private function getFileService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new FilesService();
        }

        return $instance;
    }
}
