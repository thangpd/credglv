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


	public function redirectUserLoggedIn() {
		$page_name = get_query_var( 'name' );
		if ( credglv()->wp->is_user_logged_in() && $page_name == credglv()->config->getUrlConfigs( 'credglv_login' ) ) {
			if ( current_user_can( 'administrator' ) ) {
				wp_redirect( admin_url() );
				exit;
			} else {
				wp_redirect( home_url() );
				exit;
			}
		}
	}

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


	/**
	 * Login - Register
	 */
// wp-admin/admin-ajax.php?action=credglv_login
// admin_url('admin-ajax.php');
	public function credglv_login() {
		$code        = 403;
		$message     = esc_html__( 'Sorry we could not log you in. The credentials supplied were not recognised.', 'educef' );
		$redirect_to = '';
		if ( ! empty( $_POST['data'] ) ) {
			$data = $_POST['data'];
//			if ( ThirdpartyController::getInstance()->verify_captcha( $data ) ) {
			if ( ThirdpartyController::getInstance()->verify_captcha( $data ) ) {
				$creds       = array(
					'user_login'    => $data['email'],
					'user_password' => $data['password'],
					'remember'      => false,
				);
				$redirect_to = ! empty( $data['redirect_to'] ) ? $data['redirect_to'] : '';
				$user        = wp_signon( $creds );
				if ( $user && is_user_member_of_blog( $user->ID, get_current_blog_id() ) && ! is_wp_error( $user ) ) {
					$code    = '200';
					$message = 'Login successful!';
				} else {
					$message = esc_html__( 'Your account is not correct !', 'educef' );
					wp_destroy_current_session();
					wp_clear_auth_cookie();
				}
			} else {
				$this->responseJson( array( 'code' => 403, 'message' => 'captcha verified fail' ) );
			}
		} else {
			$this->responseJson( array( 'code' => 403, 'message' => 'Login form no data' ) );
		}
		header( 'Content-Type: application/json' );
		echo json_encode( compact( 'code', 'message', 'redirect_to' ) );
		die;
	}

	public function credglv_login_otp() {
		$code        = 403;
		$message     = esc_html__( 'Sorry we could not log you in. The credentials supplied were not recognised.', 'educef' );
		$redirect_to = '';
		if ( ! empty( $_POST['data'] ) ) {
			$data = $_POST['data'];
//			if ( ThirdpartyController::getInstance()->verify_captcha( $data ) ) {
			if ( ThirdpartyController::getInstance()->verify_otp( $data ) ) {
				$creds       = array(
					'user_login'    => $data['email'],
					'user_password' => $data['password'],
					'remember'      => false,
				);
				$redirect_to = ! empty( $data['redirect_to'] ) ? $data['redirect_to'] : '';
				$user        = wp_signon( $creds );
				if ( $user && is_user_member_of_blog( $user->ID, get_current_blog_id() ) && ! is_wp_error( $user ) ) {
					$code    = '200';
					$message = 'Login successful!';
				} else {
					$message = esc_html__( 'Your account is not correct !', 'educef' );
					wp_destroy_current_session();
					wp_clear_auth_cookie();
				}
			} else {
				$this->responseJson( array( 'code' => 403, 'message' => 'captcha verified fail' ) );
			}
		} else {
			$this->responseJson( array( 'code' => 403, 'message' => 'Login form no data' ) );
		}
		header( 'Content-Type: application/json' );
		echo json_encode( compact( 'code', 'message', 'redirect_to' ) );
		die;
	}


	public function add_custom_js() {
//		echo '<script src="https://www.google.com/recaptcha/api.js?render=6Lc38psUAAAAAJuh9FtinaKCMZPGnTIYk2VFSrlA" async defer >';
		echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		return [
			'actions' => [
				'template_redirect' => [ self::getInstance(), 'redirectUserLoggedIn' ],
				'wp_head'           => [ self::getInstance(), 'add_custom_js' ],
			],
			'ajax'    => [
				'credglv_login' => [ self::getInstance(), 'credglv_login' ],
			],
			'pages'   => [
				'front' => [
					credglv()->config->getUrlConfigs( 'credglv_login' ) =>
						[
							'loginPage',
							[
								'title' => __( 'Cred GLV - Login', 'credglv' ),
//                                'single' => true
							]
						],
				]
			],
			'assets'  => [
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