<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\admin\controllers\PermissionController;
use credglv\core\components\RoleManager;
use credglv\core\RuntimeException;
use credglv\models\UserModel;
use credglv\core\components\Style;
use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;


class LoginController extends FrontController implements FrontControllerInterface {


	public function loginPage() {
		if ( ! credglv()->wp->is_user_logged_in() ) {
			if ( isset( $_GET['redirect_to'] ) && ! empty( $_GET['redirect_to'] ) ) {
				$redirect = $_GET['redirect_to'];
			} else {
				$redirect = home_url();
			}

			return $this->render( 'login', [ 'redirect_to' => $redirect ] );
		} else {
			wp_redirect( home_url() );
			exit;
		}
	}


	function credglv_extra_login_fields() {
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
       
        <div class="phone_login" style="padding-bottom: 0">
            <div class="form-row form-row-wide">
               
                <div class="login_countrycode f-bd">
                    <div class="list_countrycode <?php echo empty( $num_val ) ? 'hide' : '';


					?> f-p-focus">
                        <input type="tel" pattern="[0-9]*" class="woocommerce-phone-countrycode" placeholder="+84"
                               value="<?php echo ! empty( $num_contrycode ) ? $num_contrycode : '' ?>"
                               name="number_countrycode" size="4" readonly>
                        <ul class="digit_cs-list" style="margin: 2% 0">
                            <li class="dig-cc-visible" data-value="+60" data-country="malaysia">(+60) Malaysia</li>
                            <li class="dig-cc-visible" data-value="+84" data-country="vietnam">(+84) Vietnam</li>
                        </ul>
                    </div>
                    <input type="button" id="hide_button" style="display: none" onclick="autofocus_input()">
                    <input type="tel" class="input-number-mobile <?php echo empty( $num_val ) ? '' : 'width80' ?>"
                           name="cred_billing_phone"
						   id="reg_phone"
                           value="<?php echo $num_val; ?>" maxlength="10"/>
							<label class="f-label">Mobile number</label>
                </div>
                <script type="text/javascript">
                    function autofocus_input() {
                        jQuery('#reg_phone').trigger('focus');
                    }
                </script>

            </div>
        </div>


		<?php
	}


	function credglv_extra_otp_login_fields() {
		?>

        <p class="form-row form-row-wide otp-code" data-phone="yes" style="display:none">
            <label for="cred_otp_code_login">
				<?php _e( 'OTP', 'credglv' ); ?>
				<i class="ld-ext-right hide" style="margin-left: 15px;" id="spinning1">
	        	</i>
            </label>
            <input type="tel" class="input-otp-code" pattern="[0-9]*"
                   name="cred_otp_code"
                   id="cred_otp_code_login"
                   value="" maxlength="4"/>

        </p>
        <span class="error_log"></span>
		<?php
	}

	public function credglv_ajax_login() {
		$data = $_POST;

		$userid = UserModel::getUserIDByPhone( $data['phone'] );

		if ( $userid['code'] == 200 ) {

			$third_party = ThirdpartyController::getInstance();
			$res_mes     = $third_party->verify_otp( $data );
			if ( $res_mes['code'] == 200 ) {
				wp_set_auth_cookie( $userid['userID'], true );
				$res_mes['message'] = 'Logged in';
				$res_mes['user_id'] = $userid['userID'];
				$this->responseJson( $res_mes );
			} else {
				$this->responseJson( $res_mes );
			}
		} else {
			$this->responseJson( $userid );
		}
	}

	public function credglv_ajax_sendphone_message_login() {
		$res = array( 'code' => 403, 'message' => __( 'No phone number', 'credglv' ) );


		$user_front = UserModel::getInstance();
		$thirdparty = ThirdpartyController::getInstance();
		$phone      = $_POST['phone'];
		if ( isset( $phone ) && ! empty( $phone ) ) {
			if ( $user_front->checkPhoneIsRegistered( $phone ) ) {
				$data = array( 'phone' => $_POST['phone'] );
				$res  = $thirdparty->sendphone_otp( $data );
				$this->responseJson( $res );
			} else {
				$res['code']    = 404;
				$res['message'] = __( 'Phone is not registered', 'credglv' );
				$this->responseJson( $res );
			}
		} else {
			$res['code']    = 404;
			$res['message'] = __( 'No phone number', 'credglv' );
			$this->responseJson( $res );
		}
		wp_die();
	}

	function credglv_assets_enqueue() {
		global $post, $wp_query;

		wp_register_script( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/js/login.js' );

		if ( isset( $post->ID ) ) {
			if ( $post->ID == get_option( 'woocommerce_myaccount_page_id' ) && ! is_user_logged_in() ) {
				wp_enqueue_style( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/css/cred-reg-log.css' );
				wp_enqueue_script( 'cred-my-account-login-page' );

			}
		}
	}


	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {

		return [
			'actions' => [
//				'template_redirect'            => [ self::getInstance(), 'redirectUserLoggedIn' ],
//				'wp_head'                      => [ self::getInstance(), 'add_custom_js' ],
				'woocommerce_login_form_start' => [ self::getInstance(), 'credglv_extra_login_fields' ],
				'woocommerce_login_form'       => [ self::getInstance(), 'credglv_extra_otp_login_fields' ],
				'wp_enqueue_scripts'           => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [
				'credglv_ajax_login'              => [ self::getInstance(), 'credglv_ajax_login' ],
				'credglv_sendphone_message_login' => [ self::getInstance(), 'credglv_ajax_sendphone_message_login' ],

			],

			'assets' => [
				'css' => [
					[
						'id'           => 'credglv-user-login',
						'isInline'     => false,
						'url'          => '/front/assets/css/login.css',
						'dependencies' => [ 'credglv-style', 'select2' ]
					],
				],
				'js'  => [
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