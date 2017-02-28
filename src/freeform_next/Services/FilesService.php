<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Addons\FreeformNext\Library\FileUploads\FileUploadResponse;
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
                'allowed_types' => $data['allowed_types'] === 'all' ? '*' : $data['allowed_types'],
                'upload_path'   => $data['server_path'],
            ]
        );

        if (!ee()->upload->do_upload($field->getHandle())) {
            return new FileUploadResponse(null, ee()->upload->error_msg);
        }

        $uploadData = ee()->upload->data();

        // Insert the file metadata into the database
        ee()->load->model('file_model');
        $fileId = ee()->file_model->save_file(
            [
                'upload_location_id' => $field->getAssetSourceId(),
                'title'              => $uploadData['file_name'],
                'file_name'          => $uploadData['file_name'],
                'file_size'          => $uploadData['file_size'] * 1024,
                'mime_type'          => $uploadData['file_type'],
                'file_hw_original'   => $uploadData['image_width'] . ' ' . $uploadData['image_height'],
            ]
        );

        return new FileUploadResponse($fileId);
    }

    public function markAssetUnfinalized($assetId)
    {
        // TODO: Implement markAssetUnfinalized() method.
    }

    public function cleanUpUnfinalizedAssets()
    {
        // TODO: Implement cleanUpUnfinalizedAssets() method.
    }

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
                'illustrator' => ['label' => lang('Illustrator'), 'extensions' => ['ai']],
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

        return self::$fileKinds;
    }
}
