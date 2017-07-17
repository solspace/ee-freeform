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

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FieldException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
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

        $view = new CpView(
            'fields/listing',
            [
                'table'            => $table->viewData(),
                'cp_page_title'    => lang('Fields'),
                'form_right_links' => [
                    [
                        'title' => lang('New Field'),
                        'link'  => $this->getLink('fields/new'),
                    ],
                ],
            ]
        );
        $view->setHeading(lang('Fields'));
        $view->addModal((new ConfirmRemoveModal($this->getLink('fields/delete')))->setKind('Fields'));

        return $view;
    }

    /**
     * @param int|null $id
     *
     * @return CpView
     * @throws FieldException
     */
    public function edit($id)
    {
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
            'settings' => $this->getFieldSettingsByType($model),
        ];

        ee()->cp->add_js_script(['file' => ['cp/form_group']]);

        $view = new CpView(
            'fields/edit',
            [
                'sections'              => $sections,
                'cp_page_title'         => lang('Field'),
                'base_url'              => $this->getLink('fields/' . $id),
                'save_btn_text'         => lang('Save'),
                'save_btn_text_working' => lang('Saving...'),
                'countryOptions'        => include __DIR__ . '/../countries.php',
            ]
        );
        $view
            ->setHeading($model->label ?: lang('New Field'))
            ->addBreadcrumb(new NavigationLink('Fields', 'fields'))
            ->addJavascript('fieldEditor')
            ->addJavascript('handleGenerator');

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
        $isNew = !$field->id;

        $post        = $_POST;
        $type        = isset($_POST['type']) ? $_POST['type'] : $field->type;
        $validValues = [];
        foreach ($post as $key => $value) {
            if (property_exists($field, $key)) {
                $validValues[$key] = $value;
            }
        }

        if (isset($validValues['required'])) {
            $validValues['required'] = $validValues['required'] === 'y';
        }

        if (isset($validValues['checked'])) {
            $validValues['checked'] = $validValues['checked'] === 'y';
        }

        $fieldHasOptions = in_array(
            $type,
            [
                FieldInterface::TYPE_RADIO_GROUP,
                FieldInterface::TYPE_CHECKBOX_GROUP,
                FieldInterface::TYPE_SELECT,
                FieldInterface::TYPE_DYNAMIC_RECIPIENTS,
            ],
            true
        );

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

        if ($type === FieldInterface::TYPE_FILE && !isset($validValues['fileKinds'])) {
            $validValues['fileKinds'] = [];
        }

        $field->set($validValues);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_BEFORE_SAVE, $field, $isNew)) {
            return $field;
        }

        $field->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_FIELD_AFTER_SAVE, $field, $isNew);

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('Success'))
            ->defer();

        return $field;
    }

    /**
     * @return RedirectView
     */
    public function batchDelete()
    {
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
        ];

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
                    'desc'   => $data['desc'],
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
