<?php
/**
 * Plugin Name: Notification : credglv
 * Description: Extension for Notification plugin
 * Plugin URI: https://bracketspace.com
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 1.0.0
 * License: GPL3
 * Text Domain: notification-credglv
 * Domain Path: /languages
 *
 * @package notification/credglv
 */

/**
 * Things @todo. Replace globally these values:
 * - Credglv
 * - credglv
 * - credglv
 *
 * You can do this with this simple command replacing the sed parts:
 * find . -type f \( -iname \*.php -o -iname \*.txt -o -iname \*.json -o -iname \*.js \) -exec sed -i 's/Credglv/YOURNAMESPACE/g; s/credglv/Your Nicename/g; s/credglv/yourslug/g' {} +
 *
 * Or just execute the rename.sh script
 */

/**
 * Load Composer dependencies.
 */
require_once 'vendor/autoload.php';

/**
 * Gets plugin runtime object.
 *
 * @since  [Next]
 * @return BracketSpace\Notification\Credglv\Runtime
 */
function notification_credglv_runtime() {

	global $notification_credglv_runtime;

	if ( empty( $notification_credglv_runtime ) ) {
		$notification_credglv_runtime = new BracketSpace\Notification\Credglv\Runtime( __FILE__ );
	}

	return $notification_credglv_runtime;

}

/**
 * Boot up the plugin
 */
add_action( 'notification/boot/initial', function() {

	/**
	 * Requirements check
	 */
	$requirements = new BracketSpace\Notification\Credglv\Utils\Requirements( __( 'Notification : credglv', 'notification-credglv' ), [
		'php'          => '5.6',
		'wp'           => '4.9',
		'notification' => '6.0.0',
	] );

	$requirements->add_check( 'notification', require 'src/inc/requirements/notification.php' );

	if ( ! $requirements->satisfied() ) {
		add_action( 'admin_notices', [ $requirements, 'notice' ] );
		return;
	}

	$runtime = notification_credglv_runtime();
	$runtime->boot();

} );
