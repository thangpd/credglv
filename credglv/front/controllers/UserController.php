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
use credglv\models\Instructor;
use credglv\models\UserModel;
use credglv\core\components\Style;
use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;
use mysql_xdevapi\Exception;


class UserController extends FrontController implements FrontControllerInterface {


	const METAKEY_PHONE = 'cred_billing_phone';

	public function profilePage() {
		return $this->checkLogin( 'user-profile' );
	}

	public function checkLogin( $template ) {
		if ( credglv()->wp->is_user_logged_in() ) {
			$user = wp_get_current_user();

			return $this->render( $template, [ 'user' => $user ] );
		} else {
			if ( class_exists( 'WooCommerce' ) && get_option( 'woocommerce_myaccount_page_id' ) ) {
				wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
			} else {
				echo '<div class="container"><div class="user-login-wrapper"><a class="credglv-btn credglv-btn-primary" href="' . wp_login_url() . '">' . __( "Login", "credglv" ) . '</a></div></div>';
			}

			return '';
		}
	}

	/**
	 * Update user profile
	 * @return bool
	 */
	public function updateProfile() {
		$user_id = wp_get_current_user()->ID;
		$res     = [];
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( count( $_POST ) ) {
			$list_posts = [];

			//check if user have changed password
			if ( isset( $_POST['password'] ) ) {
				$user = get_userdata( $user_id );
				if ( $user && wp_check_password( $_POST['password']['old'], $user->user_pass, $user_id ) ) {
					if ( $_POST['password']['new'] == $_POST['password']['confirm'] ) {
						//$res['message'] = 'Success change password !';
						$new_pass = $_POST['password']['new'];
						wp_set_password( $new_pass, $user_id );

					} else {
						$res['message'] = 'Password confirm invalid !';
					}
				} else {
					$res['message'] = 'Old password invalid !';
				}
				unset( $_POST['password'] );
			}

			// edit meta user
			if ( isset( $_POST['meta'] ) && count( $_POST['meta'] ) ) {
				foreach ( $_POST['meta'] as $key => $meta ) {
					update_user_meta( $user_id, $key, $meta );
				}
				unset( $_POST['meta'] );
			}

			if ( isset( $_POST ) ) {

				if ( isset( $_POST['first_name'] ) && $_POST['first_name'] !== '' ) {
					$list_posts['display_name'] = $_POST['first_name'];
				}

				if ( isset( $_POST['last_name'] ) && $_POST['last_name'] !== '' ) {
					$list_posts['display_name'] .= $_POST['last_name'];
				}

				foreach ( $_POST as $key => $post ) {
					$list_posts[ $key ] = esc_attr( $post );
				}
				$list_posts['ID'] = $user_id;
				wp_update_user( $list_posts );
			}

			return $this->responseJson( $res );
		}
	}

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


	function credglv_wooc_edit_profile_save_fields( $args ) {
		global $wpdb;
		$user_id = get_current_user_ID();


		if ( isset( $_POST['billing_phone'] ) && $_POST['billing_phone'] == '' ) {
			$args->add( 'billing_phone_name_error', __( 'Mobile number is required.', 'woocommerce' ) );
		}
		if ( isset( $_POST['billing_phone'] ) && ! empty( $_POST['billing_phone'] ) ) {
			$mobile_num_result = $wpdb->get_var( "select B.ID from " . $wpdb->prefix . "usermeta as A join " . $wpdb->prefix . "users as B where meta_key='" . self::METAKEY_PHONE . "' and meta_value='" . $_POST['billing_phone'] . "' and A.user_id =  b.ID " );
			if ( isset( $mobile_num_result ) ) {
				if ( $user_id != $mobile_num_result ) {
					wc_add_notice( __( 'Mobile Number is already used.', 'woocommerce' ), 'error' );

					return;
				} else {
					update_user_meta( $user_id, 'billing_phone', $_POST['billing_phone'] );

					return $_POST['billing_phone'];
				}
			} else {
				update_user_meta( $user_id, 'billing_phone', $_POST['billing_phone'] );

				return $_POST['billing_phone'];
			}
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