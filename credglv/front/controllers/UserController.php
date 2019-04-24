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
	 * Edit user info
	 */
	public function editProfilePage() {
		return $this->checkLogin( 'edit-profile' );
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

	public function listInfo() {
		return [
			'first_name' => 'First Name',
			'last_name'  => 'Last Name',
		];
	}

	public function showList() {
		return [
			[
				'title' => 'Enrolled',
				'name'  => 'enrolled',
			],
			[
				'title' => 'Bookmarked',
				'name'  => 'bookmarked',
			],
		];
	}


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

        <p class="form-row form-row-wide">
            <label for="reg_billing_phone">
				<?php _e( 'Mobile Number', 'woocommerce' ); ?> <span class="required">*</span>
            </label>

        <div class="login_countrycode">
            <div class="list_countrycode <?php echo empty( $num_contrycode ) ? 'hide' : '' ?>">
                <input type="text" class="woocommerce-phone-countrycode" placeholder="+84"
                       value="<?php echo ! empty( $num_contrycode ) ? $num_contrycode : '+84' ?>"
                       name="number_countrycode" size="4">
                <ul class="digit_cs-list">
                    <li class="dig-cc-visible" data-value="+60" data-country="malaysia">(+60) Malaysia</li>
                    <li class="dig-cc-visible" data-value="+84" data-country="vietnam">(+84) Vietnam</li>
                </ul>
            </div>
            <input type="text" class="input-number-mobile <?php echo ! empty( $num_contrycode ) ? 'width80' : '' ?>"
                   name="cred_billing_phone"
                   id="reg_billing_phone"
                   value="<?php echo $num_val; ?>" maxlength="10"/>

        </div>

        </p>


		<?php
	}

	function credglv_edit_save_fields( $args ) {
		global $wpdb;
		$user_id = get_current_user_ID();


		if ( isset( $_POST['cred_billing_phone'] ) && $_POST['cred_billing_phone'] == '' ) {
			$args->add( 'billing_phone_name_error', __( 'Mobile number is required.', 'credglv' ) );
		}
		if ( isset( $_POST['cred_billing_phone'] ) && ! empty( $_POST['cred_billing_phone'] ) ) {
			$mobile_num_result = $wpdb->get_var( "select B.ID from " . $wpdb->prefix . "usermeta as A join " . $wpdb->prefix . "users as B where meta_key='cred_billing_phone' and meta_value='" . $_POST['cred_billing_phone'] . "' and A.user_id =  b.ID " );
			if ( isset( $mobile_num_result ) ) {
				if ( $user_id != $mobile_num_result ) {
					wc_add_notice( __( 'Mobile Number is already used.', 'credglv' ), 'error' );

					return;
				} else {
					update_user_meta( $user_id, 'cred_billing_phone', $_POST['cred_billing_phone'] );

					return $_POST['cred_billing_phone'];
				}
			} else {
				update_user_meta( $user_id, 'cred_billing_phone', $_POST['cred_billing_phone'] );

				return $_POST['cred_billing_phone'];
			}
		}

	}

//add_action( 'woocommerce_save_account_details_errors', array( $this, 'credglv_edit_save_fields' ), 10, 1 );


	function credglv_validate_extra_register_fields_update( $customer_id ) {
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
			}
		}

		return $validation_errors;
	}


	function credglv_assets_enqueue() {


		global $post;
		if ( $post->ID == get_option( 'woocommerce_myaccount_page_id' ) ) {
			wp_register_script( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/js/login-register.js' );
			wp_enqueue_script( 'cred-my-account-login-page' );
			wp_enqueue_style( 'cred-my-account-login-page', plugin_dir_url( __DIR__ ) . '/assets/css/cred-reg-log.css' );
		}
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */

	public static function registerAction() {
		return [
			'actions' => [
				'woocommerce_register_form_start'         => [ self::getInstance(), 'credglv_extra_register_fields' ],
				'woocommerce_save_account_details_errors' => [ self::getInstance(), 'credglv_edit_save_fields' ],
				'wp_enqueue_scripts'                      => [ self::getInstance(), 'credglv_assets_enqueue' ],
				'woocommerce_register_post'               => [
					self::getInstance(),
					'credglv_validate_extra_register_fields',
					10,
					3,
				],
				'woocommerce_created_customer'            => [
					self::getInstance(),
					'credglv_validate_extra_register_fields_update'
				],
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