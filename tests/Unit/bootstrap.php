<?php

/**
 * PHPUnit bootstrap file
 */

/**
 * Load dependencies with Composer autoloader.
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ .'/../../src/DigiD/Foundation/Helpers.php';

define('WP_PLUGIN_DIR', __DIR__);
define('WP_DEBUG', false);

define('GF_DIGID_PLUGIN_SLUG', 'owc-gravityforms-digid');
define('GF_DIGID_PLUGIN_FILE', __FILE__);
define('GF_DIGID_ROOT_PATH', __DIR__ .'/../..');
define('GF_DIGID_VERSION', '1.0');

/**
 * Bootstrap WordPress Mock.
 */
\WP_Mock::setUsePatchwork(true);
\WP_Mock::bootstrap();

$GLOBALS['owc-gravityforms-digid'] = [
    'active_plugins' => ['owc-gravityforms-digid/plugin.php'],
];
