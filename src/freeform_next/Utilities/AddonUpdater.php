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

namespace Solspace\Addons\FreeformNext\Utilities;


use Solspace\Addons\FreeformNext\Utilities\AddonUpdater\PluginAction;
use Solspace\Addons\FreeformNext\Utilities\AddonUpdater\PluginExtension;

abstract class AddonUpdater
{
    /**
     * has to be public, because EE..
     *
     * @var string
     */
    public $version;

    /** @var bool */
    private $hasBackend = true;

    /** @var bool */
    private $hasPublishFields = false;

    /**
     * AddonUpdater constructor.
     */
    public function __construct()
    {
        $this->version = $this->getAddonInfo()->getVersion();
    }

    /**
     * @return bool
     */
    final public function install()
    {
        $this->onBeforeInstall();

        $this->insertSqlTables();
        $this->checkAndInstallActions();
        $this->checkAndInstallExtensions();
        $this->installModule();

        $this->onAfterInstall();

        return true;
    }

    /**
     * @param string|null $previousVersion
     *
     * @return bool
     */
    final public function update($previousVersion = null)
    {
        $this->runMigrations($previousVersion);
        $this->checkAndInstallActions();
        $this->checkAndInstallExtensions();

        return true;
    }

    /**
     * @return bool
     */
    final public function uninstall()
    {
        $this->onBeforeUninstall();

        $this->deleteSqlTables();

        ee()->db->delete(
            'modules',
            [
                'module_name' => $this->getAddonInfo()->getModuleName(),
            ]
        );

        $this->deleteActions();
        $this->deleteExtensions();

        $this->onAfterUninstall();

        return true;
    }

    /**
     * @return bool
     */
    public function isHasBackend()
    {
        return $this->hasBackend;
    }

    /**
     * @param bool $hasBackend
     *
     * @return $this
     */
    public function setHasBackend($hasBackend)
    {
        $this->hasBackend = $hasBackend;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasPublishFields()
    {
        return $this->hasPublishFields;
    }

    /**
     * @param bool $hasPublishFields
     *
     * @return $this
     */
    public function setHasPublishFields($hasPublishFields)
    {
        $this->hasPublishFields = $hasPublishFields;

        return $this;
    }

    /**
     * Perform any actions needed AFTER installing the plugin
     */
    protected function onAfterInstall()
    {
    }

    /**
     * Perform any actions needed BEFORE installing the plugin
     */
    protected function onBeforeInstall()
    {
    }

    /**
     * Perform any actions needed AFTER uninstalling the plugin
     */
    protected function onAfterUninstall()
    {
    }

    /**
     * Perform any actions needed BEFORE uninstalling the plugin
     */
    protected function onBeforeUninstall()
    {
    }

    /**
     * Runs all migrations that a plugin has
     */
    abstract protected function runMigrations();

    /**
     * Get an array of PluginAction objects
     *
     * @return PluginAction[]
     */
    abstract protected function getInstallableActions();

    /**
     * Get an array of PluginExtension objects
     *
     * @return PluginExtension[]
     */
    abstract protected function getInstallableExtensions();

    /**
     * @return AddonInfo
     */
    protected function getAddonInfo()
    {
        return AddonInfo::getInstance();
    }

    /**
     * Installs the module
     */
    private function installModule()
    {
        $addonInfo = $this->getAddonInfo();

        $data = [
            'module_name'        => $addonInfo->getModuleName(),
            'module_version'     => $addonInfo->getVersion(),
            'has_cp_backend'     => $this->isHasBackend() ? 'y' : 'n',
            'has_publish_fields' => $this->isHasPublishFields() ? 'y' : 'n',
        ];

        ee()->db->insert('modules', $data);
    }

    /**
     * Check all actions if they should be updated or installed
     */
    private function checkAndInstallActions()
    {
        foreach ($this->getInstallableActions() as $action) {
            $data = [
                'method'      => $action->getMethodName(),
                'class'       => $action->getClassName(),
                'csrf_exempt' => $action->isCsrfExempt(),
            ];

            $existing = ee()->db
                ->select('action_id')
                ->where([
                    'method'  => $action->getMethodName(),
                    'class' => $action->getClassName(),
                ])
                ->get('actions')
                ->row();

            if ($existing) {
                ee()->db
                    ->where('action_id', $existing->action_id)
                    ->update('actions', $data);
            } else {
                ee()->db->insert('actions', $data);
            }
        }
    }

    /**
     * Check all extensions if they should be updated or installed
     */
    private function checkAndInstallExtensions()
    {
        $className = $this->getAddonInfo()->getModuleName() . '_ext';
        $version   = $this->getAddonInfo()->getVersion();

        foreach ($this->getInstallableExtensions() as $extension) {
            $data = [
                'class'    => $className,
                'method'   => $extension->getMethodName(),
                'hook'     => $extension->getHookName(),
                'settings' => serialize($extension->getSettings()),
                'priority' => $extension->getPriority(),
                'version'  => $version,
                'enabled'  => $extension->isEnabled() ? 'y' : 'n',
            ];

            $existing = ee()->db
                ->select('extension_id')
                ->where([
                    'class'  => $className,
                    'method' => $extension->getMethodName(),
                    'hook'   => $extension->getHookName(),
                ])
                ->get('extensions')
                ->row();

            if ($existing) {
                unset($data['settings'], $data['priority']);

                ee()->db
                    ->where('extension_id', $existing->extension_id)
                    ->update('extensions', $data);
            } else {
                ee()->db->insert('extensions', $data);
            }
        }
    }

    private function deleteExtensions()
    {
        $className = $this->getAddonInfo()->getModuleName() . '_ext';

        foreach ($this->getInstallableExtensions() as $extension) {
            $existing = ee()->db
                ->select('extension_id')
                ->where([
                    'class'  => $className,
                    'method' => $extension->getMethodName(),
                    'hook'   => $extension->getHookName(),
                ])
                ->get('extensions')
                ->row();

            if ($existing) {
                ee()->db->delete('extensions', ['extension_id' => $existing->extension_id]);
            }
        }
    }

    /**
     * Iterates through all statements found in db.__module__.sql file
     * And executes them
     */
    private function insertSqlTables()
    {
        $addonInfo = $this->getAddonInfo();

        $sqlFileContents = file_get_contents(__DIR__ . "/../db." . $addonInfo->getLowerName() . ".sql");
        $statements      = explode(";", $sqlFileContents);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!$statement) {
                continue;
            }

            ee()->db->query($statement);
        }
    }

    /**
     * Iterates through all table names found in db.__module__.sql file
     * And drops them
     */
    private function deleteSqlTables()
    {
        $addonInfo = $this->getAddonInfo();

        $sqlFileContents = file_get_contents(__DIR__ . "/../db." . $addonInfo->getLowerName() . ".sql");

        preg_match_all("/CREATE TABLE IF NOT EXISTS `([a-zA-Z_0-9]+)`/", $sqlFileContents, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $tableName) {
                ee()->db->query("DROP TABLE `$tableName`");
            }
        }
    }

    /**
     * Uninstall any actions that were installed with this plugin
     */
    private function deleteActions()
    {
        foreach ($this->getInstallableActions() as $action) {
            ee()->db->delete(
                'actions',
                [
                    'method' => $action->getMethodName(),
                    'class'  => $action->getClassName(),
                ]
            );
        }
    }
}
