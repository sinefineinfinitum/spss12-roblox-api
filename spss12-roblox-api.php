<?php
/**
 * Plugin Name: Roblox API Integration
 * Description: Allows you to retrieve data from the Roblox API via shortcodes.
 * Version: 0.0.1
 * Author: spss12
 * License: GPL-2.0+
 */

use SineFine\RobloxApi\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit;

// Composer autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Check php version
if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
    deactivate_plugins( plugin_basename( __FILE__ ) );
    wp_die( esc_html( __('This plugin requires PHP 8.0 or higher to function.', 'wp-roblox-api' )));
}


// Bootstrap plugin via Plugin class
add_action('plugins_loaded', static function () {
    if (class_exists('SineFine\\RobloxApi\\Plugin')) {
        (new Plugin())->boot();
    }
});
