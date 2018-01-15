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

namespace Solspace\Addons\FreeformNext\Library\Codepack;

use Solspace\Addons\FreeformNext\Library\Codepack\Components\RoutesComponent;
use Solspace\Addons\FreeformNext\Library\Codepack\Components\TemplatesFileComponent;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\CodepackException;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileObjectException;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\Manifest\ManifestNotPresentException;
use Solspace\Addons\FreeformNext\Library\Codepack\Components\AssetsFileComponent;

class Codepack
{
    const MANIFEST_NAME = 'manifest.json';

    /** @var string */
    private $location;

    /** @var Manifest */
    private $manifest;

    /** @var TemplatesFileComponent */
    private $templates;

    /** @var AssetsFileComponent */
    private $assets;

    /**
     * @param string $prefix
     *
     * @return string
     */
    public static function getCleanPrefix($prefix)
    {
        $prefix = preg_replace('/\/+/', '/', $prefix);
        $prefix = trim($prefix, '/');

        return $prefix;
    }


    /**
     * Codepack constructor.
     *
     * @param string $location
     *
     * @throws CodepackException
     * @throws ManifestNotPresentException
     */
    public function __construct($location)
    {
        if (!file_exists($location) || !is_dir($location)) {
            throw new CodepackException(
                sprintf(
                    'Codepack folder does not exist in "%s"',
                    $location
                )
            );
        }

        $this->location  = $location;
        $this->manifest  = $this->assembleManifest();
        $this->templates = $this->assembleTemplates();
        // $this->assets    = $this->assembleAssets();
        // $this->routes    = $this->assembleRoutes();
    }

    /**
     * @param string $prefix
     *
     * @throws FileObjectException
     */
    public function install($prefix)
    {
        $prefix = self::getCleanPrefix($prefix);

        $this->templates->install($prefix);
        // $this->assets->install($prefix);
        // $this->routes->install($prefix);
    }

    /**
     * @return Manifest
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * @return TemplatesFileComponent
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @return AssetsFileComponent
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return RoutesComponent
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Assembles a Manifest object based on the manifest file
     *
     * @return Manifest
     */
    private function assembleManifest()
    {
        return new Manifest($this->location . '/' . self::MANIFEST_NAME);
    }

    /**
     * Gets a TemplatesComponent object with all installable templates found
     *
     * @return TemplatesFileComponent
     */
    private function assembleTemplates()
    {
        return new TemplatesFileComponent($this->location);
    }

    /**
     * Gets an AssetsComponent object with all installable assets found
     *
     * @return AssetsFileComponent
     */
    private function assembleAssets()
    {
        return new AssetsFileComponent($this->location);
    }

    /**
     * Gets a RoutesComponent object with all installable routes
     *
     * @return RoutesComponent
     */
    private function assembleRoutes()
    {
        return new RoutesComponent($this->location);
    }
}
