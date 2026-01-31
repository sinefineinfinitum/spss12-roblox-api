<?php

namespace SineFine\RobloxApi\Tests\Infrastructure\Shortcode;

use PHPUnit\Framework\TestCase;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeRegistrar;

class ShortcodeRegistrarTest extends TestCase
{
    public function testAddShortcodeCallsWordPressFunction(): void
    {
        if (!function_exists('add_shortcode')) {
            eval('function add_shortcode($tag, $callback) { $GLOBALS["wp_shortcodes"][$tag] = $callback; }');
        }

        $registrar = new ShortcodeRegistrar();
        $name = 'test_shortcode';
        $callback = function () { return 'hello'; };

        $registrar->addShortcode($name, $callback);
        
        $this->assertArrayHasKey($name, $GLOBALS['wp_shortcodes']);
        $this->assertEquals($callback, $GLOBALS['wp_shortcodes'][$name]);
    }
}
