<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components;

use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileNotFoundException;

class AssetsFileComponent extends AbstractFileComponent
{
    private static $modifiableFileExtensions = array(
        'css',
        'scss',
        'sass',
        'less',
        'js',
        'coffee',
    );

    private static $modifiableCssFiles = array(
        'css',
        'scss',
        'sass',
        'less',
    );

    /**
     * @return string
     */
    protected function getInstallDirectory()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/assets';
    }

    /**
     * @return string
     */
    protected function getTargetFilesDirectory()
    {
        return 'assets';
    }

    /**
     * If anything has to be done with a file once it's copied over
     * This method does it
     *
     * @param string      $content
     * @param string|null $prefix
     *
     * @throws FileNotFoundException
     */
    public function fileContentModification($content, $prefix = null)
    {
        if (!file_exists($content)) {
            throw new FileNotFoundException(
                sprintf('Could not find file: %s', $content)
            );
        }

        $extension = strtolower(pathinfo($content, PATHINFO_EXTENSION));

        // Prevent from editing anything other than css and js files
        if (!in_array($extension, self::modifiableFileExtensions, true)) {
            return;
        }

        $content = file_get_contents($content);

        if (in_array($extension, self::$modifiableCssFiles, true)) {
            $content = $this->updateImagesURL($content, $prefix);
            //$content = $this->updateRelativePaths($content, $prefix);
            $content = $this->replaceCustomPrefixCalls($content, $prefix);
        }

        file_put_contents($content, $content);
    }

    /**
     * This pattern matches all url(/images[..]) with or without surrounding quotes
     * And replaces it with the prefixed asset path
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function updateImagesURL($content, $prefix)
    {
        $pattern = '/url\s*\(\s*([\'"]?)\/((?:images)\/[a-zA-Z1-9_\-\.\/]+)[\'"]?\s*\)/';
        $replace = 'url($1/assets/' . $prefix . '/$2$1)';
        $content = preg_replace($pattern, $replace, $content);

        return $content;
    }

    /**
     * Updates all "../somePath/" urls to "../$prefix_somePath/" urls
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function updateRelativePaths($content, $prefix)
    {
        $pattern = '/([\(\'"])\.\.\/([^"\'())]+)([\'"\)])/';
        $replace = '$1../' . $prefix . '$2$3';
        $content = preg_replace($pattern, $replace, $content);

        return $content;
    }

    /**
     * @param string $content
     * @param string $prefix
     *
     * @return mixed
     */
    private function replaceCustomPrefixCalls($content, $prefix)
    {
        $pattern = '/(%prefix%)/';
        $content = preg_replace($pattern, $prefix, $content);

        return $content;
    }
}
