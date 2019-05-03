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

	/**
	 * Register new endpoints to use inside My Account page.
	 */


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


	/**
	 * Print the customer avatar in My Account page, after the welcome message
	 */
	public function credglv_myaccount_customer_avatar() {
		$current_user = wp_get_current_user();

		echo '<div class="my-account-div"><div class="myaccount_avatar">' . get_avatar( $current_user->user_email, 72, '', $current_user->display_name ) . '</div>';
		?><p><?php
		/* translators: 1: user display name 2: logout url */
		printf(
			__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ),
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
			esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
		);
		?></p>
        </div><?php
	}

	public function add_my_account_menu( $items ) {

		$key = array_search( 'dashboard', array_keys( $items ) );

		if ( $key !== false ) {
			$items = ( array_merge( array_splice( $items, 0, $key + 1 ), array( 'referral' => __( 'Referral', 'credglv' ) ), $items ) );
		} else {
			$items['referral'] = __( 'Referral', 'wmc' );
		}

		return $items;
	}

	public function add_referral_query_var( $vars ) {
		$vars[] = 'referral';

		return $vars;
	}

	public function woocommerce_account_referral_endpoint_hook() {
		$this->render( 'referral', [], false );
	}




	public function init_hook() {
		add_rewrite_endpoint( 'referral', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
		if ( isset( $_GET['ru'] ) && $_GET['ru'] != '' ) {
			setcookie( 'CREDGLV_REFERRAL_CODE', $_GET['ru'], time() + 2628000 );
		}
		global $woocommerce;

		if ( version_compare( $woocommerce->version, '2.6.0', ">=" ) ) {
			/* Hooks for myaccount referral endpoint */
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_my_account_menu' ), 5 );
			add_filter( 'query_vars', array( $this, 'add_referral_query_var' ) );
			add_action( 'woocommerce_account_referral_endpoint', array(
				$this,
				'woocommerce_account_referral_endpoint_hook'
			) );
		} else {
			add_action( 'woocommerce_before_my_account', array( $this, 'woocommerce_account_referral_endpoint_hook' ) );
		}
//		add_filter( 'login_url', array( $this, 'redirectLoginUrl' ), 10, 3 );

	}


	public function redirectLoginUrl( $login_url, $redirect, $force_reauth ) {
		if ( $myaccount_page = credglv_get_woo_myaccount() && ! is_ajax() ) {
			if ( preg_match( '#wp-login.php#', $login_url ) ) {
				if ( ! is_admin() || ! current_user_can( 'administrator' ) ) {
					$login_url = $myaccount_page;
				}
				die;
			}
		}

		return $login_url;
	}

	function credglv_assets_enqueue() {
		/*wp_register_script( 'Treant-js', plugin_dir_url( __DIR__ ) . '/assets/libs/treant-js-master/Treant.js', [
			'jquery-easing',
			'jquery',
			'jquery-ui'
		] );
		wp_register_script( 'Treant-raphael-js', plugin_dir_url( __DIR__ ) . '/assets/libs/treant-js-master/vendor/raphael.js', [
			'jquery-easing',
			'jquery',
			'jquery-ui'
		] );*/
		wp_register_script( 'd3', plugin_dir_url( __DIR__ ) . '/assets/libs/d3/d3.js', [
			'jquery',
			'jquery-ui'
		] );
		wp_register_script( 'credglv-referral', plugin_dir_url( __DIR__ ) . '/assets/js/referral.js', [
			'jquery',
			'jquery-ui',
            'd3'
		] );
		global $wp_query;
		if ( isset( $wp_query->query_vars['referral'] ) ) {
			global $post;
			if ( isset( $post->ID ) ) {
				if ( $post->ID == get_option( 'woocommerce_myaccount_page_id' ) ) {
					wp_enqueue_script( 'Treant-js' );
					wp_enqueue_script( 'Treant-raphael-js' );
					wp_enqueue_script( 'credglv-referral' );
					wp_enqueue_style( 'credglv-main-css', plugin_dir_url( __DIR__ ) . '/assets/css/main.css' );
				}
			}
		}

	}

	public static function registerAction() {


		/*'login_url'             => [
			'\credglv\models\UserModel' => [ 'redirectLoginUrl', 10, 3 ],
		],*/
		return [
			'actions' => [
				'woocommerce_save_account_details_errors' => [
					self::getInstance(),
					'credglv_wooc_edit_profile_save_fields'
				],
				'woocommerce_account_content'             => [
					self::getInstance(),
					'credglv_myaccount_customer_avatar',
					5
				],
				'init'                                    => [ self::getInstance(), 'init_hook' ],
				'wp_enqueue_scripts'                      => [ self::getInstance(), 'credglv_assets_enqueue' ],

			],
			'assets'  => [
				/*'css' => [
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
				],*/
				'js' => [
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