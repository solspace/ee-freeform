<?php

namespace Solspace\Addons\FreeformNext\Services;

use Cache;

class CleanupService
{
    private const CACHE_KEY = '/freeform_next/cleanup_complete';
    private const INTERVAL_SECONDS = 3600;

    private static bool $checkedThisRequest = false;

    public function runIfDue(): void
    {
        if (self::$checkedThisRequest) {
            return;
        }

        self::$checkedThisRequest = true;

        if (ee()->cache->get(self::CACHE_KEY, Cache::GLOBAL_SCOPE) !== false) {
            return;
        }

        // Cache get/save is not an atomic lock. Serialize the occasional cache miss
        // across web workers so the same unfinished upload cannot be deleted twice.
        $lockName = 'ffn_cleanup_' . substr(
            sha1((string) ee()->db->database . ':' . (string) ee()->db->dbprefix),
            0,
            32
        );

        $lock = ee()->db->query('SELECT GET_LOCK(?, 0) AS acquired', [$lockName])->row();
        if (!$lock || (int) $lock->acquired !== 1) {
            return;
        }

        try {
            if (ee()->cache->get(self::CACHE_KEY, Cache::GLOBAL_SCOPE) !== false) {
                return;
            }

            (new FilesService())->cleanUpUnfinalizedAssets();
            (new SettingsService())->cleanUpDatabaseSessionData();

            // Mark complete only after both operations succeed, so a failure can
            // be retried on the next request rather than hiding it for an hour.
            ee()->cache->save(self::CACHE_KEY, time(), self::INTERVAL_SECONDS, Cache::GLOBAL_SCOPE);
        } finally {
            ee()->db->query('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }
}
