<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadResponse;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Repositories\FileRepository;

class FilesService implements FileUploadHandlerInterface
{
    private static $fileKinds;

    /**
     * @param FileUploadField $field
     *
     * @return FileUploadResponse
     */
    public function uploadFile(FileUploadField $field)
    {
        $data = FileRepository::getInstance()->getAssetSourceSettingsFor($field);

        if (!$data) {
            return new FileUploadResponse(null, ['File upload source doesn\'t exist']);
        }

        ee()->load->library('upload');
        ee()->upload->initialize(
            [
                'max_size'      => (int) $data['max_size'] * 1024,
                'max_width'     => $data['max_width'],
                'max_height'    => $data['max_height'],
                'allowed_types' => '*',
                'upload_path'   => $data['server_path'],
            ]
        );

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_FILE_BEFORE_UPLOAD, $field)) {
            return new FileUploadResponse(null, 'Could not upload file');
        }

        $assetIds = $errors = [];
        foreach ($_FILES[$field->getHandle()]['name'] as $index => $name) {
            if (empty($name)) {
                continue;
            }

            $type    = $_FILES[$field->getHandle()]['type'][$index];
            $tmpName = $_FILES[$field->getHandle()]['tmp_name'][$index];
            $error   = $_FILES[$field->getHandle()]['error'][$index];
            $size    = $_FILES[$field->getHandle()]['size'][$index];

            $identificator = sha1(uniqid(time() . rand(1, 9999), true) . $tmpName);

            $_FILES[$identificator]['name']     = $name;
            $_FILES[$identificator]['type']     = $type;
            $_FILES[$identificator]['tmp_name'] = $tmpName;
            $_FILES[$identificator]['error']    = $error;
            $_FILES[$identificator]['size']     = $size;

            if (!ee()->upload->do_upload($identificator)) {
                $errors = array_merge($errors, ee()->upload->error_msg);

                continue;
            }

            $uploadData = ee()->upload->data();

            // Insert the file metadata into the database
            ee()->load->model('file_model');
            $assetId = ee()->file_model->save_file(
                [
                    'upload_location_id' => $field->getAssetSourceId(),
                    'title'              => $uploadData['file_name'],
                    'file_name'          => $uploadData['file_name'],
                    'file_size'          => $uploadData['file_size'] * 1024,
                    'mime_type'          => $uploadData['file_type'],
                    'file_hw_original'   => $uploadData['image_width'] . ' ' . $uploadData['image_height'],
                ]
            );

            $assetIds[] = $assetId;
            $this->markAssetUnfinalized($assetId);
        }

        if (!empty($errors)) {
            return new FileUploadResponse(null, $errors);
        }

        ExtensionHelper::call(ExtensionHelper::HOOK_FILE_AFTER_UPLOAD, $field, $assetIds);

        return new FileUploadResponse($assetIds);
    }

    /**
     * Stores the unfinalized assetId in the database
     * So that it can be deleted later if the form hasn't been finalized
     *
     * @param int $assetId
     */
    public function markAssetUnfinalized($assetId)
    {
        $date = new \DateTime();

        ee()->db
            ->insert(
                'freeform_next_unfinalized_files',
                [
                    'assetId'     => $assetId,
                    'dateCreated' => $date->format('Y-m-d H:i:s'),
                    'dateUpdated' => $date->format('Y-m-d H:i:s'),
                ]
            );
    }

    /**
     * Remove all unfinalized assets which are older than the TTL
     * specified in settings
     */
    public function cleanUpUnfinalizedAssets()
    {
        $date = new \DateTime('-180 minutes');

        $results = ee()->db
            ->select('id, assetId')
            ->from('freeform_next_unfinalized_files')
            ->where(['dateCreated <' => $date->format('Y-m-d H:i:s')])
            ->get()
            ->result_array();

        $fileIds = [];
        foreach ($results as $row) {
            $fileIds[$row['id']] = $row['assetId'];
        }

        if ($fileIds) {
            ee()->load->model('file_model');
            ee()->file_model->delete_files($fileIds);

            ee()->db
                ->where_in('id', array_keys($fileIds))
                ->delete('freeform_next_unfinalized_files');
        }
    }

    /**
     * @return array
     */
    public function getFileKinds()
    {
        if (null === self::$fileKinds) {
            self::$fileKinds = [
                'access'      => [
                    'label'      => lang('Access'),
                    'extensions' => ['adp', 'accdb', 'mdb', 'accde', 'accdt', 'accdr'],
                ],
                'audio'       => [
                    'label'      => lang('Audio'),
                    'extensions' => [
                        '3gp',
                        'aac',
                        'act',
                        'aif',
                        'aiff',
                        'aifc',
                        'alac',
                        'amr',
                        'au',
                        'dct',
                        'dss',
                        'dvf',
                        'flac',
                        'gsm',
                        'iklax',
                        'ivs',
                        'm4a',
                        'm4p',
                        'mmf',
                        'mp3',
                        'mpc',
                        'msv',
                        'oga',
                        'ogg',
                        'opus',
                        'ra',
                        'tta',
                        'vox',
                        'wav',
                        'wma',
                        'wv',
                    ],
                ],
                'compressed'  => [
                    'label'      => lang('Compressed'),
                    'extensions' => ['bz2', 'tar', 'gz', '7z', 's7z', 'dmg', 'rar', 'zip', 'tgz', 'zipx'],
                ],
                'excel'       => [
                    'label'      => lang('Excel'),
                    'extensions' => ['xls', 'xlsx', 'xlsm', 'xltx', 'xltm'],
                ],
                'flash'       => ['label' => lang('Flash'), 'extensions' => ['fla', 'flv', 'swf', 'swt', 'swc']],
                'html'        => ['label' => lang('HTML'), 'extensions' => ['html', 'htm']],
                'illustrator' => ['label' => lang('Illustrator'), 'extensions' => ['ai', 'eps']],
                'image'       => [
                    'label'      => lang('Image'),
                    'extensions' => [
                        'jfif',
                        'jp2',
                        'jpx',
                        'jpg',
                        'jpeg',
                        'jpe',
                        'tiff',
                        'tif',
                        'png',
                        'gif',
                        'bmp',
                        'webp',
                        'ppm',
                        'pgm',
                        'pnm',
                        'pfm',
                        'pam',
                        'svg',
                    ],
                ],
                'javascript'  => ['label' => lang('Javascript'), 'extensions' => ['js']],
                'json'        => ['label' => lang('JSON'), 'extensions' => ['json']],
                'pdf'         => ['label' => lang('PDF'), 'extensions' => ['pdf']],
                'photoshop'   => ['label' => lang('Photoshop'), 'extensions' => ['psd', 'psb']],
                'php'         => ['label' => lang('PHP'), 'extensions' => ['php']],
                'powerpoint'  => [
                    'label'      => lang('PowerPoint'),
                    'extensions' => ['pps', 'ppsm', 'ppsx', 'ppt', 'pptm', 'pptx', 'potx'],
                ],
                'text'        => ['label' => lang('Text'), 'extensions' => ['txt', 'text']],
                'video'       => [
                    'label'      => lang('Video'),
                    'extensions' => [
                        'avchd',
                        'asf',
                        'asx',
                        'avi',
                        'flv',
                        'fla',
                        'mov',
                        'm4v',
                        'mng',
                        'mpeg',
                        'mpg',
                        'm1s',
                        'mp2v',
                        'm2v',
                        'm2s',
                        'mp4',
                        'mkv',
                        'qt',
                        'flv',
                        'mp4',
                        'ogg',
                        'ogv',
                        'rm',
                        'wmv',
                        'webm',
                        'vob',
                    ],
                ],
                'word'        => ['label' => lang('Word'), 'extensions' => ['doc', 'docx', 'dot', 'docm', 'dotm']],
                'xml'         => ['label' => lang('XML'), 'extensions' => ['xml']],
            ];
        }

        // freeform_extra_file_extensions config
		// Must be an array of file extensions
		// Optionally, 'label' and 'extensions' keys can be provided
		// to customize the File Kind group name.

        $extraFileExtensions = ee()->config->item('freeform_extra_file_extensions') ?: [];

        $extraFileKinds = [];

        if(is_array($extraFileExtensions) && ! empty($extraFileExtensions))
		{
			if(! isset($extraFileExtensions['label']))
			{
				$extraFileExtensions['label'] = lang('Extra');
			}

			if(! isset($extraFileExtensions['extensions']))
			{
				$extraFileExtensions['extensions'] = $extraFileExtensions;
			}

			$extraFileKinds = [
				strtolower($extraFileExtensions['label']) => $extraFileExtensions,
			];
		}

        return self::$fileKinds + $extraFileKinds;
    }
}
