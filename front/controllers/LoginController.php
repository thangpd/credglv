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
		return $this->checkLogin( 'login' );
	}

	public function redirectUserLoggedIn() {
		$page_name = get_query_var( 'name' );
		if ( credglv()->wp->is_user_logged_in() && $page_name == credglv()->config->getUrlConfigs( 'credglv_login' ) ) {
			PermissionController::redirect_credglvUser();
			if ( current_user_can( 'administrator' ) ) {
				wp_redirect( admin_url() );
				exit;
			} else {
				wp_redirect( home_url() );
				exit;
			}
		}
	}

	public function checkLogin( $template ) {
		if ( ! credglv()->wp->is_user_logged_in() ) {
			if ( isset( $_GET['redirect_to'] ) && ! empty( $_GET['redirect_to'] ) ) {
				$redirect = $_GET['redirect_to'];
			} else {
				$redirect = home_url();
			}

			return $this->render( $template, [ 'redirect_to' => $redirect ] );
		}
	}


	/**
	 * Login - Register
	 */
// wp-admin/admin-ajax.php?action=credglv_login
// admin_url('admin-ajax.php');
	public function credglv_login() {
		$success     = false;
		$message     = esc_html__( 'Sorry we could not log you in. The credentials supplied were not recognised.', 'educef' );
		$redirect_to = '';
		if ( ! empty( $_POST ) ) {
			$creds       = array(
				'user_login'    => $_POST['email'],
				'user_password' => $_POST['password'],
				'remember'      => false,
			);
			$redirect_to = $_POST['redirect_to'];
			$user        = wp_signon( $creds );
			if ( $user && is_user_member_of_blog( $user->ID, get_current_blog_id() ) && ! is_wp_error( $user ) ) {
				$success = true;
				$message = 'Login successful!';
			} else {
				$message = esc_html__( 'Your account is not correct !', 'educef' );
				wp_destroy_current_session();
				wp_clear_auth_cookie();
			}
		}
		header( 'Content-Type: application/json' );
		echo json_encode( compact( 'success', 'message', 'redirect_to' ) );
		die;
	}

// wp-admin/admin-ajax.php?action=credglv_register
// admin_url('admin-ajax.php');
	public function credglv_register() {
		$success = true;
		$message = $redirect_to = '';

		// Validation
		if ( empty( $_POST['email'] ) || empty( $_POST['display_name'] ) ) {
			$success = false;
			$message = esc_html__( 'Email and Display name are required !', 'educef' );
		}

		$enable_recaptcha         = slz_get_db_settings_option( 'recaptcha-picker/enable-recaptcha', 'disable' );
		$recaptcha_api_secret_key = slz_get_db_settings_option( 'recaptcha-picker/enable/recaptcha-api-secret-key', '' );
		if ( $success && $enable_recaptcha == 'enable' ) {
			if ( empty( $_POST['g-recaptcha-response'] ) ) {
				$success = false;
				$message = esc_html__( "Captcha not found !", 'educef' );
			} else {
				$res = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=" . esc_attr( $recaptcha_api_secret_key ) . "&response=" . esc_attr( $_POST['g-recaptcha-response'] ) . "&remoteip=" . $_SERVER['REMOTE_ADDR'] );
				$res = json_decode( $res, true );
				if ( ! $res['success'] ) {
					$success = false;
					$message = $res['error-codes'][0];
				}
			}
		}

		if ( $success ) {
			$user_email   = esc_sql( $_POST['email'] );
			$display_name = esc_sql( $_POST['display_name'] );

			if ( email_exists( $user_email ) ) {
				$success = false;
				$message = esc_html__( 'Email already exists !', 'educef' );
			} else {
				$user_id = register_new_user( $user_email, $user_email );
				if ( $user_id && ! is_wp_error( $user_id ) ) {
					$user_data = array(
						'ID'           => $user_id,
						'role'         => 'credglv_student',
						'display_name' => $display_name,
						'nickname'     => $display_name,
					);
					wp_update_user( $user_data );
					$message = esc_html__( "Your account was created ! Please confirm at email ", 'educef' ) . $user_email;
				} else {
					$success = false;
					$message = esc_html__( 'Can not create User !', 'educef' );
				}
			}
		}

		header( 'Content-Type: application/json' );
		echo json_encode( compact( 'success', 'message', 'redirect_to' ) );
		die;
	}


	/**
	 * Edit user info
	 */
	public function registerPage() {
		$this->updateProfile();

		return $this->checkLogin( 'register' );
	}

	/**
	 * Update user profile
	 * @return bool
	 */
	public function updateProfile() {
		$user_id = wp_get_current_user()->ID;

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( count( $_POST ) ) {
			$list_posts = [];

			//check if user have changed password
			foreach ( $_POST['password'] as $key => $pass ) {
				if ( $pass == '' ) {
					unset( $_POST['password'] );
					break;
				}
			}

			if ( isset( $_POST['password'] ) ) {
				$user = get_userdata( $user_id );
				if ( $user && wp_check_password( $_POST['password']['old'], $user->user_pass, $user_id ) ) {
					if ( $_POST['password']['new'] == $_POST['password']['confirm'] ) {
						wp_set_password( $_POST['password']['new'], $user_id );
					} else {
						$errorMess = 'Password confirm invalid !';
					}
				} else {
					$errorMess = 'Old password invalid !';
				}
				if ( isset( $errorMess ) ) {
					echo '<center class="text-danger"><h3><b>' . $errorMess . '</b></h3></center>';
				}
			}

			unset( $_POST['password'] );
			// end post password

			// edit meta user
			if ( count( $_POST['meta'] ) ) {
				foreach ( $_POST['meta'] as $key => $meta ) {
					update_user_meta( $user_id, $key, $meta );
				}
			}
			unset( $_POST['meta'] );

			$list_posts['display_name'] = $_POST['first_name'] . ' ' . $_POST['last_name'];

			foreach ( $_POST as $key => $post ) {
				$list_posts[ $key ] = esc_attr( $post );
			}
			$list_posts['ID'] = $user_id;
			wp_update_user( $list_posts );
		}
	}


	/**
	 * referrer_ajax_search
	 */
	public function referrer_ajax_search() {
		if ( isset( $_GET['q'] ) ) {

			$results = array();

			// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
			$args = array(
//			'blog_id'      => $GLOBALS['blog_id'],
				'search'         => $_GET['q'] . '*',
				'search_columns' => array( 'user_nicename', 'display_name' ),
				'role__in'       => RoleManager::getlist_member(),
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
				'credglv_login'        => [ self::getInstance(), 'credglv_login' ],
				'credglv_register'     => [ self::getInstance(), 'credglv_register' ],
				'referrer_ajax_search' => [ self::getInstance(), 'referrer_ajax_search' ],
			],
			'pages'   => [
				'front' => [
					credglv()->config->getUrlConfigs( 'credglv_login' )    =>
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