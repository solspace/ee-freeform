<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
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
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\MailingListField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\MultipleSelectField;
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
use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionPreferencesRepository;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;
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
        $canAccessSubmissions = $this->getPermissionsService()->canAccessSubmissions(ee()->session->userdata('group_id'));

        if (!$canAccessSubmissions) {
            return new RedirectView($this->getLink('denied'));
        }

        $baseUrl = ee('CP/URL')->getCurrentUrl();//ee('CP/URL', 'addons/settings/freeform_next/submissions/' . $form->getHandle());
        $filters = ee('CP/Filter')->add('Date');

        $canManageSubmissions = $this->getPermissionsService()->canManageSubmissions(ee()->session->userdata('group_id'));

        $columnLabels   = [];
        $visibleColumns = [];

        $preferences = SubmissionPreferencesRepository::getInstance()->getOrCreate(
            $form,
            ee()->session->userdata('member_id')
        );

        $layout = $preferences->getLayout();

        $columns = [];
        $index   = 0;

        foreach ($layout as $setting) {

            $fieldType = null;

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
                    $field     = $form->getLayout()->getFieldById($setting->getId());
                    $handle    = $field->getHandle();
                    $label     = $field->getLabel();
                    $fieldType = $field->getType();

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

            $fieldId = $setting->getId();

            if (is_int($fieldId)) {

                if (!in_array($fieldType, $this->getFilterableFieldTypes())) {
                    continue;
                }

                $fieldId = 'field_' . $fieldId;
            }

            if (!in_array($fieldId, ['statusName', 'dateCreated', 'dateUpdated'])) {
                $visibleColumns[]       = $fieldId;
                $columnLabels[$fieldId] = $label;
            }
        }

        if ($canManageSubmissions) {
            $columns = array_merge(
                $columns,
                [
                    'manage' => ['type' => Table::COL_TOOLBAR],
                    ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
                ]
            );
        }

        $attributes = new SubmissionAttributes($form);

        $currentKeyword        = '';
        $currentSearchStatus   = '';
        $currentSearchStatusId = '';
        $currentDateRangeStart = '';
        $currentDateRangeEnd   = '';
        $currentDateRange      = '';
        $currentSearchOnField  = '';

        $statuses     = StatusRepository::getInstance()->getAllStatuses();
        $formStatuses = [];

        foreach ($statuses as $status) {
            $formStatuses[$status->id] = [
            	'label' => $status->name,
            	'url' => ee('CP/URL')->getCurrentUrl()->addQueryStringVariables(['status' => $status->id])->compile()
				];
        }

        $search_vars = [
            'keywords',
            'status',
            'date_range',
            'date_range_start',
            'date_range_end',
            'search_on_field',
        ];

        $searchVars = [];

        foreach ($search_vars as $searchVarible) {
            $searchValue                = ee()->input->get_post($searchVarible, true);
            $searchVars[$searchVarible] = trim($searchValue);
        }

        $searchOnField  = $searchVars['search_on_field'];
		$searchKeywords = $searchVars['keywords'];

        if (($searchOnField == '' OR in_array($searchOnField, $visibleColumns)) AND $searchKeywords AND trim($searchKeywords) !== '') {

            $currentSearchOnField = $searchOnField;

            if ($searchOnField === '') {
                foreach ($visibleColumns as $column) {
                    $attributes->addOrLikeFilter($column, $searchKeywords);
                }

            } else if ($searchOnField == 'id') {
                $attributes->addIdFilter($searchOnField, $searchKeywords);
            } else {
                $attributes->addLikeFilter($searchOnField, $searchKeywords);
            }

            $currentKeyword = $searchKeywords;
        }

        $searchStatus = $searchVars['status'];

        if ($searchStatus AND in_array($searchStatus, array_keys($formStatuses))) {
            $currentSearchStatus = $formStatuses[$searchStatus]['label'];
            $currentSearchStatusId = $searchStatus;
            $attributes->addFilter('statusId', $searchStatus);
        }

        $dateRangesKeys = [
			'today',
			'this_week',
			'this_month',
			'last_month',
			'this_year',
		];

		$formDateRanges = [];

		foreach ($dateRangesKeys as $dateRangeKey) {
			$formDateRanges[] = [
				'label' => lang($dateRangeKey),
				'url' => ee('CP/URL')->getCurrentUrl()->addQueryStringVariables(['date_range' => $dateRangeKey])->compile()
			];
		}

        $dateRange = str_replace('_', ' ', $searchVars['date_range']);
        $currentDateRange = $searchVars['date_range'];

        if ($dateRange !== 'date range') {
            $attributes->setDateRange($dateRange);
        } else {
            $dateRangeStart = $searchVars['date_range_start'];

            if ($dateRangeStart) {
                $currentDateRangeStart = $dateRangeStart;
                $dateRangeStart        = date($dateRangeStart);
                $attributes->setDateRangeStart($dateRangeStart);
            }

            $dateRangeEnd = $searchVars['date_range_end'];

            if ($dateRangeEnd) {
                $currentDateRangeEnd = $dateRangeEnd;
                $dateRangeEnd        = date($dateRangeEnd);
                $attributes->setDateRangeEnd($dateRangeEnd);
            }
        }

        $page = (int) ee()->input->get('page') ?: 1;

        $sortDirection = ee()->input->get('sort_dir');
        $sortDirection = !$sortDirection || $sortDirection === '0' ? 'desc' : $sortDirection;
        $sortColumn    = ee()->input->get('sort_col');
        $sortColumn    = !$sortColumn || $sortColumn === '0' ? 'dateCreated' : $sortColumn;

        $sortVars = [
            'sort_col' => $sortColumn,
            'sort_dir' => $sortDirection,
        ];

        $totalSubmissionCount = SubmissionRepository::getInstance()->getAllSubmissionCountFor($attributes);

        $attributes
            ->setOrderBy($sortColumn)
            ->setSort($sortDirection)
            ->setLimit(self::MAX_PER_PAGE)
            ->setOffset(self::MAX_PER_PAGE * ($page - 1));

        $submissions = SubmissionRepository::getInstance()->getAllSubmissionsFor($attributes);

        $pagination = ee('CP/Pagination', $totalSubmissionCount)
            ->perPage(self::MAX_PER_PAGE)
            ->currentPage($page)
            ->render(
                $this->getLink('submissions/' . $form->getHandle() . '&' . http_build_query($sortVars) . '&' . http_build_query($searchVars))
            );

        /** @var Table $table */
        $table = ee(
            'CP/Table',
            [
                'autosearch' => true,
                'sortable'   => true,
                'limit'      => 5,
            ]
        );

        $table->setColumns($columns);

        ee()->javascript->set_global('file_view_url', ee('CP/URL')->make('files/file/view/###')->compile());
        $dateFormat = ee()->localize->get_date_format();

        $tableData = [];
        foreach ($submissions as $submission) {
            $link = $this->getLink('submissions/' . $form->getHandle() . '/' . $submission->id);
            $data = [];

            $titleElement = '<p style="margin: 0">' . $submission->title . '</p>';

            if ($canManageSubmissions) {
                $titleElement = '<a href="' . $link . '">' . $submission->title . '</a>';
            }

            foreach ($layout as $setting) {
                if (!$setting->isChecked()) {
                    continue;
                }

                if ($setting->getId() === 'id') {
                    $data[] = $submission->id;
                } else if ($setting->getId() === 'title') {
                    $data[] = [
                        'content' => $titleElement,
                    ];
                } else if ($setting->getId() === 'statusName') {
                    $data[] = [
                        'content' => '<span class="color-indicator" style="background: ' . $submission->statusColor . ';"></span>' . $submission->statusName,
                    ];
                } else if ($setting->getId() === 'dateCreated') {
                    $data[] = ee()->localize->format_date($dateFormat, strtotime($submission->dateCreated));
                } else if (is_numeric($setting->getId())) {
                    try {
                        $field = $form->getLayout()->getFieldById((int) $setting->getId());

                        try {
                            $value = $submission->getFieldValueAsString($field->getHandle());
                        } catch (FreeformException $e) {
                            $value = '';
                        }

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

                                    if ($asset) {
                                        $content .= '<div class="' . ($asset->isImage() ? 'has-img' : '') . '">';
                                        if ($asset->isImage()) {
                                            $content .= '<img src="' . $asset->getAbsoluteURL() . '" />';
                                        }
                                        $content .= '<div>' . $asset->file_name . '</div>';
                                        $content .= '</div>';
                                    }
                                }

                                $content .= '</div>';

                                $data[] = [
                                    'content' => $content,
                                ];
                            } else {
                                $data[] = ['toolbar_items' => []];
                            }
                        } else if ($field instanceof RatingField) {
                            $data[] = (int) $value . '/' . $field->getMaxValue();
                        } else {
                            $data[] = $value;
                        }
                    } catch (FreeformException $e) {
                        continue;
                    }
                }
            }

            $toolbarItems = [];

            if ($canManageSubmissions) {
                $toolbarItems = [
                    'edit' => [
                        'href'  => $this->getLink('submissions/' . $form->getHandle() . '/' . $submission->id),
                        'title' => lang('edit'),
                    ],
                ];
            }

            if ($canManageSubmissions) {
                $data[] = [
                    'toolbar_items' => $toolbarItems,
                ];

                $data[] = [
                    'name'  => 'id_list[]',
                    'value' => $submission->id,
                    'data'  => [
                        'confirm' => lang('Submission') . ': <b>' . htmlentities($submission->title, ENT_QUOTES) . '</b>',
                    ],
                ];
            }

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
                'attrs' => 'id="change-layout-trigger" class="btn action button--small"',
            ],
        ];

        if (class_exists('Solspace\Addons\FreeformNext\Controllers\ExportController')) {
            array_unshift($formRightLinks, [
                'title' => lang('Quick Export'),
                'link'  => '#',
                'attrs' => 'id="quick-export-trigger" style="margin-right: 5px;"  class="btn action button--small"',
            ]);
        } else {
            array_unshift($formRightLinks, [
                'title' => lang('Export CSV'),
                'link'  => $this->getLink('api/submission_export/' . $form->getId()),
                'attrs' => 'id="export-trigger" style="margin-right: 5px;"',
            ]);
        }

        $sessionType  = ee()->config->item('cp_session_type');
        $sessionToken = null;
        if ($sessionType === 'cs') {
            $sessionToken = ee()->session->userdata('fingerprint');
        } else if ($sessionType === 's') {
            $sessionToken = ee()->session->userdata('session_id');
        }

        $template = [
			'table'            => $table->viewData($this->getLink('submissions/' . $form->getHandle())),
			'cp_page_title'    => 'Submissions for ' . $form->getName(),
			'layout'           => $layout,
			'form'             => $form,
			'form_right_links' => $formRightLinks,
			'pagination'       => $pagination,
			'exportLink'       => $this->getLink('export'),
			'formStatuses'     => $formStatuses,
			'formDateRanges'   => $formDateRanges,
			'mainUrl'          => $this->getLink('submissions/' . $form->getHandle()),
			'columnLabels'     => $columnLabels,
			'visibleColumns'   => $visibleColumns,
			'form_url' => ee('CP/URL')->getCurrentUrl(),
			'form_attributes' => [
				'id' => 'entry-filters',
				'data-action' => isset($entries_filter_uri) ? $entries_filter_uri : ""
			],
			'currentSearchOnField'  => $currentSearchOnField,
			'currentKeyword'        => $currentKeyword,
			'currentSearchStatus'   => $currentSearchStatus,
			'currentSearchStatusId' => $currentSearchStatusId,
			'currentDateRangeStart' => $currentDateRangeStart,
			'currentDateRangeEnd'   => $currentDateRangeEnd,
			'currentDateRange'      => $currentDateRange,

			'baseUrl' => $baseUrl,
			'filters' => $filters,

			'sessionToken' => $sessionToken,
			'footer' => [
				'submit_lang' => lang('submit'),
				'type'        => 'bulk_action_form',
			]
		];

        $view = new CpView('submissions/listing', $template);

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
        $canManageSubmissions = $this->getPermissionsService()->canManageSubmissions(ee()->session->userdata('group_id'));

        if (!$canManageSubmissions) {
            return new RedirectView($this->getLink('denied'));
        }

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
                if ($field instanceof MailingListField) {
                    continue;
                }

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
                    } else if ($field instanceof MultipleSelectField) {
                        $fields = [
                            $handle => [
                                'type'     => 'select',
                                'value'    => $value,
                                'required' => $isRequired,
                                'multiple' => true,
                                'choices'  => $field->getOptionsAsArray(),
                            ],
                        ];
                    } else if ($field instanceof CheckboxField) {
                        $fields = [
                            $handle => [
                                'type'     => 'checkbox',
                                'value'    => $value ? $field->getStaticValue() : '',
                                'required' => $isRequired,
                                'choices'  => [$field->getStaticValue() => $field->getLabel()],
                            ],
                        ];
                    } else if ($field instanceof SelectField || ($field instanceof DynamicRecipientField && !$field->isShowAsRadio())
                    ) {
                        $fields = [
                            $handle => [
                                'type'     => 'select',
                                'value'    => $value,
                                'required' => $isRequired,
                                'choices'  => $field->getOptionsAsArray(),
                            ],
                        ];
                    } else if ($field instanceof RadioGroupField || ($field instanceof DynamicRecipientField && $field->isShowAsRadio())
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

                                if (!$asset) {
                                    continue;
                                }

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
                    } else if ($field instanceof TableField) {
                        $field->setAddButtonMarkup('<ul class="toolbar"><li class="add"><a title="' . lang('add_row') . '" class="add button button--default button--small form-table-add-row"> ' . lang('add_row') . '</a></li></ul>');
                        $field->setRemoveButtonMarkup('<ul class="toolbar"><li class="remove"><a title="' . lang('remove_row') . '" class="remove button button--default button--small form-table-remove-row"> ' . lang('remove_row') . '</a></li></ul>');
                        $field->setValue($submission->getFieldValue($field->getHandle()));
                        $output = $field->renderInput();

                        $fields = [
                            [
                                'type'    => 'html',
                                'content' => $output,
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
            ->addJavascript('fields/table.js')
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
        $canManageSubmissions = $this->getPermissionsService()->canManageSubmissions(ee()->session->userdata('group_id'));

        if (!$canManageSubmissions) {
            return false;
        }

        $submission->title    = ee()->input->post('title', true);
        $submission->statusId = ee()->input->post('statusId', StatusRepository::getInstance()->getDefaultStatusId());

        foreach ($form->getLayout()->getFields() as $field) {
            if ($field instanceof NoStorageInterface || $field instanceof FileUploadField) {
                continue;
            }

            $value = ee()->input->post($field->getHandle(), true);

            if ($field instanceof CheckboxField) {
                if (is_array($value)) {
                    $value = reset($value);
                }

                $value = $value ?: '';
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
        $canManageSubmissions = $this->getPermissionsService()->canManageSubmissions(ee()->session->userdata('group_id'));

        if (!$canManageSubmissions) {
            return new RedirectView($this->getLink('denied'));
        }

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

    private function getFilterableFieldTypes()
    {
        return [
            AbstractField::TYPE_EMAIL,
            AbstractField::TYPE_NUMBER,
            AbstractField::TYPE_PHONE,
            AbstractField::TYPE_TEXT,
            AbstractField::TYPE_TEXTAREA,
            AbstractField::TYPE_HIDDEN,
            AbstractField::TYPE_CHECKBOX,
            AbstractField::TYPE_REGEX,
            AbstractField::TYPE_SELECT,
            AbstractField::TYPE_WEBSITE,
        ];
    }
}
