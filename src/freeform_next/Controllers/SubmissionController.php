<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Model\File\File;
use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DynamicRecipientField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\EmailField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\RadioGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SelectField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextareaField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Page;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Row;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\RatingField;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionPreferencesRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
use Solspace\Addons\FreeformNext\Services\ExportService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

class SubmissionController extends Controller
{
    const MAX_PER_PAGE = 20;

    /**
     * @param Form $form
     *
     * @return CpView
     */
    public function index(Form $form)
    {
        $preferences = SubmissionPreferencesRepository::getInstance()->getOrCreate(
            $form,
            ee()->session->userdata('member_id')
        );

        $layout = $preferences->getLayout();

        $page = (int) ee()->input->get('page') ?: 1;

        $sortDirection = ee()->input->get('sort_dir');
        $sortDirection = !$sortDirection || $sortDirection === '0' ? 'desc' : $sortDirection;
        $sortColumn    = ee()->input->get('sort_col');
        $sortColumn    = !$sortColumn || $sortColumn === '0' ? 'dateCreated' : $sortColumn;

        $sortVars = [
            'sort_col' => $sortColumn,
            'sort_dir' => $sortDirection,
        ];


        $attributes = new SubmissionAttributes($form);
        $attributes
            ->setOrderBy($sortColumn)
            ->setSort($sortDirection)
            ->setLimit(self::MAX_PER_PAGE)
            ->setOffset(self::MAX_PER_PAGE * ($page - 1));

        $submissions          = SubmissionRepository::getInstance()->getAllSubmissionsFor($attributes);
        $totalSubmissionCount = SubmissionRepository::getInstance()->getAllSubmissionCountFor($attributes);

        $pagination = ee('CP/Pagination', $totalSubmissionCount)
            ->perPage(self::MAX_PER_PAGE)
            ->currentPage($page)
            ->render(
                $this->getLink('submissions/' . $form->getHandle() . '&' . http_build_query($sortVars))
            );

        $columns = [];
        $index   = 0;
        foreach ($layout as $setting) {
            if (!$setting->isChecked()) {
                continue;
            }

            $type   = Table::COL_TEXT;
            $encode = true;
            if ($setting->getId() === 'id') {
                $type = Table::COL_ID;
            } else if (in_array($setting->getId(), ['title', 'statusName'], true)) {
                $encode = false;
            }

            $handle = $setting->getHandle();
            $label  = $setting->getLabel();
            if (is_numeric($setting->getId())) {
                try {
                    $field  = $form->getLayout()->getFieldById($setting->getId());
                    $handle = $field->getHandle();
                    $label  = $field->getLabel();

                    if ($field->getType() === AbstractField::TYPE_FILE) {
                        $type   = Table::COL_TEXT;
                        $encode = false;
                    }
                } catch (FreeformException $e) {
                    continue;
                }
            }

            // Make sure the labels are "translatable"
            ee()->lang->language[$handle] = $label;

            $columns[] = [
                'label'  => $handle,
                'type'   => $type,
                'encode' => $encode,
                'sort'   => true,
            ];
        }

        $columns = array_merge(
            $columns,
            [
                'manage' => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        /** @var Table $table */
        $table = ee(
            'CP/Table',
            [
                'sortable' => true,
                'search'   => true,
                'limit'    => 5,
            ]
        );

        $table->setColumns($columns);

        ee()->javascript->set_global('file_view_url', ee('CP/URL')->make('files/file/view/###')->compile());
        $dateFormat = ee()->localize->get_date_format();

        $tableData = [];
        foreach ($submissions as $submission) {
            $link = $this->getLink('submissions/' . $form->getHandle() . '/' . $submission->id);
            $data = [];

            foreach ($layout as $setting) {
                if (!$setting->isChecked()) {
                    continue;
                }

                if ($setting->getId() === 'id') {
                    $data[] = $submission->id;
                } else if ($setting->getId() === 'title') {
                    $data[] = [
                        'content' => '<a href="' . $link . '">' . $submission->title . '</a>',
                    ];
                } else if ($setting->getId() === 'statusName') {
                    $data[] = [
                        'content' => '<span class="color-indicator" style="background: ' . $submission->statusColor . ';"></span>' . $submission->statusName,
                    ];
                } else if ($setting->getId() === 'dateCreated') {
                    $data[] = ee()->localize->format_date($dateFormat, strtotime($submission->dateCreated));
                } else if (is_numeric($setting->getId())) {
                    try {
                        $field = $form->getLayout()->getFieldById($setting->getId());
                        $value = $submission->getFieldValueAsString($field->getHandle());

                        if ($field instanceof FileUploadField) {
                            $assetIds = $submission->getFieldValue($field->getHandle());
                            if ($assetIds) {
                                if (!is_array($assetIds)) {
                                    $assetIds = [$assetIds];
                                }

                                $content = '';
                                $content .= '<div class="file-previews">';

                                foreach ($assetIds as $assetId) {
                                    /** @var File $asset */
                                    $asset = ee('Model')
                                        ->get('File')
                                        ->filter('file_id', $assetId)
                                        ->first();

                                    $content .= '<div class="' . ($asset->isImage() ? 'has-img' : '') . '">';
                                    if ($asset->isImage()) {
                                        $content .= '<img src="' . $asset->getAbsoluteURL() . '" />';
                                    }
                                    $content .= '<div>' . $asset->file_name . '</div>';
                                    $content .= '</div>';
                                }

                                $content .= '</div>';

                                $data[] = [
                                    'content' => $content,
                                ];
                            } else {
                                $data[] = ['toolbar_items' => []];
                            }
                        } elseif ($field instanceof RatingField) {
                            $data[] = (int) $value . '/' . $field->getMaxValue();
                        } else {
                            $data[] = $value;
                        }
                    } catch (FreeformException $e) {
                        continue;
                    }
                }
            }

            $data[] = [
                'toolbar_items' => [
                    'edit' => [
                        'href'  => $this->getLink('submissions/' . $form->getHandle() . '/' . $submission->id),
                        'title' => lang('edit'),
                    ],
                ],
            ];

            $data[] = [
                'name'  => 'id_list[]',
                'value' => $submission->id,
                'data'  => [
                    'confirm' => lang('Submission') . ': <b>' . htmlentities($submission->title, ENT_QUOTES) . '</b>',
                ],
            ];

            $tableData[] = $data;
        }

        if (empty($tableData) || count($tableData[0]) === count($columns)) {
            $table->setData($tableData);
            $table->setNoResultsText('No results');
        } else {
            $table->setData([]);
            $table->setNoResultsText('Please re-save the column layout');
        }

        $modal = new ConfirmRemoveModal($this->getLink('submissions/' . $form->getHandle() . '/delete'));
        $modal->setKind('Submissions');

        $formRightLinks = [
            [
                'title' => lang('Edit Layout'),
                'link'  => '#',
                'attrs' => 'id="change-layout-trigger"',
            ],
        ];

        if (class_exists('Solspace\Addons\FreeformNext\Controllers\ExportController')) {
            array_unshift($formRightLinks, [
                'title' => lang('Quick Export'),
                'link'  => '#',
                'attrs' => 'id="quick-export-trigger" style="margin-right: 5px;"',
            ]);
        } else {
            array_unshift($formRightLinks, [
                'title' => lang('Export CSV'),
                'link'  => $this->getLink('api/submission_export/' . $form->getId()),
                'attrs' => 'id="export-trigger" style="margin-right: 5px;"',
            ]);
        }


        $view = new CpView(
            'submissions/listing',
            [
                'table'            => $table->viewData($this->getLink('submissions/' . $form->getHandle())),
                'cp_page_title'    => 'Submissions for ' . $form->getName(),
                'layout'           => $layout,
                'form'             => $form,
                'form_right_links' => $formRightLinks,
                'pagination'       => $pagination,
                'exportLink'       => $this->getLink('export'),
            ]
        );

        $exportServiceClassName = 'Solspace\Addons\FreeformNext\Services\ExportService';
        if (class_exists($exportServiceClassName)) {
            $exportService = new $exportServiceClassName();
            $view->addTemplateVariables($exportService->getExportDialogueTemplateVariables($form->getId()));
        }

        $view
            ->setHeading(lang('Submissions'))
            ->addJavascript('submissions')
            ->addJavascript('export')
            ->addJavascript('lib/featherlight.min.js')
            ->addBreadcrumb(new NavigationLink('Forms', 'forms'))
            ->addBreadcrumb(new NavigationLink($form->getName(), 'forms/' . $form->getId()))
            ->addModal($modal);

        return $view;
    }

    /**
     * @param Form            $form
     * @param SubmissionModel $submission
     *
     * @return CpView
     */
    public function edit(Form $form, SubmissionModel $submission)
    {
        $view = new CpView('submissions/edit');

        $sectionData = [
            [
                [
                    'title'  => lang('Title'),
                    'fields' => [
                        'title' => [
                            'type'     => 'text',
                            'value'    => $submission->title,
                            'required' => true,
                        ],
                    ],
                ],
                [
                    'title'  => lang('Status'),
                    'fields' => [
                        'statusId' => [
                            'type'     => 'select',
                            'value'    => $submission->statusId,
                            'required' => true,
                            'choices'  => StatusRepository::getInstance()->getStatusNamesById(),
                        ],
                    ],
                ],
            ],
        ];

        /** @var Page $page */
        foreach ($form->getPages() as $page) {
            $data = [];

            foreach ($form->getLayout()->getHiddenFields() as $field) {
                if ($field->getPageIndex() === $page->getIndex()) {
                    $fields = [
                        $field->getHandle() => [
                            'type'     => 'text',
                            'value'    => $submission->getFieldValue($field->getHandle()),
                            'required' => $field->isRequired(),
                        ],
                    ];

                    $data[] = [
                        'title'  => $field->getLabel(),
                        'fields' => $fields,
                    ];
                }
            }

            /** @var Row $row */
            foreach ($page as $row) {


                /** @var AbstractField $field */
                foreach ($row as $field) {
                    if ($field instanceof NoStorageInterface) {
                        continue;
                    }

                    $handle     = $field->getHandle();
                    $value      = $submission->getFieldValue($handle);
                    $isRequired = $field->isRequired();

                    if ($field instanceof CheckboxGroupField) {
                        $fields = [
                            $handle => [
                                'type'     => 'checkbox',
                                'value'    => $value,
                                'required' => $isRequired,
                                'choices'  => $field->getOptionsAsArray(),
                            ],
                        ];
                    } else if ($field instanceof CheckboxField) {
                        $fields = [
                            $handle => [
                                'type'     => 'checkbox',
                                'value'    => $value,
                                'required' => $isRequired,
                                'choices'  => [$field->getValue() => $field->getLabel()],
                            ],
                        ];
                    } else if ($field instanceof SelectField || ($field instanceof DynamicRecipientField && !$field->isShowAsRadio(
                            ))
                    ) {
                        $fields = [
                            $handle => [
                                'type'     => 'select',
                                'value'    => $value,
                                'required' => $isRequired,
                                'choices'  => $field->getOptionsAsArray(),
                            ],
                        ];
                    } else if ($field instanceof RadioGroupField || ($field instanceof DynamicRecipientField && $field->isShowAsRadio(
                            ))
                    ) {
                        $fields = [
                            $handle => [
                                'type'     => 'radio',
                                'value'    => $value,
                                'required' => $isRequired,
                                'choices'  => $field->getOptionsAsArray(),
                            ],
                        ];
                    } else if ($field instanceof TextareaField) {
                        $fields = [
                            $handle => [
                                'type'     => 'textarea',
                                'value'    => $value,
                                'required' => $isRequired,
                            ],
                        ];
                    } else if ($field instanceof FileUploadField) {
                        $assetIds = $submission->getFieldValue($field->getHandle());

                        $content = '';
                        if ($assetIds) {
                            if (!is_array($assetIds)) {
                                $assetIds = [$assetIds];
                            }

                            $content .= '<div class="file-previews">';

                            foreach ($assetIds as $assetId) {
                                /** @var File $asset */
                                $asset = ee('Model')
                                    ->get('File')
                                    ->filter('file_id', $assetId)
                                    ->first();

                                $content .= '<div>';
                                $content .= '<div style="margin: 5px 0;">' . $asset->file_name . '</div>';
                                $content .= '<div class="toolbar-wrap"><ul class="toolbar">';
                                $content .= '<li class="edit"><a href="' . ee(
                                        'CP/URL',
                                        'cp/files/file/edit/' . $assetId
                                    )->compile() . '"></a></li>';
                                $content .= '<li class="download"><a href="' . ee(
                                        'CP/URL',
                                        'files/file/download/' . $assetId
                                    )->compile() . '"></a></li>';
                                $content .= '</ul></div>';

                                if ($asset->isImage()) {
                                    $content .= '<img style="border: 1px solid black; padding: 1px;" width="100" src="'
                                        . $asset->getAbsoluteURL() . '" />';
                                }
                                $content .= '</div>';
                            }

                            $content .= '</div>';
                        }

                        $fields = [
                            [
                                'type'    => 'html',
                                'content' => $content,
                            ],
                        ];
                    } else if ($field instanceof EmailField) {
                        $fields = [];

                        /** @var array $value */
                        if (is_array($value)) {
                            foreach ($value as $val) {
                                $fields[$handle . '[]'] = [
                                    'type'  => 'text',
                                    'value' => $val,
                                ];
                            }
                        }
                    } else {
                        $fields = [
                            $handle => [
                                'type'     => 'text',
                                'value'    => $value,
                                'required' => $isRequired,
                            ],
                        ];
                    }


                    $data[] = [
                        'title'  => $field->getLabel(),
                        'fields' => $fields,
                    ];
                }
            }

            $sectionData[$page->getLabel()] = $data;
        }

        $view
            ->setHeading($submission->title)
            ->addBreadcrumb(new NavigationLink('Forms', 'forms'))
            ->addBreadcrumb(new NavigationLink($form->getName(), 'forms/' . $form->getId()))
            ->addBreadcrumb(new NavigationLink('Submissions', 'submissions/' . $form->getHandle()))
            ->setTemplateVariables(
                [
                    'base_url'              => $this->getLink(
                        'submissions/' . $form->getHandle() . '/' . $submission->id
                    ),
                    'cp_page_title'         => $submission->title,
                    'save_btn_text'         => 'Save',
                    'save_btn_text_working' => 'Saving...',
                    'sections'              => $sectionData,
                ]
            );

        return $view;
    }

    /**
     * @param Form            $form
     * @param SubmissionModel $submission
     *
     * @return bool
     */
    public function save(Form $form, SubmissionModel $submission)
    {
        $submission->title    = ee()->input->post('title', true);
        $submission->statusId = ee()->input->post('statusId', StatusRepository::getInstance()->getDefaultStatusId());

        foreach ($form->getLayout()->getFields() as $field) {
            if ($field instanceof NoStorageInterface || $field instanceof FileUploadField) {
                continue;
            }

            $value = ee()->input->post($field->getHandle(), true);

            if ($field instanceof CheckboxField && is_array($value)) {
                $value = reset($value);
            }

            $submission->setFieldValue($field->getHandle(), $value);
        }

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_BEFORE_SAVE, $submission, false)) {

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->withTitle(lang('Failed'))
                ->defer();

            return false;
        }

        $submission->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_AFTER_SAVE, $submission, false);

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('Success'))
            ->defer();

        return true;
    }

    /**
     * @param Form $form
     *
     * @return RedirectView
     */
    public function batchDelete(Form $form)
    {
        if (isset($_POST['id_list'])) {
            $ids = [];
            foreach ($_POST['id_list'] as $id) {
                $ids[] = (int) $id;
            }

            $models = SubmissionRepository::getInstance()->getSubmissionsByIdList($ids);

            foreach ($models as $model) {

                if (!ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_SUBMISSION_AFTER_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink('submissions/' . $form->getHandle()));
    }
}
