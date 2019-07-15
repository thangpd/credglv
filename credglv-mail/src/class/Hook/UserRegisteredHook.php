<?php
/**
 * Settings class
 *
 * @package notification/signature
 */

namespace BracketSpace\Notification\Credmail\Hook;

use BracketSpace\Notification\Defaults\Trigger\User\UserRegistered;


/**
 * Settings class
 */
class UserRegisteredHook extends UserRegistered {
	public $slug = 'wordpress/user_registered';

	public function __construct() {
		parent::__construct();
		add_filter( 'notification/carrier/email/recipients', array( $this, 'add_recipient_custom_trigger' ), 10, 3 );
	}

	public function add_recipient_custom_trigger( $recipients, $context, $trigger ) {
		if ( $trigger->slug == $this->slug ) {
			/*
			 $trigger->user_object->data
			  stdClass Object
			(
				[ID] => 228
				[user_login] => thaowi
				[user_pass] => $P$BhVf5Mtr.1mY81OQptnyAST8QcgSxi/
				[user_nicename] => thaowi
				[user_email] => oiajwef@gmail.com
				[user_url] =>
				[user_registered] => 2019-07-14 06:38:54
				[user_activation_key] =>
				[user_status] => 0
				[display_name] => thaowi
			)*/
//			print_r( $trigger->user_object->data->user_email );
			if ( isset( $trigger->user_object->data->user_email ) ) {
				$recipients[] = $trigger->user_object->data->user_email;
			}
		}
		echo '<pre>';
		print_r($recipients);
		echo '</pre>';

		return $recipients;


	}


}
