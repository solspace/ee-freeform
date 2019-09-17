<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Utilities;


class AddonInfo
{
    /** @var AddonInfo */
    private static $instance;

    /** @var string */
    private $module_name;

    /** @var string */
    private $lowerName;

    /** @var string */
    private $author;

    /** @var string */
    private $author_url;

    /** @var string */
    private $docs_url;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $version;

    /** @var string */
    private $namespace;

    /** @var bool */
    private $settings_exist;

    /** @var array */
    private $models;

    /**
     * @return AddonInfo
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new AddonInfo();
        }

        return self::$instance;
    }

    /**
     * AddonInfo constructor.
     */
    protected function __construct()
    {
        if (REQ !== 'CP' && !session_id()) {
            @session_start();
        }

        $data = require __DIR__ . '/../addon.setup.php';

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->lowerName = strtolower($this->module_name);
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * @return string
     */
    public function getLowerName()
    {
        return $this->lowerName;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorUrl()
    {
        return $this->author_url;
    }

    /**
     * @return string
     */
    public function getDocsUrl()
    {
        return $this->docs_url;
    }

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return bool
     */
    public function isSettingsExist()
    {
        return $this->settings_exist;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }
}
