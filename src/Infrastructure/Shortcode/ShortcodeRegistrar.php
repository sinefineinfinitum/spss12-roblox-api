<?php

namespace  SineFine\RobloxApi\Infrastructure\Shortcode;

class ShortcodeRegistrar
{
    public function addShortcode(string $name, callable $callback): void
    {
        add_shortcode($name, $callback);
    }
}
