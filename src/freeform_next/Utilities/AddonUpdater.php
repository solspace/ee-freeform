<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Utilities;


use Solspace\Addons\FreeformNext\Utilities\AddonUpdater\PluginAction;
use Solspace\Addons\FreeformNext\Utilities\AddonUpdater\PluginExtension;
use Throwable;

abstract class AddonUpdater
{
    /**
     * has to be public, because EE..
     *
     * @var string
     */
    public $version;

    private bool $hasBackend = true;

    private bool $hasPublishFields = false;

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
    final public function install(): bool
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
    final public function update(?string $previousVersion = null): bool
    {
        if (!$this->runMigrations($previousVersion)) {
            return false;
        }
        $this->checkAndInstallActions();
        $this->checkAndInstallExtensions();

        return true;
    }

    /**
     * @return bool
     */
    final public function uninstall(): bool
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
     * @return $this
     */
    public function setHasBackend(bool $hasBackend)
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
     * @return $this
     */
    public function setHasPublishFields(bool $hasPublishFields)
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
    private function installModule(): void
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
    private function checkAndInstallActions(): void
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
    private function checkAndInstallExtensions(): void
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
                // Keep administrator choices when an existing hook is refreshed.
                unset($data['settings'], $data['priority'], $data['enabled']);

                ee()->db
                    ->where('extension_id', $existing->extension_id)
                    ->update('extensions', $data);
            } else {
                ee()->db->insert('extensions', $data);
            }
        }
    }

    private function deleteExtensions(): void
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
    private function insertSqlTables(): void
    {
        $addonInfo = $this->getAddonInfo();

        $sqlFileContents = file_get_contents(__DIR__ . "/../db." . $addonInfo->getLowerName() . ".sql");
        $sqlFileContents = $this->stripSqlComments($sqlFileContents);

        $currentPrefix = ee()->db->dbprefix;
        $defaultPrefix = 'exp_';

        if ($currentPrefix !== $defaultPrefix) {
            // Replace backticked identifiers: `exp_table_name` → `myprefix_table_name`
            $sqlFileContents = preg_replace(
                '/`' . preg_quote($defaultPrefix, '/') . '([a-zA-Z0-9_]+)`/',
                '`' . $currentPrefix . '$1`',
                $sqlFileContents
            );

            // Also replace bare identifiers (just in case any appear un-backticked)
            // e.g.,  ALTER TABLE exp_xxx ...  or  REFERENCES exp_xxx (...)
            $sqlFileContents = preg_replace(
                '/\b' . preg_quote($defaultPrefix, '/') . '([a-zA-Z0-9_]+)\b/',
                $currentPrefix . '$1',
                $sqlFileContents
            );
        }

        $sqlFileContents = $this->stripNamedForeignKeyConstraints($sqlFileContents);

        // Only for MySQL/MariaDB: running without this is fine too
        if ($this->isMySqlLike()) {
            ee()->db->query('SET FOREIGN_KEY_CHECKS=0');
        }

        $statements = $this->splitSqlStatements($sqlFileContents);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }

            ee()->db->query($statement);
        }

        if ($this->isMySqlLike()) {
            ee()->db->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Iterates through all table names found in db.__module__.sql file
     * And drops them
     */
    private function deleteSqlTables(): void
    {
        // Only for MySQL/MariaDB: running without this is fine too
        if ($this->isMySqlLike()) {
            ee()->db->query('SET FOREIGN_KEY_CHECKS=0');
        }

        $addonInfo = $this->getAddonInfo();
        $sqlFileContents = file_get_contents(__DIR__ . "/../db." . $addonInfo->getLowerName() . ".sql");
        $sqlFileContents = $this->stripSqlComments($sqlFileContents);

        // Rewrite table prefix in the SQL *before* parsing blocks
        $currentPrefix = ee()->db->dbprefix;
        $defaultPrefix = 'exp_';
        if ($currentPrefix !== $defaultPrefix) {
            $sqlFileContents = preg_replace('/`' . preg_quote($defaultPrefix, '/') . '([a-zA-Z0-9_]+)`/', '`' . $currentPrefix . '$1`', $sqlFileContents);
            $sqlFileContents = preg_replace('/\b' . preg_quote($defaultPrefix, '/') . '([a-zA-Z0-9_]+)\b/', $currentPrefix . '$1', $sqlFileContents);
        }

        // Parse blocks *after* rewrite so names are correct
        $blocks = $this->findCreateTableBlocks($sqlFileContents);

        $tables = [];
        $allForeignKeys = [];
        foreach ($blocks as $block) {
            $tables[] = $block['table'];
            $allForeignKeys = array_merge($allForeignKeys, $this->parseForeignKeys($block['table'], $block['body']));
        }

        // Drop children → parents
        $dropOrder = $this->computeDropOrder($tables, $allForeignKeys);
        foreach ($dropOrder as $tableName) {
            ee()->db->query("DROP TABLE IF EXISTS `{$tableName}`");
        }

        if ($this->isMySqlLike()) {
            ee()->db->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Uninstall any actions that were installed with this plugin
     */
    private function deleteActions(): void
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

    private function isMySqlLike(): bool
    {
        // EE uses MySQLi; MariaDB reports as MySQL platform
        $platform = strtolower((string) ee()->db->platform()); // "mysql"

        return str_contains($platform, 'mysql');
    }

    /**
     * Remove /* … * /, -- … and # … comments without touching content inside strings.
     */
    private function stripSqlComments(string $sql): string
    {
        // Remove /* ... */ blocks
        $sql = preg_replace('#/\*.*?\*/#s', '', $sql);

        $out = [];
        $lines = preg_split("/\R/", $sql);
        foreach ($lines as $line) {
            $inSingle = false;
            $inDouble = false;
            $escaped  = false;
            $buf = '';
            $len = strlen($line);

            for ($i = 0; $i < $len; $i++) {
                $ch = $line[$i];

                if ($escaped) { $buf .= $ch; $escaped = false; continue; }
                if ($ch === '\\') { $buf .= $ch; $escaped = true; continue; }

                if ($ch === "'" && !$inDouble) { $inSingle = !$inSingle; $buf .= $ch; continue; }
                if ($ch === '"' && !$inSingle) { $inDouble = !$inDouble; $buf .= $ch; continue; }

                if (!$inSingle && !$inDouble) {
                    if ($ch === '-' && ($i + 1 < $len) && $line[$i + 1] === '-') { break; }
                    if ($ch === '#') { break; }
                }

                $buf .= $ch;
            }
            $out[] = $buf;
        }
        return implode("\n", $out);
    }

    /**
     * Split SQL by semicolons not inside quotes.
     */
    private function splitSqlStatements(string $sql): array
    {
        $stmts = [];
        $buf = '';
        $inSingle = false; $inDouble = false; $escaped = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];

            if ($escaped) { $buf .= $ch; $escaped = false; continue; }
            if ($ch === '\\') { $buf .= $ch; $escaped = true; continue; }

            if ($ch === "'" && !$inDouble) { $inSingle = !$inSingle; $buf .= $ch; continue; }
            if ($ch === '"' && !$inSingle) { $inDouble = !$inDouble; $buf .= $ch; continue; }

            if ($ch === ';' && !$inSingle && !$inDouble) {
                $stmts[] = $buf;
                $buf = '';
                continue;
            }
            $buf .= $ch;
        }
        if (trim($buf) !== '') { $stmts[] = $buf; }

        return array_values(array_filter($stmts, fn($s): bool => trim($s) !== ''));
    }

    /**
     * Option B: Strip explicit constraint names so MySQL auto-names them.
     * (Turns "CONSTRAINT `name` FOREIGN KEY" into just "FOREIGN KEY")
     */
    private function stripNamedForeignKeyConstraints(string $sql): string
    {
        // Replace only inside CREATE TABLE bodies
        if (!preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?\s*\((.*?)\)[^;]*;/is', $sql, $blocks, PREG_SET_ORDER)) {
            return $sql;
        }

        foreach ($blocks as $block) {
            $full = $block[0];
            $body = $block[2];

            $updatedBody = preg_replace(
                '/CONSTRAINT\s+`?[a-zA-Z0-9_]+`?\s+(FOREIGN\s+KEY\s*\()/i',
                '$1',
                $body
            );

            if ($updatedBody !== $body) {
                $newFull = str_replace($body, $updatedBody, $full);
                $sql = str_replace($full, $newFull, $sql);
            }
        }
        return $sql;
    }

    /**
     * Find CREATE TABLE blocks: returns array of ['table' => string, 'body' => string]
     */
    private function findCreateTableBlocks(string $sql): array
    {
        $blocks = [];
        $pattern = '/
            CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?     # CREATE TABLE [IF NOT EXISTS]
            `?([a-zA-Z0-9_]+)`?                            # table name (captured)
            \s*\(                                          # opening paren
            (.*?)                                          # body (non-greedy)
            \)                                             # closing paren
            [^;]*;                                         # up to semicolon
        /isx';

        if (preg_match_all($pattern, $sql, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $blocks[] = [
                    'table' => $match[1],
                    'body' => $match[2],
                ];
            }
        }
        return $blocks;
    }

    /**
     * Parse FKs from a CREATE TABLE body without lookbehinds.
     * - 1st pass: named constraints (captures full segment)
     * - Strip matched segments from a working copy
     * - 2nd pass: unnamed "FOREIGN KEY (...)" constraints
     */
    private function parseForeignKeys(string $table, string $body): array
    {
        $fks = [];

        // Normalize whitespace for simpler regex
        $bodyNorm = preg_replace('/\s+/', ' ', $body);

        // ----- Pass 1: named constraints -----
        // Capture the full segment so we can strip it before pass 2.
        $reNamed = '/CONSTRAINT\s+`?([a-zA-Z0-9_]+)`?\s+FOREIGN\s+KEY\s*\(\s*`?([^)`]+)`?\s*\)\s*REFERENCES\s+`?([a-zA-Z0-9_]+)`?\s*\(\s*`?([^)`]+)`?\s*\)/i';
        $namedMatches = [];
        if (preg_match_all($reNamed, $bodyNorm, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($m as $row) {
                $constraint = $row[1][0];
                $cols       = array_map('trim', array_map(fn($s): string => trim($s, "` \t\n\r\0\x0B"), explode(',', $row[2][0])));
                $refTable   = $row[3][0];
                $refCols    = array_map('trim', array_map(fn($s): string => trim($s, "` \t\n\r\0\x0B"), explode(',', $row[4][0])));

                $fks[] = [
                    'constraint' => $constraint,
                    'table'      => $table,
                    'columns'    => $cols,
                    'refTable'   => $refTable,
                    'refColumns' => $refCols,
                ];

                // Remember full matched text so we can remove it
                $namedMatches[] = $row[0][0];
            }
        }

        // Strip named FK segments from a working copy
        $work = $bodyNorm;
        foreach ($namedMatches as $seg) {
            // Replace only the first occurrence each time to preserve positions
            $work = preg_replace('/'.preg_quote($seg, '/').'/', '', $work, 1);
        }

        // ----- Pass 2: unnamed constraints -----
        $reUnnamed = '/FOREIGN\s+KEY\s*\(\s*`?([^)`]+)`?\s*\)\s*REFERENCES\s+`?([a-zA-Z0-9_]+)`?\s*\(\s*`?([^)`]+)`?\s*\)/i';
        if (preg_match_all($reUnnamed, $work, $m2, PREG_SET_ORDER)) {
            foreach ($m2 as $row) {
                $cols     = array_map('trim', array_map(fn($s): string => trim($s, "` \t\n\r\0\x0B"), explode(',', $row[1])));
                $refTable = $row[2];
                $refCols  = array_map('trim', array_map(fn($s): string => trim($s, "` \t\n\r\0\x0B"), explode(',', $row[3])));

                $fks[] = [
                    'constraint' => null,
                    'table'      => $table,
                    'columns'    => $cols,
                    'refTable'   => $refTable,
                    'refColumns' => $refCols,
                ];
            }
        }

        return $fks;
    }

    /**
     * Topological sort: returns array of table names in safe DROP order (children → parents).
     * Edges: child -> parent (because FK points to parent).
     */
    private function computeDropOrder(array $tables, array $foreignKeys): array
    {
        // Build adjacency + in-degree
        $adj = [];
        $inDegree = [];
        foreach ($tables as $t) {
            $adj[$t] = [];
            $inDegree[$t] = 0;
        }
        foreach ($foreignKeys as $fk) {
            $child = $fk['table'];
            $parent = $fk['refTable'];
            if (!isset($adj[$child])) $adj[$child] = [];
            if (!isset($inDegree[$parent])) $inDegree[$parent] = 0;
            if (!isset($inDegree[$child])) $inDegree[$child] = 0;

            // avoid duplicate edges
            if (!in_array($parent, $adj[$child], true)) {
                $adj[$child][] = $parent;
                $inDegree[$parent]++;
            }
        }

        // Kahn's algorithm but reversed output for DROP order:
        // We want nodes with highest dependency first (children), so normal topo order gives parents first.
        // We'll compute topo order and then reverse it.
        $q = [];
        foreach ($inDegree as $node => $deg) {
            if ($deg === 0) $q[] = $node;
        }

        $topo = [];
        while (!empty($q)) {
            $n = array_shift($q);
            $topo[] = $n;
            foreach ($adj[$n] as $m) {
                $inDegree[$m]--;
                if ($inDegree[$m] === 0) $q[] = $m;
            }
        }

        // If there’s a cycle, append any remaining nodes in any order
        $unprocessed = array_keys(array_filter($inDegree, fn($d): bool => $d > 0));
        if (!empty($unprocessed)) {
            // Put cyclical nodes first in DROP order (safe when combined with SET FOREIGN_KEY_CHECKS=0)
            $dropOrder = array_merge($unprocessed, array_reverse($topo));
            $dropOrder = array_values(array_unique($dropOrder));
            return $dropOrder;
        }

        // Normal case: reverse topo (children → parents)
        return array_reverse($topo);
    }
}
