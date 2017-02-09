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

namespace Solspace\Addons\FreeformNext\Utilities;


class AddonUpdater
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
    private $hasPublishFields = true;

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
    public function install()
    {
        $this->insertSqlTables();
        $this->installModule();

        return true;
    }

    /**
     * @return bool
     */
    public function update()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->deleteSqlTables();

        ee()->db->delete(
            'modules',
            [
                'module_name' => $this->getAddonInfo()->getModuleName(),
            ]
        );

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
     * Iterates through all statements found in db.__module__.sql file
     * And executes them
     */
    private function insertSqlTables()
    {
        $addonInfo = $this->getAddonInfo();

        $sqlFileContents = file_get_contents(__DIR__ . "/../db." . $addonInfo->getLowerName() . ".sql");
        $statements = explode(";", $sqlFileContents);

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
}