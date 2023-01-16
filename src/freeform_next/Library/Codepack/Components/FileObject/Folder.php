<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components\FileObject;

use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileObjectException;
use Symfony\Component\Finder\Finder;

class Folder extends FileObject implements \Iterator
{
    /** @var FileObject[]|null */
    protected $files;

    /** @var int */
    private $fileCount;

    /**
     * Folder constructor.
     *
     * @param string $path
     */
    protected function __construct($path)
    {
        $finder = new Finder();
        $finder->sortByName()->depth(0)->in($path);

        $this->folder = true;
        $this->path   = $path;
        $this->name   = pathinfo($path, PATHINFO_BASENAME);

        $files = [];
        foreach ($finder as $file) {
            $files[] = FileObject::createFromPath($file->getRealPath());
        }

        $this->files = $files;
    }

    /**
     * Copy the file or directory to $target location
     *
     * @param string              $target
     * @param string|null         $prefix
     * @param array|callable|null $callable
     * @param string|null         $filePrefix
     *
     * @return void
     * @throws FileObjectException
     */
    public function copy($target, $prefix = null, $callable = null, $filePrefix = null)
    {
        $fs = $this->getFilesystem();

        $target           = rtrim($target, '/');
        $targetFolderPath = $target . '/' . $filePrefix . $this->name;
        if (!file_exists($targetFolderPath) || !is_dir($targetFolderPath)) {
            $fs->mkdir($targetFolderPath);

            if (!is_dir($targetFolderPath)) {
                throw new FileObjectException(
                    sprintf(
                        'Permissions denied. Could not create a folder in "%s".<br>Check how to solve this problem <a href="%s">here</a>',
                        $targetFolderPath,
                        self::HELP_LINK
                    )
                );
            }
        }

        foreach ($this->files as $file) {
            $file->copy($targetFolderPath, $prefix, $callable);
        }
    }

    /**
     * Gets the total number of File instances this Folder object has, recursively
     *
     * @return int
     */
    public function getFileCount()
    {
        if (null === $this->fileCount) {
            $count = 0;
            foreach ($this->files as $file) {
                if ($file instanceof Folder) {
                    $count += $file->getFileCount();
                } else {
                    $count++;
                }
            }

            $this->fileCount = $count;
        }

        return $this->fileCount;
    }

    /**
     * @return FileObject[]|null
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
	public function current()
    {
        return current($this->files);
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function next()
    {
        next($this->files);
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->files);
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return null !== $this->key() && $this->key() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->files);
    }
}
