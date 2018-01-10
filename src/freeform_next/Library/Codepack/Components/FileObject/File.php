<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components\FileObject;

use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileObjectException;

class File extends FileObject
{
    /**
     * File constructor.
     *
     * @param $path
     */
    protected function __construct($path)
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $this->folder = false;
        $this->path   = $path;
        $this->name   = $filename;
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

        $target      = rtrim($target, '/');
        $newFilePath = $target . '/' . $filePrefix . $this->name;

        $fs->copy($this->path, $newFilePath, true);

        if (!file_exists($newFilePath)) {
            throw new FileObjectException(
                sprintf(
                    'Permissions denied. Could not write file in "%s".<br><a href="%s">Click here to find out how to resolve this issue.</a>',
                    $this->path,
                    self::HELP_LINK
                )
            );
        }

        if (is_callable($callable)) {
            call_user_func_array($callable, [$newFilePath, $prefix]);
        }
    }
}
