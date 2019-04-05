<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://thangpd.info
 * @since      1.0.0
 *
 * @package    CredGLV
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}



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

//\credglv\core\Bootstrap::boot($config);

//Run uninstall progress
\credglv\core\App::getInstance()->uninstall();