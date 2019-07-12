<?php
/**
 * Plugin Name: Notification : Credglv Mail Notification
 * Description: Extension for Notification plugin
 * Plugin URI: https://bracketspace.com
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 1.0.0
 * License: GPL3
 * Text Domain: notification-credmail
 * Domain Path: /languages
 *
 * @package notification/credmail
 */

/**
 * Things @todo. Replace globally these values:
 * - Credmail
 * - Credglv Mail Notification
 * - credmail
 *
 * You can do this with this simple command replacing the sed parts:
 * find . -type f \( -iname \*.php -o -iname \*.txt -o -iname \*.json -o -iname \*.js \) -exec sed -i 's/Credmail/YOURNAMESPACE/g; s/Credglv Mail Notification/Your Nicename/g; s/credmail/yourslug/g' {} +
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
 * @return BracketSpace\Notification\Credmail\Runtime
 */
function notification_credmail_runtime() {

	global $notification_credmail_runtime;

	if ( empty( $notification_credmail_runtime ) ) {
		$notification_credmail_runtime = new BracketSpace\Notification\Credmail\Runtime( __FILE__ );
	}

	return $notification_credmail_runtime;

}

/**
 * Boot up the plugin
 */
add_action( 'notification/boot/initial', function() {

	/**
	 * Requirements check
	 */
	$requirements = new BracketSpace\Notification\Credmail\Utils\Requirements( __( 'Notification : Credglv Mail Notification', 'notification-credmail' ), [
		'php'          => '5.6',
		'wp'           => '4.9',
		'notification' => '6.0.0',
	] );

	$requirements->add_check( 'notification', require 'src/inc/requirements/notification.php' );

	if ( ! $requirements->satisfied() ) {
		add_action( 'admin_notices', [ $requirements, 'notice' ] );
		return;
	}

	$runtime = notification_credmail_runtime();
	$runtime->boot();

} );
