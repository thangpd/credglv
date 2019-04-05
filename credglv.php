<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://thangpd.info
 * @wordpress-plugin
 * Plugin Name:       Credcoin Gold Leaf Ventures
 * Plugin URI:        http://thangpd.info
 * Description:       Core of Credcoin System plugin.
 * Version:           1.0.0
 * Author:            Thomas Pham
 * Author URI:        http://thangpd.info
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       credglv
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
defined('CREDGLV_VERSION') or define('CREDGLV_VERSION', '1.0.18');

/**
 * Require composer autoload file
 */
require_once (__DIR__ .'/vendor/autoload.php');

/**
 * Get plugin default configs
 */
$config = require (__DIR__ .'/config.php');
/**
 * Require plugin bootstrap
 */


require_once (__DIR__ .'/functions.php');
require_once (__DIR__ .'/core/Bootstrap.php');
/**
 * Run Cred GLV plugin
 */

\credglv\core\Bootstrap::boot($config);

/**
 * Register the hook run when this plugin activated
 */
register_activation_hook( __FILE__, [\credglv\core\App::getInstance(), 'activate']);
/**
 * Register the hook run when this plugin deactivated
 */
register_deactivation_hook( __FILE__, [\credglv\core\App::getInstance(),'deactivate']);
