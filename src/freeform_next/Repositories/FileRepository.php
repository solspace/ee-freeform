<?php

namespace Solspace\Addons\FreeformNext\Repositories;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;

class FileRepository extends Repository
{
    /**
     * @return FileRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @param FileUploadField $field
     *
     * @return array|null
     */
    public function getAssetSourceSettingsFor(FileUploadField $field)
    {
        $results = ee()->db
            ->select('id, name, server_path, url, allowed_types, max_size, max_width, max_height')
            ->from('exp_upload_prefs')
            ->where(['id' => $field->getAssetSourceId()])
            ->get()
            ->result_array();

        if (count($results) > 0) {
            $result = $results[0];

            $result['url']         = parse_config_variables($result['url']);
            $result['server_path'] = parse_config_variables($result['server_path']);

            return $result;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAllAssetSources()
    {
        $results = ee()->db
            ->select('id, name')
            ->from('exp_upload_prefs')
            ->where(
                [
                    'site_id'   => ee()->config->item('site_id'),
                    'module_id' => 0,
                ]
            )
            ->get()
            ->result_array();

        return $results;
    }
}
