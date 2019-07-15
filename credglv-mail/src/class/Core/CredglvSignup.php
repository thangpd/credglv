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
class CredglvSignup extends \BracketSpace\Notification\Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		// 1. Slug, can be prefixed with your plugin name.
		// 2. Title, should be translatable.
		parent::__construct(
			'credmail/credglv_signup',
			__( 'Credglv Signup', 'notification-credmail' )
		);

		// 1. Action hook.
		// 2. (optional) Action priority, default: 10.
		// 3. (optional) Action args, default: 1.
		// It's the same as add_action( 'any_action_hook', 'callback', 10, 2 ) with
		// only difference - the callback is always action() method (see below).
		$this->add_action( 'credglv_user_registered', 10, 1 );

		// 1. Trigger group, should be translatable.
		// This is optional, Group is displayed in the Trigger select.
		$this->set_group( __( 'Credglv Triggers', 'notification-credmail' ) );

		// 1. Trigger description, should be translatable.
		// This is optional, Description is displayed in the Trigger select.
		$this->set_description(
			__( 'Fires when user registered', 'notification-credmail' )
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
			//trigger
			$recipients[] = $trigger->user_email;
		}

		return $recipients;
	}


	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * You can use the action method arguments as usually.
	 *  $userdata (array)
	 *    'ID'                   => 0,    //(int) User ID. If supplied, the user will be updated.
	 *    'user_pass'            => $pass,   //(string) The plain-text user password.
	 *    'user_login'           => $data['username'],   //(string) The user's login username.
	 *    'user_email'           => $data['user_email'],   //(string) The user email address.
	 *    'show_admin_bar_front' => false,   //(string) The user email address.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $userdata ) {

		/**
		 * This is a method callback hooked to the action you've added in the Constructor.
		 *
		 * Two important things which are happening here:
		 * - $this->callback_args is a numeric array containing all the callback parameters
		 *   if you want to treat them as an array
		 * - if you want to abort Trigger execution, you must return false here
		 */
		if ( ! empty( $userdata ) ) {
			$this->user_id                  = $userdata['ID'];
			$this->user_pass                = $userdata['user_pass'];
			$this->user_pin                 = $userdata['user_pin'];
			$this->user_phone               = $userdata['user_phone'];
			$this->user_email               = $userdata['user_email'];
			$this->introducer               = $userdata['user_referral'];
			$this->user_object              = get_userdata( $this->user_id );
			$this->user_meta                = get_user_meta( $this->user_id );
			$this->user_registered_datetime = strtotime( $this->user_object->user_registered );
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
		 * $this->user_pass                = $userdata['user_pass'];
		 * $this->user_pin                = $userdata['user_pin'];
		 * $this->user_phone                = $userdata['user_phone'];
		 */
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_pin',
			// Name (required), should be translatable.
			'name'        => __( 'User Pin', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_pin;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User pin', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_pass',
			// Name (required), should be translatable.
			'name'        => __( 'User pass', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_pass;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User pass', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_phone',
			// Name (required), should be translatable.
			'name'        => __( 'User phone', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_phone;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User Phone', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => true,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_login',
			// Name (required), should be translatable.
			'name'        => __( 'User login', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_object->user_login;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'User Login', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => true,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'introducer',
			// Name (required), should be translatable.
			'name'        => __( 'User Introducer', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->introducer;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'Introducer', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => true,
		] ) );

	}

}
