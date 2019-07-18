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
class CredglvReceiveSubmission extends \BracketSpace\Notification\Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		// 1. Slug, can be prefixed with your plugin name.
		// 2. Title, should be translatable.
		parent::__construct(
			'credmail/credglv_receive_commission',
			__( 'Credglv Receive Commission', 'notification-credmail' )
		);

		// 1. Action hook.
		// 2. (optional) Action priority, default: 10.
		// 3. (optional) Action args, default: 1.
		// It's the same as add_action( 'any_action_hook', 'callback', 10, 2 ) with
		// only difference - the callback is always action() method (see below).
		$this->add_action( 'credglv_receive_commission', 10, 1 );

		// 1. Trigger group, should be translatable.
		// This is optional, Group is displayed in the Trigger select.
		$this->set_group( __( 'Credglv Triggers', 'notification-credmail' ) );

		// 1. Trigger description, should be translatable.
		// This is optional, Description is displayed in the Trigger select.
		$this->set_description(
			__( 'Fires when user receive commission', 'notification-credmail' )
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
			$recipients[] = $trigger->user_taker_data->user_email;

		}

		return $recipients;
	}


	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * You can use the action method arguments as usually.
	 *  $userdata (array)
	 *
	 * 'user_giver'        => $user_id,
	 * 'user_taker'        => $user_id_tempp, //user id who get bonus
	 * 'share_commission' => $this->arr_share_comission[ $level ] // bonus number
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $userdata ) {
		if ( ! empty( $userdata ) ) {
			$user_giver_data        = get_userdata( $userdata['user_giver'] );
			$user_taker_data        = get_userdata( $userdata['user_taker'] );
			$this->user_taker_data  = $user_giver_data;
			$this->user_giver_data  = $user_taker_data;
			$this->share_commission = $userdata['share_commission'];
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
			'slug'        => 'credglv_user_taker_name',
			// Name (required), should be translatable.
			'name'        => __( 'User name who get bonus', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_taker_data->user_name;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'Username who get bonus', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_user_giver_name',
			// Name (required), should be translatable.
			'name'        => __( 'User name who give bonus', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->user_giver_data->user_name;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'Username who give bonus', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
		$this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			// Slug (required), this will be used as {parametrized_url} value.
			// Don't translate this.
			'slug'        => 'credglv_share_commission',
			// Name (required), should be translatable.
			'name'        => __( 'Commission', 'notification-credmail' ),
			// Resolver (required), this can be a closure like below or function name
			// like: 'parametrized_url_resolver' or array( $this, 'parametrized_url_resolver' ).
			'resolver'    => function ( $trigger ) {
				// Trigger object is available here,
				// with all the properties you set in action() method.
				return $trigger->share_commission;
			},
			// Description (optional), should be translatable, default: ''.
			'description' => __( 'Commission', 'notification-credmail' ),
			// Example indicator (optional)
			// if true, then description will have "Example" label, default: false.
			'example'     => false,
		] ) );
	}

}