<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\DataExport;

/**
 * ExportDataCSV - Exports to CSV (comma separated value) format.
 */
class ExportDataCSV extends ExportData
{
    /**
     * @param array $row
     *
     * @return string
     */
    public function generateRow($row) {
        foreach ($row as $key => $value) {
            // Escape inner quotes by double-quoting and wrap non-empty contents in new quotes
            if ($value !== '') {
                $row[$key] = '"' . str_replace('"', '""', $value) . '"';
            }
        }
        return implode(',', $row) . "\n";
    }

    public function sendHttpHeaders() {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' .basename($this->filename));
    }
}
