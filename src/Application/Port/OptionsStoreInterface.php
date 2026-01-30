<?php

namespace SineFine\RobloxApi\Application\Port;

interface OptionsStoreInterface
{
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void;
}
