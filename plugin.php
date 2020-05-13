<?php
/**
 * Plugin Name: OWC GravityForms DigiD
 * Plugin URI: https://www.yard.nl
 * Description: Add a DigiD login field to GravityForms
 * Author: Yard Digital Agency
 * Author URI: https://www.yard.nl
 * Version: 1.0.2
 * License: GPL3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: owc-gravityforms-digid
 * Domain Path: /languages.
 */

/*
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

define('GF_DIGID_PLUGIN_FILE', __FILE__);
define('GF_DIGID_PLUGIN_SLUG', 'owc-gravityforms-digid');
define('GF_DIGID_ROOT_PATH', __DIR__);
define('GF_DIGID_VERSION', '1.0.2');

/**
 * Manual loaded file: the autoloader.
 */
require_once __DIR__.'/autoloader.php';
$autoloader = new \Yard\DigiD\Autoloader();

/**
 * Begin execution of the plugin.
 */
$plugin = \Yard\DigiD\Foundation\Plugin::getInstance(__DIR__)->boot();
