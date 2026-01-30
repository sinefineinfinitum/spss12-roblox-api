<?php

namespace SineFine\RobloxApi\Application\Port;

interface CacheInterface
{
    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed;

    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttl seconds
     */
    public function set(string $key, mixed $value, int $ttl): void;

    /**
     * @param string $key
     */
    public function delete(string $key): void;
}
