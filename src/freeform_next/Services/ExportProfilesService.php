<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextareaField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataExport\ExportDataCSV;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\SettingsRepository;

class ExportProfilesService
{
    /**
     * @param Form  $form
     * @param array $labels
     * @param array $data
     */
    public function exportCsv(Form $form, array $labels, array $data)
    {
        $data = $this->normalizeArrayData($form, $data);

        $csvData = $data;

        array_unshift($csvData, array_values($labels));

        $fileName = sprintf('%s submissions %s.csv', $form->getName(), date('Y-m-d H:i', time()));

        $export = new ExportDataCSV('browser', $fileName);
        $export->initialize();

        foreach ($csvData as $csv) {
            $export->addRow($csv);
        }

        $export->finalize();
        exit();
    }

    /**
     * @param Form  $form
     * @param array $data
     */
    public function exportJson(Form $form, array $data)
    {
        $data = $this->normalizeArrayData($form, $data, false);

        $export = [];
        foreach ($data as $itemList) {
            $sub = [];
            foreach ($itemList as $id => $value) {
                $label = $this->getHandleFromIdentificator($form, $id);

                $sub[$label] = $value;
            }

            $export[] = $sub;
        }

        $fileName = sprintf('%s submissions %s.json', $form->getName(), date('Y-m-d H:i', time()));

        $output = json_encode($export, JSON_PRETTY_PRINT);

        $this->outputFile($output, $fileName, 'application/octet-stream');
    }

    /**
     * @param Form  $form
     * @param array $data
     */
    public function exportText(Form $form, array $data)
    {
        $data = $this->normalizeArrayData($form, $data);

        $output = '';
        foreach ($data as $itemList) {
            foreach ($itemList as $id => $value) {
                $label = $this->getHandleFromIdentificator($form, $id);

                $output .= $label . ': ' . $value . "\n";
            }

            $output .= "\n";
        }

        $fileName = sprintf('%s submissions %s.txt', $form->getName(), date('Y-m-d H:i', time()));

        $this->outputFile($output, $fileName, 'text/plain');
    }

    /**
     * @param Form  $form
     * @param array $data
     */
    public function exportXml(Form $form, array $data)
    {
        $data = $this->normalizeArrayData($form, $data);

        $xml = new \SimpleXMLElement('<root/>');

        foreach ($data as $itemList) {
            $submission = $xml->addChild('submission');

            foreach ($itemList as $id => $value) {
                $label = $this->getHandleFromIdentificator($form, $id);

                if (is_null($value)) {
                    $value = '';
                }
                $node = $submission->addChild($label, htmlspecialchars($value));
                $node->addAttribute('label', $this->getLabelFromIdentificator($form, $id));
            }
        }

        $fileName = sprintf('%s submissions %s.xml', $form->getName(), date('Y-m-d H:i', time()));

        $this->outputFile($xml->asXML(), $fileName, 'text/xml');
    }

    /**
     * @param Form   $form
     * @param string $id
     *
     * @return string
     */
    private function getLabelFromIdentificator(Form $form, $id)
    {
        static $cache;

        if (null === $cache) {
            $cache = [];
        }

        if (!isset($cache[$id])) {
            $label = $id;
            if (preg_match('/^(?:field_)?(\d+)$/', $label, $matches)) {
                $fieldId = $matches[1];
                try {
                    $field = $form->getLayout()->getFieldById($fieldId);
                    $label = $field->getLabel();
                } catch (FreeformException $e) {
                }
            } else {
                switch ($id) {
                    case 'id':
                        $label = 'ID';
                        break;

                    case 'dateCreated':
                        $label = 'Date Created';
                        break;

                    default:
                        $label = ucfirst($label);
                        break;
                }
            }

            $cache[$id] = $label;
        }

        return $cache[$id];
    }

    /**
     * @param Form   $form
     * @param string $id
     *
     * @return string
     */
    private function getHandleFromIdentificator(Form $form, $id)
    {
        static $cache;

        if (null === $cache) {
            $cache = [];
        }

        if (!isset($cache[$id])) {
            $label = $id;
            if (preg_match('/^field_(\d+)$/', $label, $matches)) {
                $fieldId = $matches[1];
                try {
                    $field = $form->getLayout()->getFieldById($fieldId);

                    $label = $field->getHandle();

                    if ($field instanceof TableField) {
                        $tableColumns = $field->getLayout();

                        foreach ($tableColumns as $tableColumn) {
                            $label = $tableColumn['label'];
                        }
                    }

                } catch (FreeformException $e) {
                }
            }

            $cache[$id] = $label;
        }

        return $cache[$id];
    }

    /**
     * @param Form  $form
     * @param array $data
     * @param bool  $flattenArrays
     *
     * @return array
     */
    private function normalizeArrayData(Form $form, array $data, $flattenArrays = true)
    {
        $isRemoveNewlines = (bool) SettingsRepository::getInstance()->getOrCreate()->removeNewlines;

        $tableRowsData = null;
        $tableFieldIds = [];

        foreach ($data as $index => $item) {
            foreach ($item as $fieldId => $value) {
                if (!preg_match('/^' . SubmissionModel::FIELD_COLUMN_PREFIX . '(\d+)$/', $fieldId, $matches)) {
                    continue;
                }

                try {
                    $field = $form->getLayout()->getFieldById($matches[1]);

                    if ($field instanceof FileUploadField) {
                        $value = (array) json_decode($value ?: '[]', true);
                        $combo = [];

                        foreach ($value as $assetId) {
                            /** @var File $asset */
                            $asset = ee('Model')
                                ->get('File')
                                ->filter('file_id', (int) $assetId)
                                ->first();

                            if ($asset) {
                                $assetValue = $asset->file_name;
                                if ($asset->getAbsoluteURL()) {
                                    $assetValue = $asset->getAbsoluteURL();
                                }

                                $combo[] = $assetValue;
                            }
                        }

                        $data[$index][$fieldId] = implode(', ', $combo);

                        continue;
                    }

                    if ($field instanceof TableField) {
                        $rowsValues = json_decode($value ?: '[]', true);
                        $rowsValuesFormatted = [];

                        if ($rowsValues) {
                            $tableRowsData[$index][$fieldId] = $rowsValues;

                            if (!in_array($fieldId, $tableFieldIds)) {
                                $tableFieldIds[] = $fieldId;
                            }

                            if ($flattenArrays && is_array($rowsValues)) {

                                foreach ($rowsValues as $rowsValue) {
                                    $rowsValuesFormatted[] = implode(',', $rowsValue);
                                }

                                $rowsValues = implode('|', $rowsValuesFormatted);

                            }

                            $data[$index][$fieldId] = $rowsValues;
                        }

                        continue;

                    }

                    if ($field instanceof MultipleValueInterface) {
                        $value = json_decode($value ?: '[]', true);
                        if ($flattenArrays && is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $data[$index][$fieldId] = $value;
                    }

                    if ($isRemoveNewlines && $field instanceof TextareaField) {
                        $data[$index][$fieldId] = trim(preg_replace('/\s+/', ' ', $value));
                    }
                } catch (FreeformException $e) {
                    continue;
                }
            }
        }

        if ($tableRowsData) {
            $data = $this->populateDataWithTableDate($data, $tableRowsData, $tableFieldIds, $form);
        }

        return $data;
    }

    /**
     * @param string $content
     * @param string $fileName
     * @param string $contentType
     */
    private function outputFile($content, $fileName, $contentType)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));

        echo $content;

        exit();
    }


    private function populateDataWithTableDate($data, $tableRowsData, $tableFieldIds, $form)
    {
        $newData = [];

        $artificialRowsCount = $this->getArtificialRowsCount($tableRowsData);

        foreach ($data as $submissionId => $rowValues) {

        	if(! isset($tableRowsData[$submissionId]))
			{
				$newRow = $rowValues;
				$newData[] = $newRow;
				continue;
			}

            $submissionTableData = $tableRowsData[$submissionId];

            for ($i = 1; $i <= $artificialRowsCount[$submissionId]; $i++) {

                $newRow = $rowValues;

                if ($i > 1) {
                    foreach ($newRow as $newFieldId => $newFieldValue) {
                        $newRow[$newFieldId] = null;
                    }
                }

                foreach ($tableFieldIds as $tableFieldId) {


                    if (array_key_exists($tableFieldId, $submissionTableData)) {

                        $submissionsFieldData = $submissionTableData[$tableFieldId];

                        $tableFieldRowValue = 'no value';

                        if (array_key_exists($i-1, $submissionsFieldData)) {
                            $tableFieldRowValue = $submissionTableData[$tableFieldId][$i-1];

                        } else {
                            preg_match('/^' . SubmissionModel::FIELD_COLUMN_PREFIX . '(\d+)$/', $tableFieldId, $matches);

                            if (array_key_exists(1, $matches)) {
                                $field = $form->getLayout()->getFieldById($matches[1]);

                                if ($field instanceof TableField) {
                                    $tableColumns = $field->getLayout();

                                    $emptyFields = [];

                                    foreach ($tableColumns as $tableColumn) {
                                        $emptyFields[] = '';
                                    }

                                    $tableFieldRowValue = $emptyFields;
                                }
                            }
                        }

                        $thisKey = null;

                        $keyCounter = 0;
                        foreach ($newRow as $newRowFieldId => $newRowFieldValue) {
                            if ($newRowFieldId === $tableFieldId) {
                                $thisKey = $keyCounter;
                            }
                            $keyCounter++;
                        }

                        unset($newRow[$tableFieldId]);
                        array_splice($newRow, $thisKey, 0, $tableFieldRowValue);
                    }
                }

                $newData[] = $newRow;
            }
        }

        return $newData;
    }

    private function getArtificialRowsCount($tableRowsData)
    {
        $artificialRowsCount = [];

        foreach ($tableRowsData as $submissionId => $submissionTableFields) {
            foreach ($submissionTableFields as $submissionTableFieldId => $submissionTableFieldValues) {
                if (!array_key_exists($submissionId, $artificialRowsCount)) {
                    $artificialRowsCount[$submissionId] = count($submissionTableFieldValues);
                } elseif ($artificialRowsCount[$submissionId] < count($submissionTableFieldValues)) {
                    $artificialRowsCount[$submissionId] = count($submissionTableFieldValues);
                }
            }
        }

        return $artificialRowsCount;
    }
}
