<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use ExpressionEngine\Library\CP\Table;
use ExpressionEngine\Service\Validation\Result;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Model\ExportProfileModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\ExportProfilesRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Services\ExportProfilesService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;

class ExportProfilesController extends Controller
{
    /**
     * @return CpView
     */
    public function index()
    {
        $forms = FormRepository::getInstance()->getAllForms();

        $exportProfileRepository = ExportProfilesRepository::getInstance();
        $exportProfiles          = $exportProfileRepository->getAllProfiles();

        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $table->setColumns(
            [
                'id'          => ['type' => Table::COL_ID],
                'Name'        => ['type' => Table::COL_TEXT],
                'Form'        => ['type' => Table::COL_TEXT],
                'Submissions' => ['type' => Table::COL_TEXT],
                'manage'      => ['type' => Table::COL_TOOLBAR],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $tableData = [];
        foreach ($exportProfiles as $profile) {
            $tableData[] = [
                $profile->id,
                [
                    'content' => $profile->name,
                    'href'    => $this->getLink('export_profiles/' . $profile->id),
                ],
                $profile->getFormModel()->name,
                $profile->getSubmissionCount(),
                [
                    'toolbar_items' => [
                        'csv'  => [
                            'href'  => $this->getLink('export_profiles/csv/' . $profile->id),
                            'content' => lang('CSV'),
                        ],
                        'json' => [
                            'href'  => $this->getLink('export_profiles/json/' . $profile->id),
                            'content' => lang('JSON'),
                        ],
                        'xml'  => [
                            'href'  => $this->getLink('export_profiles/xml/' . $profile->id),
                            'content' => lang('XML'),
                        ],
                        'text' => [
                            'href'  => $this->getLink('export_profiles/text/' . $profile->id),
                            'content' => lang('Text'),
                        ],
                        'edit' => [
                            'href'  => $this->getLink('export_profiles/' . $profile->id),
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                [
                    'name'  => 'id_list[]',
                    'value' => $profile->id,
                    'data'  => [
                        'confirm' => lang('Export Profile') . ': <b>' . htmlentities(
                                $profile->name,
                                ENT_QUOTES
                            ) . '</b>',
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $formRightLinks = [];
        foreach ($forms as $form) {
            $formRightLinks[] = [
                'title' => $form->name,
                'link'  => $this->getLink('export_profiles/new/' . $form->handle),
            ];
        }

        $template = [
			'table'               => $table->viewData(),
			'cp_page_title'       => lang('Export Profiles'),
			'form_dropdown_links' => [
				'Create Export Profile' => $formRightLinks,
			],
			'footer' => [
				'submit_lang' => lang('submit'),
				'type'        => 'bulk_action_form',
			]
		];

        $view = new CpView('export_profiles/listing', $template);

        $view
            ->setHeading(lang('Export Profiles'))
            ->addJavascript('export-profiles')
            ->addModal(
                (new ConfirmRemoveModal($this->getLink('export_profiles/delete')))
                    ->setKind('Export Profiles')
            );

        return $view;
    }

    /**
     * @param int|string  $profileId
     * @param string      $formHandle
     * @param Result|null $validation
     *
     * @return CpView
     * @throws FreeformException
     */
    public function edit($profileId, $formHandle, Result $validation = null)
    {
        $profile = ExportProfilesRepository::getInstance()->getProfileById($profileId);

        if ($profile) {
            $form = $profile->getFormModel();
        } else {
            $form = FormRepository::getInstance()->getFormByIdOrHandle($formHandle);
        }

        $statuses = StatusRepository::getInstance()->getStatusNamesById();

        if (!$form) {
            throw new FreeformException('Could not find form');
        }

        if (strtolower($profileId) === 'new') {
            $profile = ExportProfileModel::create($form->getForm());
        }

        if (!$profile) {
            throw new FreeformException("Notification doesn't exist");
        }

        $view = new CpView('export_profiles/edit');
        $view
            ->setHeading($profile->name ?: lang('New Export Profile'))
            ->addBreadcrumb(new NavigationLink('Export Profiles', 'export_profiles'))
            ->addJavascript('export-profiles')
            ->setTemplateVariables(
                [
                    'errors'                => $validation,
                    'cp_page_title'         => 'Export Profiles',
                    'base_url'              => $this->getLink("export_profiles/$profileId/$formHandle"),
                    'save_btn_text'         => 'Save',
                    'save_btn_text_working' => 'Saving',
                    'sections'              => [
                        [
                            [
                                'title'  => 'Name',
                                'desc'   => 'What this export profile will be called in the CP.',
                                'fields' => [
                                    'name'   => [
                                        'type'     => 'text',
                                        'value'    => $profile->name,
                                        'required' => true,
                                        'attrs'    => 'data-generator-base',
                                    ],
                                    'formId' => [
                                        'type'  => 'hidden',
                                        'value' => $form->id,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Statuses',
                                'desc'   => 'Select which statuses to use',
                                'fields' => [
                                    'export-statuses' => [
                                        'type'    => 'checkbox',
                                        'choices' => $statuses,
                                        'value'   => $profile->statuses,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Date Range',
                                'desc'   => 'The date range for fetching submissions',
                                'fields' => [
                                    'dateRange' => [
                                        'type'    => 'select',
                                        'choices' => [
                                            ''          => 'None',
                                            'today'     => 'Today',
                                            'yesterday' => 'Yesterday',
                                            7           => 'Last 7 days',
                                            30          => 'Last 30 days',
                                            365         => 'Last 365 days',
                                        ],
                                        'value'   => $profile->dateRange,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Limit',
                                'desc'   => 'Maximum number of submissions to fetch.',
                                'fields' => [
                                    'limit' => [
                                        'type'  => 'text',
                                        'value' => $profile->limit,
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Fields to export',
                                'desc'   => 'Specify the fields you wish to export and their order.',
                                'fields' => [
                                    'fields' => [
                                        'type'    => 'html',
                                        'content' => $this->getFieldExportTemplate($profile),
                                    ],
                                ],
                            ],
                            [
                                'title'  => 'Filters',
                                'desc'   => 'Add filters to narrow down your results',
                                'wide'   => true,
                                'fields' => [
                                    'filters' => [
                                        'type'    => 'html',
                                        'content' => $this->getFiltersTemplate($profile),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        return $view;
    }

    /**
     * @param int|string $id
     *
     * @return bool
     * @throws FreeformException
     */
    public function save($id)
    {
        $formId = ee()->input->post('formId');
        $form   = FormRepository::getInstance()->getFormById($formId);

        if (!$form) {
            throw new FreeformException('Form not found');
        }

        if ($id === 'new') {
            $profile = ExportProfileModel::create($form->getForm());
        } else {
            $profile = ExportProfilesRepository::getInstance()->getProfileById($id);
        }

        if (!$profile) {
            throw new FreeformException('Profile not found');
        }

        $profile->name      = ee()->input->post('name');
        $profile->formId    = $formId;
        $profile->limit     = ee()->input->post('limit') ?: null;
        $profile->dateRange = ee()->input->post('dateRange') ?: null;
        $profile->fields    = ee()->input->post('fieldSettings') ?: [];
        $profile->filters   = ee()->input->post('filters') ?: [];
        $profile->statuses  = ee()->input->post('statuses') ?: [];
        $profile->save();

        return true;
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

            $models = ExportProfilesRepository::getInstance()->getProfilesByIdList($ids);

            foreach ($models as $model) {
                $model->delete();
            }
        }

        return new RedirectView($this->getLink('export_profiles'));
    }

    /**
     * @param int    $profileId
     * @param string $type
     *
     * @throws \Exception
     */
    public function export($profileId, $type)
    {
        $profile = ExportProfilesRepository::getInstance()->getProfileById($profileId);

        if (!$profile) {
            throw new \Exception(sprintf('Profile with ID %d not found', $profileId));
        }

        $form = $profile->getFormModel()->getForm();
        $data = $profile->getSubmissionData();

        switch ($type) {
            case 'json':
                $this->getExportProfileService()->exportJson($form, $data);
                break;

            case 'xml':
                $this->getExportProfileService()->exportXml($form, $data);
                break;

            case 'text':
                $this->getExportProfileService()->exportText($form, $data);
                break;

            case 'csv':
            default:
                $labels = [];
                foreach ($profile->getFieldSettings() as $id => $item) {
                    if (!$item['checked']) {
                        continue;
                    }
                    $labels[$id] = $item['label'];
                }

                $this->getExportProfileService()->exportCsv($form, $labels, $data);
        }
    }

    /**
     * @param ExportProfileModel $profile
     *
     * @return string
     */
    private function getFieldExportTemplate(ExportProfileModel $profile)
    {
        ob_start();
        include __DIR__ . '/../View/export_profiles/fieldSettings.php';

        return ob_get_clean();
    }

    /**
     * @param ExportProfileModel $profile
     *
     * @return string
     */
    private function getFiltersTemplate(ExportProfileModel $profile)
    {
        ob_start();
        include __DIR__ . '/../View/export_profiles/filters.php';

        return ob_get_clean();
    }

    /**
     * @param int  $id
     * @param Form $form
     *
     * @return ExportProfileModel
     */
    private function getNewOrExistingProfile($id, Form $form)
    {
        $profile = ExportProfilesRepository::getInstance()->getProfileById($id);

        if (!$profile) {
            $profile = ExportProfileModel::create($form);
        }

        return $profile;
    }

    /**
     * @return ExportProfilesService
     */
    private function getExportProfileService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new ExportProfilesService();
        }

        return $instance;
    }
}
