<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Codepack\Components;

use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileNotFoundException;
use Craft\IOHelper;

class TemplatesFileComponent extends AbstractFileComponent
{
    private $modifiableFileExtensions = array(
        'html',
        'twig',
    );

    /**
     * @return string
     */
    protected function getInstallDirectory()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getTargetFilesDirectory()
    {
        return 'templates';
    }

    /**
     * If anything has to be done with a file once it's copied over
     * This method does it
     *
     * @param string      $content
     * @param string|null $prefix
     *
     * @return string
     */
    public function fileContentModification($content, $prefix = null)
    {
        $content = $this->updateSrcAndHref($content, $prefix);
        $content = $this->updateLinks($content, $prefix);
        $content = $this->updateTemplateCalls($content, $prefix);
        $content = $this->replaceCustomPrefixCalls($content, $prefix);
        $content = $this->offsetSegments($content, $prefix);

        return $content;
    }

    /**
     * This pattern matches all src or href tag values which begin with:
     * /css or /js or /images
     * And replaces it with the prefixed asset path
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function updateSrcAndHref($content, $prefix)
    {
        $pattern = '/(src|href)=([\'"](?:\{{2}\s*siteUrl\s*}{2})?(?:\/?assets\/))demo\//';
        $replace = '$1=$2' . $prefix . '/';
        $content = preg_replace($pattern, $replace, $content);

        return $content;
    }

    /**
     * Replaces all links that starts with "{{ siteUrl }}demo/" with the new path
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function updateLinks($content, $prefix)
    {
        $pattern = '/([\'"](?:\{{2}\s*siteUrl\s*}{2})?\/?)demo\//';
        $replace = '$1' . $prefix . '/';
        $content = preg_replace($pattern, $replace, $content);

        return $content;
    }

    /**
     * Updates all includes and extends with the new location
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function updateTemplateCalls($content, $prefix)
    {
        $pattern = '/(\{\%\s*(?:extends|include)) ([\'"])(\/?)demo\//';
        $replace = '$1 $2$3' . $prefix . '/';
        $content = preg_replace($pattern, $replace, $content);

        return $content;
    }

    /**
     * Offset all segments by the number of segments the $prefix has
     * since our demo templates will be at least 1 folder deep
     *
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    private function offsetSegments($content, $prefix)
    {
        $segmentCount = count(explode("/", $prefix));

        $content = str_replace(
            '{% set baseUrlSegments = 1 %}',
            "{% set baseUrlSegments = $segmentCount %}",
            $content
        );

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
        $pattern = '#(%prefix%)#';
        $content = preg_replace($pattern, $prefix, $content);

        return $content;
    }
}
