<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataExport\ExportDataCSV;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;

class ExportProfilesService
{
    /**
     * @param Form  $form
     * @param array $labels
     * @param array $data
     */
    public function exportCsv(Form $form, array $labels, array $data)
    {
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
        $xml = new \SimpleXMLElement('<root/>');

        foreach ($data as $itemList) {
            $submission = $xml->addChild('submission');

            foreach ($itemList as $id => $value) {
                $label = $this->getHandleFromIdentificator($form, $id);

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
                } catch (FreeformException $e) {
                }
            }

            $cache[$id] = $label;
        }

        return $cache[$id];
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
}