<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;
use Solspace\Addons\FreeformNext\Model\ExportSettingModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\ExportSettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;
use Solspace\Addons\FreeformNext\Services\ExportProfilesService;

class ExportController extends Controller
{
    public function export()
    {
        $settings = $this->getExportSettings();

        $formId       = ee()->input->post('form_id');
        $exportType   = ee()->input->post('export_type');
        $exportFields = ee()->input->post('export_fields');

        $formModel = FormRepository::getInstance()->getFormById($formId);
        if (!$formModel) {
            return;
        }

        $form      = $formModel->getForm();
        $fieldData = $exportFields[$form->getId()];

        $settings->settings = $exportFields;
        $settings->save();

        $searchableFields = $labels = [];

        $columnIndex = 0;

        foreach ($fieldData as $fieldId => $data) {

            $label     = $data['label'];
            $isChecked = $data['checked'];

            if (!(bool) $isChecked) {
                continue;
            }

            if (is_numeric($fieldId)) {
                $field = $form->getLayout()->getFieldById($fieldId);

                if ($field instanceof TableField) {
                    $tableColumns = $field->getLayout();

                    foreach ($tableColumns as $tableColumn) {
                        $label     = $tableColumn['label'];
                        $isChecked = $data['checked'];

                        $labels[$columnIndex] = $label;

                        $columnIndex++;
                    }
                } else {
                    $labels[$columnIndex] = $label;
                }

            } else {
                $labels[$columnIndex] = $label;
            }

            $fieldName = is_numeric($fieldId) ? SubmissionModel::getFieldColumnName($fieldId) : $fieldId;
            $fieldName = 's.' . $fieldName;

            $searchableFields[] = $fieldName;

            $columnIndex++;
        }

        $data = ee()
            ->db
            ->select(implode(',', $searchableFields))
            ->from(SubmissionModel::TABLE . ' s')
            ->where('formId', $form->getId())
            ->get()
            ->result_array();

        $exportService = new ExportProfilesService();

        switch ($exportType) {
            case 'json':
                return $exportService->exportJson($form, $data);

            case 'xml':
                return $exportService->exportXml($form, $data);

            case 'text':
                return $exportService->exportText($form, $data);

            case 'csv':
            default:
                return $exportService->exportCsv($form, $labels, $data);
        }
    }

    /**
     * @return ExportSettingModel
     */
    private function getExportSettings()
    {
        $userId   = ee()->session->userdata('member_id');

        return ExportSettingsRepository::getInstance()->getOrCreate($userId);
    }
}
