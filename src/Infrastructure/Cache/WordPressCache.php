<?php

namespace SineFine\RobloxApi\Infrastructure\Cache;

use SineFine\RobloxApi\Application\Port\CacheInterface;

class WordPressCache implements CacheInterface
{
    public function get(string $key): mixed
    {
        return get_transient($key);
    }

    public function set(string $key, mixed $value, int $ttl): void
    {
        set_transient($key, $value, $ttl);
    }

    public function delete(string $key): void
    {
        delete_transient($key);
    }
}
