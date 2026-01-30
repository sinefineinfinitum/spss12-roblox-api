<?php

namespace SineFine\RobloxApi\Infrastructure\Option;

use SineFine\RobloxApi\Application\Port\OptionsStoreInterface;

class OptionService implements OptionsStoreInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return get_option($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        update_option($key, $value);
    }
}