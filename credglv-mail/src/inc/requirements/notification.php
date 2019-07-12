<?php
/**
 * Notification requirment check
 *
 * @package notification/credmail
 */

/**
 * Check if Notification plugin is installed
 */
return function( $comparison, $r ) {
	if ( ! function_exists( 'notification_runtime' ) ) {
		$r->add_error( __( '<a href="https://wordpress.org/plugins/notification/" target="_blank">Notification</a> plugin active', 'notification-credmail' ) );
		return;
	}

	if ( true !== $comparison ) {
		if ( ! defined( 'NOTIFICATION_VERSION' ) ) {
			$r->add_error( __( 'Notification plugin updated to the latest version', 'notification-credmail' ) );
		} elseif ( version_compare( $comparison, NOTIFICATION_VERSION, '>' ) ) {
			// translators: version number.
			$r->add_error( sprintf( __( 'Notification plugin in version at least %s', 'notification-credmail' ), $comparison ) );
		}
	}
};
