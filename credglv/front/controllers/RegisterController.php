<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\core\components\RoleManager;
use credglv\models\UserModel;
use credglv\core\interfaces\FrontControllerInterface;
use http\Client\Curl\User;
use PHPUnit\Runner\Exception;


class RegisterController extends FrontController implements FrontControllerInterface {

	/**
	 * referrer_ajax_search
	 */
	public function referrer_ajax_search() {
		if ( isset( $_GET['q'] ) ) {
			$results = array();

			// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
			$args  = array(
//			'blog_id'      => $GLOBALS['blog_id'],
				'search'         => $_GET['q'] . '*',
				'search_columns' => array( 'user_nicename', 'display_name', 'login' ),
//				'role__in'       => RoleManager::getlist_member(),
				'role__not_in'   => array(),
				'meta_key'       => '',
				'meta_value'     => '',
				'meta_compare'   => '',
				'meta_query'     => array(),
				'date_query'     => array(),
				'include'        => array(),
				'exclude'        => array(),
				'orderby'        => 'login',
				'order'          => 'ASC',
				'offset'         => '',
				'number'         => '',
				'count_total'    => false,
				'fields'         => 'all',
				'who'            => '',
			);
			$users = get_users( $args );
			foreach ( $users as $key => $value ) {
				$results[] = array( 'id' => $value->data->ID, 'text' => $value->data->user_nicename );
			}

			echo json_encode( array( 'results' => $results, 'pagination' => array( 'more' => true ) ) );
		} else {
			echo 'no $_GET[q]';
		}
		die;

	}


//add_action( 'woocommerce_save_account_details_errors', array( $this, 'credglv_edit_save_fields' ), 10, 1 );
	function credglv_validate_extra_register_fields_update( $customer_id ) {
		if ( isset( $_POST['input_referral'] ) && ! empty( $_POST['input_referral'] ) ) {
			$parent_ref = $_POST['input_referral'];
		} else {
			$parent_ref = '';
		}
		try {
			$user                  = new UserModel();
			$user->user_id         = $customer_id;
			$user->referral_parent = $parent_ref;
			$user->referral_code   = $user->get_referralcode();
			$user->save();
		} catch ( Exception $e ) {
			throw ( new Exception( 'cant add user referral ' ) );
		}
		if ( isset( $_POST['cred_billing_phone'] ) && isset( $_POST['number_countrycode'] ) ) {
			update_user_meta( $customer_id, 'cred_billing_phone', sanitize_text_field( $_POST['number_countrycode'] ) . sanitize_text_field( $_POST['cred_billing_phone'] ) );
		} else {
			throw( new Exception( 'missing phone or number countrycode' ) );
		}
	}

//add_action( 'woocommerce_register_post', array( $this, 'mrp_wooc_validate_extra_register_fields' ), 10, 3 );

	function credglv_validate_extra_register_fields( $username, $email, $validation_errors ) {
		global $wpdb;
		if ( isset( $_POST['cred_billing_phone'] ) && empty( $_POST['cred_billing_phone'] ) ) {
			$validation_errors->add( 'billing_phone_name_error', __( 'Mobile number is required.', 'woocommerce' ) );
		}
		if ( isset( $_POST['cred_billing_phone'] ) && ! empty( $_POST['cred_billing_phone'] ) && isset( $_POST['number_countrycode'] ) && ! empty( $_POST['number_countrycode'] ) ) {
			$mobile_num_result = $wpdb->get_var( "select user_id from " . $wpdb->prefix . "usermeta  where meta_key='cred_billing_phone' and meta_value='" . $_POST['number_countrycode'] . $_POST['cred_billing_phone'] . "' " );
			if ( isset( $mobile_num_result ) && ! empty( $mobile_num_result ) ) {
				$validation_errors->add( 'billing_phone_name_error', __( 'Mobile Number is already registred.', 'credglv' ) );
			} else {
				if ( isset( $_POST['cred_otp_code'] ) && ! empty( $_POST['cred_otp_code'] ) ) {
//			$_POST['number_countrycode'].$_POST['cred_billing_phone']
					$data = array(
						'phone' => $_POST['number_countrycode'] . $_POST['cred_billing_phone'],
						'otp'   => $_POST['cred_otp_code']
					);

					$third_party = ThirdpartyController::getInstance();

					$res = $third_party->verify_otp( $data );
					if ( $res['code'] != 200 ) {
						$validation_errors->add( 'otp_error', $res['message'], 'error' );
					}
				}
			}
		}

		return $validation_errors;
	}

	/**
	 * Extra otp register fields
	 */
	function credglv_extra_otp_register_fields() {
		$user_ref='';
		if ( isset( $_GET['ru'] ) ) {
			$user_ref = $_GET['ru'];
		} elseif ( isset( $_COOKIE[ UserController::METAKEY_COOKIE ] ) ) {
			$user_ref = $_COOKIE[ UserController::METAKEY_COOKIE ];
		}

		$user = get_user_by( 'login', $user_ref );


		$option = '';
		if ( $user ) {
			$option = '<option value="' . $user->data->ID . '">' . $user->data->user_login . '</option>';
		}
		?>
        <p class="form-row form-row-wide mt-20">
            <label for="reg_referral">
				<?php _e( 'Introducer', 'credglv' ); ?>
            </label>

            <select id="input_referral" name="input_referral" class="input-referral" style="width:100%">
				<?php echo $option ?>
            </select><!--
            <input type="text" class="input-referral"
                   name="input_referral"
                   id="reg_referral"
                   value="" maxlength="10"/>-->
        </p>
        <p class="form-row form-row-wide otp-code hide" data-phone="yes">
            <label for="cred_otp_code">
				<?php _e( 'OTP', 'credglv' ); ?> <span class="required">*</span>
            </label>
            <input type="tel" class="input-otp-code" pattern="[0-9]*"
                   name="cred_otp_code"
                   id="cred_otp_code"
                   maxlength="4"/>
        </p>
        <span class="error_log"></span>

		<?php
	}

	/**
	 * Extra register fields
	 */
	function credglv_extra_register_fields() {
		$num_val = '';
		if ( is_user_logged_in() ) {
			$user_id        = get_current_user_ID();
			$num_val        = get_user_meta( $user_id, 'cred_billing_phone', true );
			$num_contrycode = get_user_meta( $user_id, 'number_countrycode', true );
			if ( isset( $_POST['cred_billing_phone'] ) ) {
				$num_val = $_POST['cred_billing_phone'];
			}
			if ( isset( $_POST['number_countrycode'] ) ) {
				$num_contrycode = $_POST['number_countrycode'];
			}
		} else {
			if ( isset( $_POST['cred_billing_phone'] ) ) {
				$num_val = $_POST['cred_billing_phone'];
			}
			if ( isset( $_POST['number_countrycode'] ) ) {
				$num_contrycode = $_POST['number_countrycode'];
			}
		}

		?>

        <div class="form-row form-row-wide">
           
            <div class="login_countrycode custom-mg f-bd mt-20">

                <div class="list_countrycode <?php echo empty( $num_val ) ? 'hide' : ''; ?> f-p-focus">
                    <input type="text" class="woocommerce-phone-countrycode" placeholder="+84"
                           value="<?php echo ! empty( $num_contrycode ) ? $num_contrycode : '' ?>"
                           name="number_countrycode" size="4">
                    <ul class="digit_cs-list">
                        <li class="dig-cc-visible" data-value="+60" data-country="malaysia">(+60) Malaysia</li>
                        <li class="dig-cc-visible" data-value="+84" data-country="vietnam">(+84) Vietnam</li>
                    </ul>
                </div>
                <input type="tel" pattern="[0-9]*"
                       class="input-number-mobile r-mb <?php echo empty( $num_val ) ? '' : 'width80' ?>"
					   name="cred_billing_phone"
                       id="reg_phone_register"
					   value="<?php echo $num_val; ?>" maxlength="10"/>
					<label class="f-label">Mobile number</label>
            </div>

        </div>


		<?php
	}

	function credglv_assets_enqueue() {
		global $post, $wp_query;


		wp_register_script( 'cred-my-account-detail', plugin_dir_url( __DIR__ ) . '/assets/js/account-details.js' );
		wp_register_script( 'cred-my-account-register-page', plugin_dir_url( __DIR__ ) . '/assets/js/register.js' );


		if ( isset( $post->ID ) ) {
			if ( $post->ID == get_option( 'woocommerce_myaccount_page_id' ) ) {

				if ( isset( $wp_query->query_vars['edit-account'] ) ) {
					wp_enqueue_script( 'cred-my-account-detail' );
				}
				wp_enqueue_style( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/css/cred-reg-log.css' );
			}


		}
		$page_name = get_query_var( 'name' );
		if ( ! credglv()->wp->is_user_logged_in() && $page_name == 'register' ) {
			wp_enqueue_style( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/css/cred-reg-log.css' );
			wp_enqueue_script( 'cred-my-account-register-page' );

		}


	}

	public function registerPage() {

		$user = UserController::getInstance();

		$data      = [];
		$page_name = get_query_var( 'name' );

		if ( credglv()->wp->is_user_logged_in() && $page_name == 'register' ) {
			if ( current_user_can( 'administrator' ) ) {
				wp_redirect( admin_url() );
				exit;
			} else {

			}
		} else {
			return $this->render( 'register', [ 'data' => $data ] );
		}
	}


	public function credglv_ajax_register() {

		$data = $_POST;

		$userid = UserModel::getUserIDByPhone( $data['phone'] );


		if ( $userid['code'] !== 200 ) {

			$third_party = ThirdpartyController::getInstance();
			$res_mes     = $third_party->verify_otp( $data );

			if ( $res_mes['code'] == 403 ) {
				$third_party->sendphone_otp( $data );
			}

			if ( $res_mes['code'] == 200 ) {

				$userdata      = array(
					'ID'         => 0,    //(int) User ID. If supplied, the user will be updated.
					'user_pass'  => '',   //(string) The plain-text user password.
					'user_login' => $data['username'],   //(string) The user's login username.
					'user_email' => $data['user_email'],   //(string) The user email address.
					'show_admin_bar_front' => false,   //(string) The user email address.
				);
				$userId        = wp_insert_user( $userdata );
				$current_user  = get_user_by( 'id', $userId );
				$current_email = $current_user->user_email;

				$account_email = sanitize_email( $data['user_email'] );
				if ( email_exists( $account_email ) && $account_email !== $current_email ) {
					$this->responseJson( array(
						'code'    => 200,
						'message' => __( 'This email address is already registered.', 'woocommerce' )
					) );
				}

				//On success
				if ( ! is_wp_error( $userId ) ) {
					update_user_meta( $userId, UserController::METAKEY_PHONE, $data['phone'] );
					wp_update_user( array( 'ID' => $userId, 'user_email' => $data['email'] ) );
					wp_set_auth_cookie( $userId, true );


					$this->credglv_validate_extra_register_fields_update($userId);
					$this->responseJson( array( 'code' => 200, 'message' => "User created : " . $userId ) );
				} else {
					$this->responseJson( array( 'code' => 404, 'message' => 'Cant create user' ) );
				}
			} else {
				$this->responseJson( $res_mes );
			}
		} else {
			$this->responseJson( $userid );
		}
	}

	public function credglv_ajax_sendphone_message_register() {
		$res = array( 'code' => 404, 'message' => __( 'Phone is registed', 'credglv' ) );


		$user_front = UserModel::getInstance();
		$thirdparty = ThirdpartyController::getInstance();
		$phone      = $_POST['phone'];
		if ( isset( $phone ) && ! empty( $phone ) ) {
			if ( ! $user_front->checkPhoneIsRegistered( $phone ) ) {
				$data = array( 'phone' => $_POST['phone'] );
				$res  = $thirdparty->sendphone_otp( $data );
				$this->responseJson( $res );
			} else {
				$res['code']    = 404;
				$res['message'] = __( 'Phone is registered', 'credglv' );
				$this->responseJson( $res );
			}
		} else {
			$res['code']    = 404;
			$res['message'] = __( 'No phone number', 'credglv' );
			$this->responseJson( $res );
		}
		wp_die();
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [

				'woocommerce_register_form_start' => [ self::getInstance(), 'credglv_extra_register_fields' ],
				'woocommerce_register_form'       => [
					self::getInstance(),
					'credglv_extra_otp_register_fields'
				],
//				'woocommerce_save_account_details_errors' => [ self::getInstance(), 'credglv_edit_save_fields' ],
				'woocommerce_register_post'       => [
					self::getInstance(),
					'credglv_validate_extra_register_fields',
					10,
					3,
				],
				'woocommerce_created_customer'    => [
					self::getInstance(),
					'credglv_validate_extra_register_fields_update'
				],
				'wp_enqueue_scripts'              => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [
				'referrer_ajax_search'                    => [ self::getInstance(), 'referrer_ajax_search' ],
				'credglv_ajax_register'                   => [ self::getInstance(), 'credglv_ajax_register' ],
				'credglv_ajax_sendphone_message_register' => [
					self::getInstance(),
					'credglv_ajax_sendphone_message_register'
				],

			],
			'pages'   => [
				'front' => [
					'register' =>
						[
							'registerPage',
							[
								'title' => __( 'Be a GLV Member and enjoy many benefits', 'credglv' ),
//                                'single' => true
							]
						],

				]
			],
			'assets'  => [
				'css' => [
					[
						'id'           => 'credglv-user-register',
						'isInline'     => false,
						'url'          => '/front/assets/css/register.css',
						'dependencies' => [ 'credglv-style', 'select2' ]
					],
				],
				'js'  => [
					/*[
						'id'       => 'credglv-register-page-js',
						'isInline' => false,
						'url'      => '/front/assets/js/register.js',
					],*/
					[
						'id'       => 'credglv-main-js',
						'isInline' => false,
						'url'      => '/front/assets/js/main.js',
					]
				]
			]
		];
	}

}