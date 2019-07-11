<?php
/**
 * Enqueues scripts
 *
 * @package notification/credglv
 */

namespace BracketSpace\Notification\Credglv\Admin;

use BracketSpace\Notification\Utils\Files;

/**
 * Scripts class
 */
class Scripts {

	/**
	 * Files class
	 *
	 * @var object
	 */
	private $files;

	/**
	 * Scripts constructor
	 *
	 * @since [Next]
	 * @param Files $files Files class.
	 */
	public function __construct( Files $files ) {
		$this->files = $files;
	}

	/**
	 * Adds a page hook where Notification scripts should be loaded.
	 *
	 * Either:
	 * - Add new page hook to this array
	 * - Create new action if you don't need any core Notification scripts or styles
	 *
	 * @filter notification/scripts/allowed_hooks
	 *
	 * @since  [Next]
	 * @param  array $page_hooks Page hooks.
	 * @return array
	 */
	public function add_screen( $page_hooks ) {
		// $page_hook[] = 'tools.php'
		return $page_hooks;
	}

	/**
	 * Enqueue scripts and styles for admin
	 *
	 * @action notification/scripts
	 *
	 * @param  string $page_hook current page hook.
	 * @return void
	 */
	public function enqueue_scripts( $page_hook ) {

		wp_enqueue_style( 'notification-credglv', $this->files->asset_url( 'css', 'style.css' ), [], $this->files->asset_mtime( 'css', 'style.css' ) );
		wp_enqueue_script( 'notification-credglv', $this->files->asset_url( 'js', 'scripts.min.js' ), [ 'jquery' ], $this->files->asset_mtime( 'js', 'scripts.min.js' ), true );

		wp_localize_script( 'notification-credglv', 'notification_credglv', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		] );

	}


}
