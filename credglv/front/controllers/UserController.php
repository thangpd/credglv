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
use credglv\models\OrderModel;
use credglv\models\UserModel;
use credglv\core\components\Style;
use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;
use mysql_xdevapi\Exception;


class UserController extends FrontController implements FrontControllerInterface {


	const METAKEY_PHONE = 'cred_billing_phone';
	const METAKEY_PIN = 'cred_user_pin';

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

		if ( isset( $_POST[ self::METAKEY_PHONE ] ) && $_POST[ self::METAKEY_PHONE ] == '' ) {
			$args->add( 'billing_phone_name_error', __( 'Mobile number is required.', 'woocommerce' ) );

			return $_POST;
		}
		if ( isset( $_POST[ self::METAKEY_PHONE ] ) && ! empty( $_POST[ self::METAKEY_PHONE ] ) ) {
			$current_phone = UserController::getPhoneByUserID( $user_id );
			if ( $_POST['cred_billing_phone'] !== $current_phone ) {
				$mobile_num_result = self::getUserIDByPhone( $_POST[ self::METAKEY_PHONE ] );
				if ( isset( $mobile_num_result['code'] ) && $mobile_num_result['code'] == 200 ) {
					if ( $user_id != $mobile_num_result ) {
						wc_add_notice( __( 'Mobile Number is already used.', 'woocommerce' ), 'error' );

						return $_POST;
					} else {
						update_user_meta( $user_id, self::METAKEY_PHONE, $_POST[ self::METAKEY_PHONE ] );
					}
				} else {
					update_user_meta( $user_id, self::METAKEY_PHONE, $_POST[ self::METAKEY_PHONE ] );
				}
			}

		}

		if ( ( isset( $_POST[ self::METAKEY_PIN ] ) && $_POST[ self::METAKEY_PIN ] == '' ) ) {
			if ( empty( get_user_meta( $user_id, \credglv\front\controllers\UserController::METAKEY_PIN, true ) ) ) {
				$args->add( 'user_pin_name_error', __( 'Pin is required.', 'woocommerce' ) );

				return $_POST;
			}
		} else {
			update_user_meta( $user_id, self::METAKEY_PIN, $_POST[ self::METAKEY_PIN ] );
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

		$key = array_search( 'edit-account', array_keys( $items ) );

		if ( $key !== false ) {
			$items = (
			array_merge(
				array_splice( $items, 0, $key + 1 ),
				array(
					'payment'       => __( 'Payment', 'credglv' ),
					'profile'       => __( 'Profile', 'credglv' ),
					'referral'      => __( 'Referral', 'credglv' ),
					'cash_redeem'   => __( 'Cash Redeem', 'credglv' ),
					'local_redeem'  => __( 'Local Redeem', 'credglv' ),
					'point_history' => __( 'History Log', 'credglv' ),
				),
				$items ) );
		} else {
			$items['payment']       = __( 'Payment', 'credglv' );
			$items['profile']       = __( 'Profile', 'credglv' );
			$items['referral']      = __( 'Referral', 'credglv' );
			$items['cash_redeem']   = __( 'Cash Redeem', 'credglv' );
			$items['local_redeem']  = __( 'Local Redeem', 'credglv' );
			$items['point_history'] = __( 'History Log', 'credglv' );
		}

		return $items;
	}

	public function add_referral_query_var( $vars ) {
		$vars[] = 'referral';
		$vars[] = 'payment';
		$vars[] = 'profile';
		$vars[] = 'point_history';
		$vars[] = 'cash_redeem';
		$vars[] = 'local_redeem';

		return $vars;
	}

	public function woocommerce_account_referral_endpoint_hook() {
		$this->render( 'referral', [], false );
	}

	public function woocommerce_account_payment_endpoint_hook() {
		$this->render( 'payment', [], false );
	}

	public function woocommerce_account_profile_endpoint_hook() {
		$this->render( 'profile', [], false );
	}

	public function woocommerce_account_point_history_endpoint_hook() {
		$this->render( 'point_history', [], false );
	}

	public function woocommerce_account_local_redeem_endpoint_hook() {
		$format = '<p class="tr">
            <span>
        <span class="title">#<br class="no-style-break"></span>
        %1$s
      </span>
            <br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Log: <br class="no-style-break"></span>
        %2$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Status: <br class="no-style-break"></span>
        %3$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Amount: <br class="no-style-break"></span>
        %4$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
      <span>
        <span class="title">Create Date: <br class="no-style-break"></span>
       %5$s
      </span><br class="no-style-break"><br class="no-style-break">
        </p>
        <p class="spacer">&nbsp;</p>
';

		$order               = new OrderModel();
		$data                = [];
		$data['html']        = '';
		$data['total_cash']  = $order->getTotalUserCash( get_current_user_id(), 1, OrderModel::ORDER_TYPE_CASH );
		$data['total_local'] = $order->getTotalUserCash( get_current_user_id(), 1, OrderModel::ORDER_TYPE_LOCAL );
		$records             = $order->findAllrecordsUser( get_current_user_id(), OrderModel::ORDER_TYPE_LOCAL );
		if ( ! empty( $records ) ) {
			foreach ( $records as $val ) {
				$log = json_decode( $val->data );

				$log         = $log->message;
				$status      = $val->active == 0 ? 'Pending' : 'Completed';
				$amount      = $val->amount;
				$create_date = $val->created_date;

				$data['html'] .= sprintf( $format, $val->id, $log, $status, $amount, $create_date );
			}
		}
		$this->render( 'redeem_local', [ 'data' => $data ], false );
	}

	public function woocommerce_account_cash_redeem_endpoint_hook() {

		$format = '<p class="tr">
            <span>
        <span class="title">#<br class="no-style-break"></span>
        %1$s
      </span>
            <br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Log: <br class="no-style-break"></span>
        %2$s
      </span><br class="no-style-break"><br class="no-style-break">

            <span>
        <span class="title">Status: <br class="no-style-break"></span>
        %3$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Amount: <br class="no-style-break"></span>
        %4$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
      <span>
        <span class="title">Create Date: <br class="no-style-break"></span>
       %6$s
      </span><br class="no-style-break"><br class="no-style-break">
        </p>
        <p class="spacer">&nbsp;</p>
';

		$order              = new OrderModel();
		$data               = [];
		$data['html']       = '';
		$data['total_cash'] = $order->getTotalUserCash( get_current_user_id() );
		$records            = $order->findAllrecordsUser( get_current_user_id() );
		if ( ! empty( $records ) ) {
			foreach ( $records as $val ) {
				$log = json_decode( $val->data );

				$log         = $log->message;
				$status      = $val->active == 0 ? 'Pending' : 'Completed';
				$amount      = $val->amount;
				$fee         = $val->fee;
				$create_date = $val->created_date;

				$data['html'] .= sprintf( $format, $val->id, $log, $status, $amount, $fee, $create_date );
			}
		}
		$this->render( 'redeem_cash', [ 'data' => $data ], false );
	}


	public function init_hook() {
		if ( isset( $_GET['ru'] ) && $_GET['ru'] != '' ) {
			setcookie( 'CREDGLV_REFERRAL_CODE', $_GET['ru'], time() + 2628000 );
		}

		$arr_pages = $this->add_referral_query_var( array() );
		foreach ( $arr_pages as $val ) {
			add_rewrite_endpoint( $val, EP_ROOT | EP_PAGES );
			add_action( 'woocommerce_account_' . $val . '_endpoint', array(
				$this,
				'woocommerce_account_' . $val . '_endpoint_hook'
			) );
		}
		flush_rewrite_rules();


		/* Hooks for myaccount referral endpoint */
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_my_account_menu' ), 5 );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_referral_query_var' ) );


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
		if ( isset( $wp_query->query_vars['cash_redeem'] ) || isset( $wp_query->query_vars['local_redeem'] ) ) {
			global $post;
			if ( isset( $post->ID ) ) {
				if ( $post->ID == get_option( 'woocommerce_myaccount_page_id' ) ) {
					wp_enqueue_style( 'credglv-main-css', plugin_dir_url( __DIR__ ) . '/assets/css/main.css' );
					wp_enqueue_script( 'credglv-redeem-js', plugin_dir_url( __DIR__ ) . '/assets/js/redeem.js' );
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
				/*'woocommerce_account_content'             => [
					self::getInstance(),
					'credglv_myaccount_customer_avatar',
					5
				],*/
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