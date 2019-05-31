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
        <p class="form-row form-row-wide">
            <label for="login-with-phone"> <input type="radio" id="login-with-phone" name="selector" checked>
                <span><?php echo __( 'With phone number', 'credglv' ); ?></span></label>
            <label for="login-with-user"> <input type="radio" id="login-with-user" name="selector">
                <span><?php echo __( 'With username/ email', 'credglv' ); ?></span></label>
        </p>
        <div class="phone_login">
            <div class="form-row form-row-wide">
                <label for="reg_phone">
					<?php _e( 'Mobile number', 'credglv' ); ?> <span class="required">*</span>
                </label>

                <div class="login_countrycode">
                    <div class="list_countrycode <?php echo empty( $num_val ) ? 'hide' : '';


					?>">
                        <input type="tel" pattern="[0-9]*" class="woocommerce-phone-countrycode" placeholder="+84"
                               value="<?php echo ! empty( $num_contrycode ) ? $num_contrycode : '' ?>"
                               name="number_countrycode" size="4">
                        <ul class="digit_cs-list">
                            <li class="dig-cc-visible" data-value="+60" data-country="malaysia">(+60) Malaysia</li>
                            <li class="dig-cc-visible" data-value="+84" data-country="vietnam">(+84) Vietnam</li>
                        </ul>
                    </div>
                    <input type="button" id="hide_button" style="display: none" onclick="autofocus_input()">
                    <input type="tel" class="input-number-mobile <?php echo empty( $num_val ) ? '' : 'width80' ?>"
                           name="cred_billing_phone"
                           id="reg_phone"
                           value="<?php echo $num_val; ?>" maxlength="10"/>

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
				<?php _e( 'OTP', 'credglv' ); ?> <span class="required">*</span>
            </label>
            <input type="tel" class="input-otp-code" pattern="[0-9]*"
                   name="cred_otp_code"
                   id="cred_otp_code_login"
                   value="" maxlength="4"/>

        </p>
        <span class="error_log"></span>
		<?php
	}

	public function credglv_login() {
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
			],
			'ajax'    => [
				'credglv_login' => [ self::getInstance(), 'credglv_login' ],

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
						'id'       => 'credglv-login-page-js',
						'isInline' => false,
						'url'      => '/front/assets/js/login.js',
					],
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