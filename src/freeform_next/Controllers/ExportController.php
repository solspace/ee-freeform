<?php

namespace Solspace\Addons\FreeformNext\Controllers;

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
        foreach ($fieldData as $fieldId => $data) {
            $label     = $data['label'];
            $isChecked = $data['checked'];

            if (!(bool) $isChecked) {
                continue;
            }

            $labels[$fieldId] = $label;

            $fieldName = is_numeric($fieldId) ? SubmissionModel::getFieldColumnName($fieldId) : $fieldId;
            $fieldName = 's.' . $fieldName;

            $searchableFields[] = $fieldName;
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
