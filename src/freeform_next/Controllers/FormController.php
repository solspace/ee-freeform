<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Model\FormModel;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;

class FormController extends Controller
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
                'id'          => ['type' => Table::COL_ID],
                'form'        => ['type' => 'html'],
                'submissions' => ['type' => 'html'],
                'manage'      => ['type' => 'html'],
                ['type' => Table::COL_CHECKBOX, 'name' => 'selection'],
            ]
        );

        $table->setData(
            [
                [
                    1,
                    "test",
                    "schmest",
                    "asd",
                    [
                        'name'  => 'selections[]',
                        'value' => 1,
                        'data'  => [
                            'confirm' => lang('form') . ': <b>' . htmlentities("test", ENT_QUOTES) . '</b>'
                        ]
                    ]
                ]
            ]
        );

        $table->setNoResultsText('No reults');

        $view = new CpView('form/listing', ['table' => $table->viewData()]);
        $view->setHeading(lang('Forms'));

        return $view;
    }

    /**
     * @param FormModel $form
     *
     * @return CpView
     */
    public function editForm(FormModel $form)
    {
        $view = new CpView('form/edit');
        $view->setHeading('Freeform');
        $view->addJavascript('composer/app.js');
        $view->addJavascript('composer/vendor.js');

        return $view;
    }
}