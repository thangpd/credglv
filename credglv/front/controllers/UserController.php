<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\core\RuntimeException;
use credglv\helpers\GeneralHelper;
use credglv\models\Instructor;
use credglv\models\UserModel;
use credglv\core\components\Style;
use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;
use mysql_xdevapi\Exception;


class UserController extends FrontController implements FrontControllerInterface {


	const METAKEY_PHONE = 'cred_billing_phone';

	/**
	 * Get user id by phone
	 * @return mixed
	 */
	public static function getUserIDByPhone( $phone ) {
		global $wpdb;

		$res = array( 'code' => 200, 'message' => 'Phone is registered' );

		$mobile_num_result = $wpdb->get_var( "select user_id from " . $wpdb->prefix . "usermeta  where meta_key='" . self::METAKEY_PHONE . "' and meta_value='" . $phone . "' " );


		if ( ! empty( $mobile_num_result ) ) {

			$res['userID'] = $mobile_num_result;

			return $res;

		} else {
			$res['code']    = 404;
			$res['message'] = __( 'Phone is not registered', 'credglv' );

			return $res;
		}


	}

	/**
	 * Get phone by userid
	 * @return mixed
	 */
	public static function getPhoneByUserID( $userID ) {

		$phone = get_user_meta( $userID, UserController::METAKEY_PHONE, true );

		return $phone;
	}


	function credglv_wooc_edit_profile_save_fields( $args ) {
		$user_id = get_current_user_ID();
		if ( isset( $_POST['cred_billing_phone'] ) && $_POST['cred_billing_phone'] == '' ) {
			$args->add( 'billing_phone_name_error', __( 'Mobile number is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST['cred_billing_phone'] ) && ! empty( $_POST['cred_billing_phone'] ) ) {
			$current_phone = UserController::getPhoneByUserID( $user_id );
			if ( $_POST['cred_billing_phone'] !== $current_phone ) {
				$mobile_num_result = self::getUserIDByPhone( $_POST['cred_billing_phone'] );
				if ( isset( $mobile_num_result ) ) {
					if ( $user_id != $mobile_num_result ) {
						wc_add_notice( __( 'Mobile Number is already used.', 'woocommerce' ), 'error' );

						return $_POST;
					} else {
						update_user_meta( $user_id, 'billing_phone', $_POST['cred_billing_phone'] );
					}
				} else {
					update_user_meta( $user_id, 'billing_phone', $_POST['cred_billing_phone'] );
				}
			}

		}

		//date of birth
		if ( isset( $_POST['cred_date_of_birth'] ) && $_POST['cred_date_of_birth'] == '' ) {
			$args->add( 'cred_date_of_birth_error', __( 'Date of birth is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST['cred_date_of_birth'] ) && ! empty( $_POST['cred_date_of_birth'] ) ) {
			update_user_meta( $user_id, 'cred_date_of_birth', $_POST['cred_date_of_birth'] );
		}

		//gender
		if ( isset( $_POST['cred_gender'] ) && $_POST['cred_gender'] == '' ) {
			$args->add( 'cred_gender_error', __( 'Gender is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST['cred_gender'] ) && ! empty( $_POST['cred_gender'] ) ) {
			update_user_meta( $user_id, 'cred_gender', $_POST['cred_gender'] );
		}
		//passport
		if ( isset( $_POST['cred_passport'] ) && $_POST['cred_passport'] == '' ) {
			$args->add( 'cred_passport_error', __( 'Passport is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST['cred_passport'] ) && ! empty( $_POST['cred_passport'] ) ) {
			update_user_meta( $user_id, 'cred_passport', $_POST['cred_passport'] );
		}
		//cred_identification_card
		if ( isset( $_POST['cred_identification_card'] ) && $_POST['cred_identification_card'] == '' ) {
			$args->add( 'cred_identification_card_error', __( 'Identification_card is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST['cred_identification_card'] ) && ! empty( $_POST['cred_identification_card'] ) ) {
			update_user_meta( $user_id, 'cred_identification_card', $_POST['cred_identification_card'] );
		}


	}


	public static function registerAction() {


		return [
			'actions' => [
				'woocommerce_save_account_details_errors' => [
					self::getInstance(),
					'credglv_wooc_edit_profile_save_fields'
				]
			],
			'assets'  => [
				'css' => [
					[
						'id'           => 'credglv-user-profile',
						'isInline'     => false,
						'url'          => '/front/assets/css/credglv-user-profile.css',
						'dependencies' => [ 'credglv-style', 'font-awesome' ]
					],
					[
						'id'       => 'category',
						'isInline' => false,
						'url'      => '/front/assets/css/category.css'
					],
				],
				'js'  => [
					[
						'id'       => 'credglv-main-js',
						'isInline' => false,
						'url'      => '/front/assets/js/main.js',
					]
				]
			],
			'ajax'    => [
				'ajax_update_profile' => [ self::getInstance(), 'updateProfile' ],
			]
		];
	}

}