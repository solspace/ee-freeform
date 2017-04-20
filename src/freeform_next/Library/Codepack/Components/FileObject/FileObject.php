<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components\FileObject;

use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

abstract class FileObject
{
    // TODO: NEED TO TARGET THIS CORRECTLY
    const HELP_LINK = 'https://solspace.com/';

    /** @var Filesystem */
    private static $filesystem;

    /** @var string */
    protected $name;

    /** @var string */
    protected $path;

    /** @var bool */
    protected $folder;

    /**
     * @param string $path
     *
     * @return FileObject
     * @throws FileNotFoundException
     */
    public static function createFromPath($path)
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException(
                sprintf('Path points to nothing: "%s"', $path)
            );
        }

        $isFolder = is_dir($path);

        return $isFolder ? new Folder($path) : new File($path);
    }

    /**
     * @param $path
     */
    abstract protected function __construct($path);

    /**
     * Copy the file or directory to $target location
     *
     * @param string              $target
     * @param string|null         $prefix
     * @param array|callable|null $callable
     * @param string|null         $filePrefix
     *
     * @return void
     */
    abstract public function copy($target, $prefix = null, $callable = null, $filePrefix = null);

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return boolean
     */
    public function isFolder()
    {
        return $this->folder;
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if (null === self::$filesystem) {
            self::$filesystem = new Filesystem();
        }

        return self::$filesystem;
    }
}
