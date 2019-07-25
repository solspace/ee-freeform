<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components;

use Solspace\Addons\FreeformNext\Library\Codepack\Components\FileObject\FileObject;
use Solspace\Addons\FreeformNext\Library\Codepack\Components\FileObject\Folder;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\CodepackException;

abstract class AbstractFileComponent implements ComponentInterface
{
    /** @var string */
    protected $installDirectory;

    /** @var string */
    protected $targetFilesDirectory;

    /** @var Folder */
    protected $contents;

    /** @var string */
    private $location;

    /**
     * @param string $location - the location of files
     *
     * @throws CodepackException
     */
    public final function __construct($location)
    {
        $this->location = $location;
        $this->contents = $this->locateFiles();
    }

    /**
     * @return string
     */
    abstract protected function getInstallDirectory();

    /**
     * @return string
     */
    abstract protected function getTargetFilesDirectory();

    /**
     * If anything must come after /{install_directory}/{prefix}demo/{???}
     * It is returned here
     *
     * @param string $prefix
     *
     * @return string
     */
    protected function getSubInstallDirectory($prefix)
    {
        return '';
    }

    /**
     * Installs the component files into the $installDirectory
     *
     * @param string|null $prefix
     */
    public function install($prefix = null)
    {
        $siteId = ee()->config->item('site_id');

        //get template group number
        $maxGroupOrder = ee()->db
            ->select_max('group_order')
            ->where('site_id', $siteId)
            ->get('template_groups')
            ->row('group_order');

        $groupId = ee()->db
            ->select('group_id')
            ->where(
                [
                    'site_id' => $siteId,
                    'group_name' => $prefix,
                ]
            )
            ->get('template_groups')
            ->row('group_id');

        if (!$groupId) {
            ee()->db
                ->insert(
                    'template_groups',
                    [
                        'site_id' => $siteId,
                        'group_name' => $prefix,
                        'group_order' => $maxGroupOrder + 1,
                        'is_site_default' => 'n',
                    ]
                );

            $groupId = (int) ee()->db->insert_id();
        }

        foreach ($this->contents as $file) {
            $fileContent = file_get_contents($file->getPath());
            $fileContent = $this->fileContentModification($fileContent, $prefix);

            $templateType     = 'webpage';
            $templateName     = $file->getName();
            $allowPhp         = preg_match('/<\?php/is', $fileContent) ? 'y' : 'n';
            $phpParseLocation = preg_match('/<\?php\s\/\/\sinput/is', $fileContent) ? 'i' : 'o';

            $insertData = [
                'site_id'            => $siteId,
                'group_id'           => $groupId,
                'template_type'      => $templateType,
                'edit_date'          => time(),
                'template_name'      => $templateName,
                'template_data'      => $fileContent,
                'allow_php'          => $allowPhp,
                'php_parse_location' => $phpParseLocation,
            ];

            $existingTemplateId = ee()->db
                ->select('template_id')
                ->where(
                    [
                        'template_name' => $templateName,
                        'template_type' => $templateType,
                        'group_id' => $groupId,
                        'site_id' => $siteId,
                    ]
                )
                ->get('templates')
                ->row('template_id');

            if ($existingTemplateId) {
                ee()->db->update(
                    'templates',
                    $insertData,
                    ['template_id' => $existingTemplateId]
                );
            } else {
                ee()->db->insert(
                    'templates',
                    $insertData
                );
            }
        }
    }

    /**
     * If anything has to be done with the file contents before it's persisted
     * This method does it
     *
     * @param string      $content
     * @param string|null $prefix
     *
     * @return string
     */
    public function fileContentModification($content, $prefix = null)
    {
    }

    /**
     * @return FileObject
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return FileObject
     * @throws CodepackException
     */
    private function locateFiles()
    {
        $directory = FileObject::createFromPath($this->getFileLocation());

        if (!$directory instanceof Folder) {
            throw new CodepackException('Target directory is not a directory: ' . $this->getFileLocation());
        }

        return $directory;
    }

    /**
     * @return string
     */
    private function getFileLocation()
    {
        return $this->location . '/' . $this->getTargetFilesDirectory();
    }
}
