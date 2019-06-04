<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\core\interfaces\FrontControllerInterface;
use credglv\models\UserModel;


class MycredController extends FrontController implements FrontControllerInterface {

	private $minimum_transer;
	private $joining_fee;
	public $arr_share_comission;
	public $transfer_fee;


	public function init() {
		parent::init(); // TODO: Change the autogenerated stub


		$lvl1 = credglv()->config->credglv_comission_level1;
		$lvl2 = credglv()->config->credglv_comission_level2;
		$lvl3 = credglv()->config->credglv_comission_level3;
		$lvl4 = credglv()->config->credglv_comission_level4;

		$min_transfer        = credglv()->config->credglv_min_transfer;
		$credglv_joining_fee = credglv()->config->credglv_joining_fee;
		$transfer_fee        = credglv()->config->credglv_mycred_fee;

		$this->arr_share_comission = array(
			! empty( $lvl4 ) ? $lvl4 : 6,
			! empty( $lvl3 ) ? $lvl3 : 1,
			! empty( $lvl2 ) ? $lvl2 : 1,
			! empty( $lvl1 ) ? $lvl1 : 1,
		);
		$this->minimum_transer     = ! empty( $min_transfer ) ? $min_transfer : 10;
		$this->joining_fee         = ! empty( $credglv_joining_fee ) ? $credglv_joining_fee : 15;
		$this->transfer_fee        = ! empty( $transfer_fee ) ? $transfer_fee : 1;

	}


	public function credglv_transfer_active_verify( $transferid, $request, $settings, $context ) {
		$user     = new UserModel();
		$settings = mycred_part_woo_settings();
		if ( isset( $request['recipient_id'] ) && ! empty( $request['recipient_id'] ) ) {
			$user_id = $request['recipient_id'];
			$mycred  = mycred( $settings['point_type'] );

			// Excluded from usage
			if ( $mycred->exclude_user( $user_id ) ) {
				wc_add_notice( __( 'You are not allowed to use this feature.', 'credglv' ), 'error' );

				return;
			}

			$balance = $mycred->get_users_balance( $user_id );
//			echo '<pre>';
//			print_r( 'init if else active referal' );
//			echo '</pre>';
//			echo '<pre>';
//			print_r($user_id);
//			echo '</pre>';
			//			if ( empty( $user->check_actived_referral( $user_id, 0 ) ) && $balance >= $this->joining_fee && ! $mycred->has_entry( 'register_fee', 1, $user_id ) ) {
			if ( ! empty( $user->check_actived_referral( $user_id, 0 ) ) ) {
//				echo '<pre>';
//				print_r( 'empty( $user->check_actived_referral( $user_id, 0 )' );
//				echo '</pre>';
				if ( $balance >= $this->joining_fee ) {
//					echo '<pre>';
//					print_r( '$balance >= $this->joining_fee' );
//					echo '</pre>';
					if ( ! $mycred->has_entry( 'register_fee', 1, $user_id ) ) {
//						echo '<pre>';
//						print_r( 'Has not joining fee' );
//						echo '</pre>';
						$mycred->add_creds( 'register_fee',
							$user_id,
							- $this->joining_fee,
							__( 'Joining fee', 'credglv' ),
							1,
							'',
							$settings['point_type'] );
						$benefit_of_joining_fee = $this->mycred_share_commision( $user_id, $mycred, $this->joining_fee );
						$mycred->add_creds( 'benefit_register_fee',
							1,
							$benefit_of_joining_fee,
							__( 'Benefit of register fee from user: ' . $user_id, 'credglv' ),
							'',
							'',
							$settings['point_type'] );
						$user->update_active_status( $user_id );
					}
				}
			}
		}
	}

	public function mycred_share_commision( $user_id, $mycred, $joining_fee ) {
		return $this->share_referral_uper( $user_id, 0, $mycred, $joining_fee );
	}

	public function share_referral_uper( $user_id, $level, $mycred, &$joining_fee ) {
		$user          = UserModel::getInstance();
		$user_id_tempp = $user->get_referral_parent( $user_id );
		if ( ! empty( $user_id_tempp->referral_parent ) && isset( $user_id_tempp->referral_parent ) && $level <= 3 ) {
			$user_id_tempp = $user_id_tempp->referral_parent;
			$mycred->add_creds( 'share_comission',
				$user_id_tempp,
				$this->arr_share_comission[ $level ],
				__( 'Share commission from actived user ' . $user_id, 'credglv' ) );
			$joining_fee -= $this->arr_share_comission[ $level ];
			$level ++;
			$this->share_referral_uper( $user_id_tempp, $level, $mycred, $joining_fee );
		}

		return $joining_fee;
	}


	public function credglv_pro_custom_transfer_messages( $message ) {
		$message['low_amount']  = __( 'You must transfer minimum ', 'credglv' ) . $this->minimum_transer . '.';
		$message['invalid_pin'] = __( 'Your pin is wrong', 'credglv' );

		return $message;
	}

	function credglv_assets_enqueue() {

	}

	function credglv_mycred_valid_transfer_extra( $valided, $context ) {
		// Example: Minimum 30 points
		if ( $context->request['amount'] < $this->minimum_transer ) {
			return 'low_amount';
		}
		$pin = get_user_meta( get_current_user_id(), \credglv\front\controllers\UserController::METAKEY_PIN, true );
		if ( empty( $pin ) ) {
			return 'invalid_pin';

		}
		if ( $context->request['pin_transfer'] != $pin ) {
			return 'invalid_pin';
		}


		return true;
	}


	public function credglv_transfer_form_extra_otp_field( $fields, $args, $settings ) {


		$fields_temp = $fields;

		$fields = '
		<div class="row">

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

				<div class="form-group select-recipient-wrapper">
				<label>' . __( "Pin", "credglv" ) . '</label>
				<input type="password" maxlength="4" name="mycred_new_transfer[pin_transfer]"  
				value="" aria-required="true" class="form-control" >
				</div>
			</div>
		</div>
		<br>
		<p>' . __( 'Transaction fee (will be debited to recipent’s Gold Wallet): 1 Gold. Minimum transaction amount: 10 Gold. The amount is a multiple of 10.', 'credglv' ) . '</p>
';
		$fields .= $fields_temp;

		return $fields;
	}


	public function credglv_mycred_add_params_new_transfer_request( $arr_default, $_post ) {
		$arr_default['pin_transfer'] = '';
		if ( isset( $_post['pin_transfer'] ) ) {
			$arr_default['pin_transfer'] = $_post['pin_transfer'];
		}

		return $arr_default;
	}

	public function credglv_mycred_add_fee_to_transfer( $transfer_amount, $context ) {

		return $transfer_amount + $this->transfer_fee;


	}


	public function init_hook() {
		add_filter( 'mycred_transfer_messages', [ $this, 'credglv_pro_custom_transfer_messages' ] );
		add_filter( 'mycred_is_valid_transfer_request', [ $this, 'credglv_mycred_valid_transfer_extra' ], 10, 2 );
		add_filter( 'mycred_new_transfer_request', [ $this, 'credglv_mycred_add_params_new_transfer_request' ], 10, 2 );
		add_filter( 'mycred_transfer_charge', [ $this, 'credglv_mycred_add_fee_to_transfer' ], 10, 2 );
	}

	public static function registerAction() {


		/*'login_url'             => [
			'\credglv\models\UserModel' => [ 'redirectLoginUrl', 10, 3 ],
		],*/
		return [
			'actions' => [
				'init'                       => [ self::getInstance(), 'init_hook' ],
				'wp_enqueue_scripts'         => [ self::getInstance(), 'credglv_assets_enqueue' ],
				'mycred_transfer_completed'  => [ self::getInstance(), 'credglv_transfer_active_verify', 10, 4 ],
				'mycred_transfer_form_extra' => [ self::getInstance(), 'credglv_transfer_form_extra_otp_field', 10, 3 ],
			],
			'assets'  => [

			],
			'ajax'    => [
//				'ajax_update_profile' => [ self::getInstance(), 'updateProfile' ],
			]
		];
	}

}