<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Repositories\StatusRepository;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class StatusController extends Controller
{
    /**
     * @return CpView
     */
    public function index()
    {
        $statuses = StatusRepository::getInstance()->getAllStatuses();

        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $columns = [
            'id'         => ['type' => Table::COL_ID],
            'name'       => ['type' => Table::COL_TEXT, 'encode' => false],
            'handle'     => ['type' => Table::COL_TEXT],
            'is_default' => ['type' => Table::COL_TEXT],
            'manage'     => ['type' => Table::COL_TOOLBAR],
            ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
        ];

        $tableData = [];
        foreach ($statuses as $status) {
            $checkbox = [
                'name'  => 'id_list[]',
                'value' => $status->id,
                'data'  => [
                    'confirm' => lang('status') . ': <b>' . htmlentities('test', ENT_QUOTES) . '</b>',
                ],
            ];

            if ($status->isDefault) {
                $checkbox['disabled'] = true;
            }

            $link = $this->getLink('settings/statuses/' . $status->id);

            $tableData[] = [
                $status->id,
                [
                    'content' => '<a href="' . $link . '"><span class="color-indicator" style="background: ' . $status->color . '"></span>' . $status->name . '</a>',
                ],
                $status->handle,
                $status->isDefault ? 'Yes' : '',
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href'  => $link,
                            'title' => lang('edit'),
                        ],
                    ],
                ],
                $checkbox,
            ];
        }

        $tableData = FreeformHelper::get('column_count', $tableData);
        $columns   = FreeformHelper::get('columns', $columns);

        $table->setColumns($columns);
        $table->setData($tableData);
        $table->setNoResultsText('No results');

        $removeModal = new ConfirmRemoveModal($this->getLink('settings/statuses/delete'));
        $removeModal->setKind('Statuses');

        $template = [
			'table'            => $table->viewData(),
			'cp_page_title'    => lang('Statuses'),
			'form_right_links' => FreeformHelper::get('right_links', $this),
			'footer' => [
				'submit_lang' => lang('submit'),
				'type'        => 'bulk_action_form',
			]
		];

        $view = new CpView('statuses/listing', $template);

        $view
            ->setHeading(lang('Statuses'))
            ->addModal($removeModal);

        return $view;
    }

    /**
     * @param int|string $id
     *
     * @return View
     * @throws FreeformException
     */
    public function edit($id)
    {
        if ('new' === $id) {
            $status = StatusModel::create();
        } else {
            $status = StatusRepository::getInstance()->getStatusById($id);
        }

        if (!$status) {
            throw new FreeformException(lang('Such status does not exist'));
        }

        $sectionData = [
            [
                [
                    'title'  => 'Name',
                    'desc'   => 'The name of the status.',
                    'fields' => [
                        'name' => [
                            'type'     => 'text',
                            'value'    => $status->name,
                            'required' => true,
                            'attrs'    => 'data-generator-base',
                        ],
                    ],
                ],
                [
                    'title'  => 'Handle',
                    'desc'   => 'How you’ll refer to this status in the templates.',
                    'fields' => [
                        'handle' => [
                            'type'     => 'text',
                            'value'    => $status->handle,
                            'required' => true,
                            'attrs'    => 'data-generator-target',
                        ],
                    ],
                ],
                [
                    'title'  => 'Color',
                    'desc'   => 'The color of the status circle when viewing inside CP.',
                    'fields' => [
                        'color' => [
                            'type'     => 'text',
                            'attrs'    => 'id="color-picker" style="background: ' . $status->color . ';"',
                            'value'    => $status->color,
                            'required' => true,
                        ],
                    ],
                ],
                [
                    'title'  => 'Is Default?',
                    'desc'   => 'Set this status be selected by default when creating new forms?',
                    'fields' => [
                        'default' => [
                            'type'  => 'yes_no',
                            'value' => $status->isDefault ? 'y' : 'n',
                        ],
                    ],
                ],
            ],
        ];

        $view = new CpView('statuses/edit');
        $view
            ->setHeading($status->name ?: lang('New Status'))
            ->addBreadcrumb(new NavigationLink('Statuses', 'settings/statuses'))
            ->addJavascript('statuses')
            ->addJavascript('handleGenerator')
            ->setTemplateVariables(
                [
                    'cp_page_title'         => 'Statuses',
                    'base_url'              => $this->getLink('settings/statuses/' . $id),
                    'save_btn_text'         => 'Save',
                    'save_btn_text_working' => 'Saving',
                    'sections'              => $sectionData,
                ]
            );

        return $view;
    }

    /**
     * @param string|int $id
     *
     * @return StatusModel
     * @throws FreeformException
     */
    public function save($id)
    {
        if ('new' === $id) {
            $model = StatusModel::create();
        } else {
            $model = StatusRepository::getInstance()->getStatusById($id);
        }

        if (!$model) {
            throw new FreeformException(lang('Such status does not exist'));
        }

        $isNew = !$model->id;

        $name    = ee()->input->post('name');
        $handle  = ee()->input->post('handle');
        $color   = ee()->input->post('color');
        $default = ee()->input->post('default');

        $model->set(['name' => $name]);
        $model->handle    = $handle;
        $model->color     = $color;
        $model->isDefault = $default === 'y';

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_BEFORE_SAVE, $model, $isNew)) {
            return $model;
        }

        try {
            $model->save();

            ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_AFTER_SAVE, $model, $isNew);

            if ($model->isDefault) {
                ee()
                    ->db
                    ->update(
                        StatusModel::TABLE,
                        ['isDefault' => 0],
                        ['id !=' => $model->id]
                    );
            } else {
                $this->updateDefaults();
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('Success'))
                ->defer();
        } catch (\Exception $e) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->withTitle($e->getMessage())
                ->defer();
        }

        return $model;
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

            $models = StatusRepository::getInstance()->getStatusesByIdList($ids);

            foreach ($models as $model) {
                if (ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_STATUS_AFTER_DELETE, $model);
            }

            $this->updateDefaults();
        }

        return new RedirectView($this->getLink('settings/statuses'));
    }

    /**
     * Sets the isDefault to TRUE for the first entry found if no isDefault is set
     */
    private function updateDefaults()
    {
        $hasDefault = ee()
            ->db
            ->where('isDefault', true)
            ->get(StatusModel::TABLE)
            ->num_rows();

        if (!$hasDefault) {
            ee()
                ->db
                ->limit(1)
                ->update(
                    StatusModel::TABLE,
                    ['isDefault' => true]
                );
        }
    }
}
