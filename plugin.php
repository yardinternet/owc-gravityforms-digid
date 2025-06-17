<?php

declare(strict_types=1);

/**
 * Plugin Name: Yard | GravityForms DigiD
 * Plugin URI: https://www.yard.nl
 * Description: Add a DigiD login field to GravityForms.
 * Author: Yard | Digital Agency
 * Author URI: https://www.yard.nl
 * Version: 1.7.0
 * License: GPL3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: owc-gravityforms-digid
 * Domain Path: /languages.
 */

/**
 * If this file is called directly, abort.
 */
if (! defined('WPINC')) {
    die;
}

define('GF_DIGID_PLUGIN_FILE', __FILE__);
define('GF_DIGID_PLUGIN_SLUG', 'owc-gravityforms-digid');
define('GF_DIGID_ROOT_PATH', __DIR__);
define('GF_DIGID_VERSION', '1.7.0');
define('GF_DIGID_LOGGER_DEFAULT_MAX_FILES', 7);
define('GF_DIGID_DEFAULT_SESSION_LIFETIME_SECONDS', '1500');

/**
 * Autoloader.
 */
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    require_once __DIR__ . '/autoloader.php';
    $autoloader = new \Yard\DigiD\Autoloader();
}

/**
 * Begin execution of the plugin.
 */
add_action('plugins_loaded', function () {
    $plugin = \Yard\DigiD\Foundation\Plugin::getInstance(__DIR__);

    add_action('after_setup_theme', function () use ($plugin) {
        $plugin->boot();
    });
}, 10);
