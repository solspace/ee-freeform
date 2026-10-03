<?php

namespace {
    class Cache
    {
        public const GLOBAL_SCOPE = 'global';
    }

    class FakeCache
    {
        public array $items = [];
        public int $saves = 0;

        public function get(string $key, string $scope): int|false
        {
            return $this->items[$key] ?? false;
        }

        public function save(string $key, int $value, int $ttl, string $scope): void
        {
            if ($ttl !== 3600 || $scope !== Cache::GLOBAL_SCOPE) {
                throw new \RuntimeException('Unexpected cache settings');
            }

            $this->items[$key] = $value;
            ++$this->saves;
        }
    }

    class FakeDb
    {
        public string $database = 'test';
        public string $dbprefix = 'exp_';
        public bool $allowLock = true;
        public int $locks = 0;
        public int $releases = 0;

        public function query(string $sql, array $bindings): object
        {
            if (str_contains($sql, 'GET_LOCK')) {
                ++$this->locks;
                return new class($this->allowLock) {
                    public function __construct(private bool $allowed) {}
                    public function row(): object { return (object) ['acquired' => (int) $this->allowed]; }
                };
            }

            if (str_contains($sql, 'RELEASE_LOCK')) {
                ++$this->releases;
                return new \stdClass();
            }

            throw new \RuntimeException('Unexpected query');
        }
    }

    function ee(): object
    {
        return $GLOBALS['fakeEe'];
    }
}

namespace Solspace\Addons\FreeformNext\Services {
    class FilesService
    {
        public static int $calls = 0;
        public static bool $fail = false;

        public function cleanUpUnfinalizedAssets(): void
        {
            ++self::$calls;
            if (self::$fail) {
                throw new \RuntimeException('File cleanup failed');
            }
        }
    }

    class SettingsService
    {
        public static int $calls = 0;

        public function cleanUpDatabaseSessionData(): void
        {
            ++self::$calls;
        }
    }
}

namespace {
    require __DIR__ . '/../src/freeform_next/Services/CleanupService.php';

    $GLOBALS['fakeEe'] = (object) ['cache' => new FakeCache(), 'db' => new FakeDb()];
    $resetRequest = static function (): void {
        $property = new \ReflectionProperty(\Solspace\Addons\FreeformNext\Services\CleanupService::class, 'checkedThisRequest');
        $property->setValue(null, false);
    };
    $cleanup = new \Solspace\Addons\FreeformNext\Services\CleanupService();
    $cleanup->runIfDue();
    $cleanup->runIfDue();
    $resetRequest();
    $cleanup->runIfDue();

    if (\Solspace\Addons\FreeformNext\Services\FilesService::$calls !== 1
        || \Solspace\Addons\FreeformNext\Services\SettingsService::$calls !== 1
        || ee()->db->locks !== 1 || ee()->db->releases !== 1) {
        throw new \RuntimeException('Cleanup was not throttled');
    }

    ee()->cache->items = [];
    ee()->db->allowLock = false;
    $resetRequest();
    $cleanup->runIfDue();
    if (\Solspace\Addons\FreeformNext\Services\FilesService::$calls !== 1) {
        throw new \RuntimeException('Cleanup ran without the lock');
    }

    ee()->db->allowLock = true;
    \Solspace\Addons\FreeformNext\Services\FilesService::$fail = true;
    $resetRequest();
    try {
        $cleanup->runIfDue();
        throw new \RuntimeException('Expected cleanup failure');
    } catch (\RuntimeException $error) {
        if ($error->getMessage() !== 'File cleanup failed') {
            throw $error;
        }
    }

    if (ee()->cache->saves !== 1 || ee()->db->releases !== 2) {
        throw new \RuntimeException('Failure was cached or lock was not released');
    }

    \Solspace\Addons\FreeformNext\Services\FilesService::$fail = false;
    $resetRequest();
    $cleanup->runIfDue();
    if (\Solspace\Addons\FreeformNext\Services\FilesService::$calls !== 3
        || \Solspace\Addons\FreeformNext\Services\SettingsService::$calls !== 2) {
        throw new \RuntimeException('Failed cleanup did not retry');
    }

    echo "Cleanup throttle checks passed\n";
}
