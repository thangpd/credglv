<?php
/**
 * Signature class
 *
 * @package notification/signature
 */

namespace BracketSpace\Notification\Credmail\Core;

/**
 * Custom trigger class
 */
class CredglvPinChanged extends \BracketSpace\Notification\Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		// 1. Slug, can be prefixed with your plugin name.
		// 2. Title, should be translatable.
		parent::__construct(
			'credmail/credglv_security_info_changed',
			__( 'Credglv Pin Page Changed', 'notification-credmail' )
		);

		// 1. Action hook.
		// 2. (optional) Action priority, default: 10.
		// 3. (optional) Action args, default: 1.
		// It's the same as add_action( 'any_action_hook', 'callback', 10, 2 ) with
		// only difference - the callback is always action() method (see below).
		$this->add_action( 'credglv_security_info_changed', 10, 1 );

		// 1. Trigger group, should be translatable.
		// This is optional, Group is displayed in the Trigger select.
		$this->set_group( __( 'Credglv Triggers', 'notification-credmail' ) );

		// 1. Trigger description, should be translatable.
		// This is optional, Description is displayed in the Trigger select.
		$this->set_description(
			__( 'Fires when page pin changed', 'notification-credmail' )
		);
		add_filter( 'notification/carrier/email/recipients', array( $this, 'add_recipient_custom_trigger' ), 10, 3 );
	}

	public function add_recipient_custom_trigger( $recipients, $context, $trigger ) {
		if ( $trigger->slug == $this->slug ) {
			//recipients
//			echo '<pre>';
//			print_r( $recipients );
//			echo '</pre>';
			//context
//			echo '<pre>';
//			print_r( $context );
//			echo '</pre>';
			$recipients[] = $trigger->user_email;
			if ( isset( $trigger->old_email ) ) {
				$recipients[] = $trigger->old_email;
			}
		}

		return $recipients;
	}


	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * You can use the action method arguments as usually.
	 *  $userdata (array)
	 * $data_change['password']
	 * $data_change['email']
	 * $data_change['meta_pin']
	 * $data_change['meta_phone']
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $userdata, $old_email ) {

		/**
		 * This is a method callback hooked to the action you've added in the Constructor.
		 *
		 * Two important things which are happening here:
		 * - $this->callback_args is a numeric array containing all the callback parameters
		 *   if you want to treat them as an array
		 * - if you want to abort Trigger execution, you must return false here
		 */
		if ( ! empty( $userdata ) ) {
			$this->data_changed = implode( ", ", $userdata );
			$user_info          = get_userdata( get_current_user_id() );
			$this->user_name    = $user_info->user_login;
			$this->user_email   = $user_info->user_email;
		}
		if ( ! empty( $old_email ) ) {
			$this->old_email = $old_email;
		}
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		/**
		 * In this method you can assign any Merge Tags to the trigger.
		 *
		 * To see what Merge Tags are available go to Notification plugin's core
		 * and look in class/Defaults/MergeTag directory.
		 *
		 * $this->data_changed;
		 */
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_data_changed',
			// Name (required), should be translatable.
			'name'        => __( 'Data changed', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->data_changed;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'If password or phone or email or pin changed.', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_email',
			// Name (required), should be translatable.
			'name'        => __( 'User Email', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_email;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User Email', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_name',
			// Name (required), should be translatable.
			'name'        => __( 'User Name', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_name;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User Name', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );

	}

}
