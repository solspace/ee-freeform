<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Repositories\ExportSettingsRepository;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ExportService
{
    /**
     * @param int $formId
     *
     * @return array
     */
    public function getExportDialogueTemplateVariables($formId = null)
    {
        /** @var Form[] $forms */
        $forms = [];

        $fields     = [];
        $formModels = FormRepository::getInstance()->getAllForms();
        foreach ($formModels as $form) {
            $forms[$form->id] = $form->getForm();
            foreach ($form->getForm()->getLayout()->getFields() as $field) {
                if ($field instanceof NoStorageInterface || !$field->getId()) {
                    continue;
                }

                $fields[$field->getId()] = $field;
            }
        }

        $firstForm = reset($forms);
        $userId = ee()->session->userdata('member_id');

        $settingRecord = ExportSettingsRepository::getInstance()->getOrCreate($userId);

        $settings = [];
        foreach ($forms as $form) {
            $storedFieldIds = $fieldSetting = [];

            if ($settingRecord && isset($settingRecord->settings[$form->getId()])) {
                foreach ($settingRecord->settings[$form->getId()] as $fieldId => $item) {
                    $label     = $item['label'];
                    $isChecked = (bool) $item['checked'];

                    if (is_numeric($fieldId)) {
                        try {
                            $field = $form->getLayout()->getFieldById($fieldId);
                            $label = $field->getLabel();

                            $storedFieldIds[] = $field->getId();
                        } catch (FreeformException $e) {
                            continue;
                        }
                    }

                    $fieldSetting[$fieldId] = [
                        'label'   => $label,
                        'checked' => $isChecked,
                    ];
                }
            }

            if (empty($fieldSetting)) {
                $fieldSetting['id']          = [
                    'label'   => 'ID',
                    'checked' => true,
                ];
                $fieldSetting['title']       = [
                    'label'   => 'Title',
                    'checked' => true,
                ];
                $fieldSetting['dateCreated'] = [
                    'label'   => 'Date Created',
                    'checked' => true,
                ];
            }

            foreach ($form->getLayout()->getFields() as $field) {
                if (
                    $field instanceof NoStorageInterface ||
                    !$field->getId() ||
                    in_array($field->getId(), $storedFieldIds, true)
                ) {
                    continue;
                }

                $fieldSetting[$field->getId()] = [
                    'label'   => $field->getLabel(),
                    'checked' => true,
                ];
            }

            $formSetting['form']   = $form;
            $formSetting['fields'] = $fieldSetting;

            $settings[] = $formSetting;
        }

        $selectedFormId = null;
        if ($formId && isset($forms[$formId])) {
            $selectedFormId = $formId;
        } else if ($firstForm) {
            $selectedFormId = $firstForm->getId();
        }

        return [
            'settings'       => $settings,
            'forms'          => $forms,
            'fields'         => $fields,
            'selectedFormId' => $selectedFormId,
        ];
    }
}
