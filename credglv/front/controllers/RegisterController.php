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
			if( WP_DEBUG === true ) {
				echo 'Debug is true';
				$array=[];
				$char='ABC';
				$num[]=array('parent'=>123,'child'=>4,'prev'=>'');
				$num[]=array('parent'=>1231,'child'=>4,'prev'=>$char.'123');
				$num[]=array('parent'=>12311,'child'=>4,'prev'=>$char.'1231');
				$num[]=array('parent'=>123111,'child'=>4,'prev'=>$char.'12311');
				$num[]=array('parent'=>1231111,'child'=>4,'prev'=>$char.'123111');
				$num[]=array('parent'=>12311111,'child'=>4,'prev'=>$char.'1231111');
				$num[]=array('parent'=>123111111,'child'=>4,'prev'=>$char.'12311111');
				$num[]=array('parent'=>1231111111,'child'=>4,'prev'=>$char.'123111111');
				$num[]=array('parent'=>123111112,'child'=>4,'prev'=>$char.'12311111');
				$num[]=array('parent'=>1231111121,'child'=>4,'prev'=>$char.'123111112');
				$num[]=array('parent'=>123111113,'child'=>4,'prev'=>$char.'12311111');
				$num[]=array('parent'=>1231111131,'child'=>4,'prev'=>$char.'123111113');
				$num[]=array('parent'=>12311112,'child'=>4,'prev'=>$char.'1231111');
				$num[]=array('parent'=>123111121,'child'=>4,'prev'=>$char.'12311112');
				$num[]=array('parent'=>123111122,'child'=>4,'prev'=>$char.'12311112');
				$num[]=array('parent'=>123111123,'child'=>4,'prev'=>$char.'12311112');
				$num[]=array('parent'=>12311113,'child'=>4,'prev'=>$char.'1231111');
				$num[]=array('parent'=>123111131,'child'=>4,'prev'=>$char.'12311113');
				$num[]=array('parent'=>123111132,'child'=>4,'prev'=>$char.'12311113');
				$num[]=array('parent'=>123111133,'child'=>4,'prev'=>$char.'12311113');
				$num[]=array('parent'=>1231112,'child'=>4,'prev'=>$char.'123111');
				$num[]=array('parent'=>1231113,'child'=>4,'prev'=>$char.'123111');
				$num[]=array('parent'=>123112,'child'=>4,'prev'=>$char.'12311');
				$num[]=array('parent'=>1231121,'child'=>4,'prev'=>$char.'123112');
				$num[]=array('parent'=>12311211,'child'=>4,'prev'=>$char.'1231121');
				$num[]=array('parent'=>12311212,'child'=>4,'prev'=>$char.'1231121');
				$num[]=array('parent'=>12311213,'child'=>4,'prev'=>$char.'1231121');
				$num[]=array('parent'=>1231122,'child'=>4,'prev'=>$char.'123112');
				$num[]=array('parent'=>12311221,'child'=>4,'prev'=>$char.'1231122');
				$num[]=array('parent'=>12311222,'child'=>4,'prev'=>$char.'1231122');
				$num[]=array('parent'=>12311223,'child'=>4,'prev'=>$char.'1231122');
				$num[]=array('parent'=>1231123,'child'=>4,'prev'=>$char.'123112');
				$num[]=array('parent'=>12311231,'child'=>4,'prev'=>$char.'1231123');
				$num[]=array('parent'=>12311232,'child'=>4,'prev'=>$char.'1231123');
				$num[]=array('parent'=>12311233,'child'=>4,'prev'=>$char.'1231123');
				$num[]=array('parent'=>123113,'child'=>4,'prev'=>$char.'12311');
				$num[]=array('parent'=>1231131,'child'=>4,'prev'=>$char.'123113');
				$num[]=array('parent'=>12311311,'child'=>4,'prev'=>$char.'1231131');
				$num[]=array('parent'=>12311312,'child'=>4,'prev'=>$char.'1231131');
				$num[]=array('parent'=>12311313,'child'=>4,'prev'=>$char.'1231131');
				$num[]=array('parent'=>1231132,'child'=>4,'prev'=>$char.'123113');
				$num[]=array('parent'=>12311321,'child'=>4,'prev'=>$char.'1231132');
				$num[]=array('parent'=>12311322,'child'=>4,'prev'=>$char.'1231132');
				$num[]=array('parent'=>12311323,'child'=>4,'prev'=>$char.'1231132');
				$num[]=array('parent'=>1231133,'child'=>4,'prev'=>$char.'123113');
				$num[]=array('parent'=>12311331,'child'=>4,'prev'=>$char.'1231133');
				$num[]=array('parent'=>12311332,'child'=>4,'prev'=>$char.'1231133');
				$num[]=array('parent'=>12311333,'child'=>4,'prev'=>$char.'1231133');
				$num[]=array('parent'=>12312,'child'=>4,'prev'=>$char.'1231');
				$num[]=array('parent'=>123121,'child'=>4,'prev'=>$char.'12312');
				$num[]=array('parent'=>1231211,'child'=>4,'prev'=>$char.'123121');
				$num[]=array('parent'=>12312111,'child'=>4,'prev'=>$char.'1231211');
				$num[]=array('parent'=>1231212,'child'=>4,'prev'=>$char.'123121');
				$num[]=array('parent'=>12312121,'child'=>4,'prev'=>$char.'1231212');
				$num[]=array('parent'=>1231213,'child'=>4,'prev'=>$char.'123121');
				$num[]=array('parent'=>12312131,'child'=>4,'prev'=>$char.'1231213');
				$num[]=array('parent'=>123122,'child'=>4,'prev'=>$char.'12312');
				$num[]=array('parent'=>1231221,'child'=>4,'prev'=>$char.'123122');
				$num[]=array('parent'=>12312211,'child'=>4,'prev'=>$char.'1231221');
				$num[]=array('parent'=>1231222,'child'=>4,'prev'=>$char.'123122');
				$num[]=array('parent'=>12312221,'child'=>4,'prev'=>$char.'1231222');
				$num[]=array('parent'=>1231223,'child'=>4,'prev'=>$char.'123122');
				$num[]=array('parent'=>12312231,'child'=>4,'prev'=>$char.'1231223');
				$num[]=array('parent'=>123123,'child'=>4,'prev'=>$char.'12312');
				$num[]=array('parent'=>1231231,'child'=>4,'prev'=>$char.'123123');
				$num[]=array('parent'=>12312311,'child'=>4,'prev'=>$char.'1231231');
				$num[]=array('parent'=>1231232,'child'=>4,'prev'=>$char.'123123');
				$num[]=array('parent'=>12312321,'child'=>4,'prev'=>$char.'1231232');
				$num[]=array('parent'=>1231233,'child'=>4,'prev'=>$char.'123123');
				$num[]=array('parent'=>12312331,'child'=>4,'prev'=>$char.'1231233');
				$num[]=array('parent'=>12313,'child'=>4,'prev'=>$char.'1231');
				$num[]=array('parent'=>123131,'child'=>4,'prev'=>$char.'12313');
				$num[]=array('parent'=>1231311,'child'=>4,'prev'=>$char.'123131');
				$num[]=array('parent'=>1231312,'child'=>4,'prev'=>$char.'123131');
				$num[]=array('parent'=>1231313,'child'=>4,'prev'=>$char.'123131');
				$num[]=array('parent'=>123132,'child'=>4,'prev'=>$char.'12313');
				$num[]=array('parent'=>1231321,'child'=>4,'prev'=>$char.'123132');
				$num[]=array('parent'=>1231322,'child'=>4,'prev'=>$char.'123132');
				$num[]=array('parent'=>1231323,'child'=>4,'prev'=>$char.'123132');
				$num[]=array('parent'=>123133,'child'=>4,'prev'=>$char.'12313');
				$num[]=array('parent'=>1231331,'child'=>4,'prev'=>$char.'123133');
				$num[]=array('parent'=>1231332,'child'=>4,'prev'=>$char.'123133');
				$num[]=array('parent'=>1231333,'child'=>4,'prev'=>$char.'123133');
				$num[]=array('parent'=>12314,'child'=>4,'prev'=>$char.'1231');
				$num[]=array('parent'=>123141,'child'=>4,'prev'=>$char.'12314');
				$num[]=array('parent'=>1231411,'child'=>4,'prev'=>$char.'123141');
				$num[]=array('parent'=>1231412,'child'=>4,'prev'=>$char.'123141');
				$num[]=array('parent'=>1231413,'child'=>4,'prev'=>$char.'123141');
				$num[]=array('parent'=>123142,'child'=>4,'prev'=>$char.'12314');
				$num[]=array('parent'=>1231421,'child'=>4,'prev'=>$char.'123142');
				$num[]=array('parent'=>1231422,'child'=>4,'prev'=>$char.'123142');
				$num[]=array('parent'=>1231423,'child'=>4,'prev'=>$char.'123142');
				$num[]=array('parent'=>123143,'child'=>4,'prev'=>$char.'12314');
				$num[]=array('parent'=>1231431,'child'=>4,'prev'=>$char.'123143');
				$num[]=array('parent'=>1231432,'child'=>4,'prev'=>$char.'123143');
				$num[]=array('parent'=>1231433,'child'=>4,'prev'=>$char.'123143');
				$num[]=array('parent'=>1232,'child'=>4,'prev'=>$char.'123');
				$num[]=array('parent'=>12321,'child'=>4,'prev'=>$char.'1232');
				$num[]=array('parent'=>123211,'child'=>4,'prev'=>$char.'12321');
				$num[]=array('parent'=>1232111,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>1232112,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>1232113,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>123212,'child'=>4,'prev'=>$char.'12321');
				$num[]=array('parent'=>1232121,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>1232122,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>1232123,'child'=>4,'prev'=>$char.'123211');
				$num[]=array('parent'=>123213,'child'=>4,'prev'=>$char.'12321');
				$num[]=array('parent'=>1232131,'child'=>4,'prev'=>$char.'123213');
				$num[]=array('parent'=>1232132,'child'=>4,'prev'=>$char.'123213');
				$num[]=array('parent'=>1232133,'child'=>4,'prev'=>$char.'123213');
				$num[]=array('parent'=>12322,'child'=>4,'prev'=>$char.'1232');
				$num[]=array('parent'=>123221,'child'=>4,'prev'=>$char.'12322');
				$num[]=array('parent'=>1232211,'child'=>4,'prev'=>$char.'123221');
				$num[]=array('parent'=>1232212,'child'=>4,'prev'=>$char.'123221');
				$num[]=array('parent'=>1232213,'child'=>4,'prev'=>$char.'123221');
				$num[]=array('parent'=>123222,'child'=>4,'prev'=>$char.'12322');
				$num[]=array('parent'=>1232221,'child'=>4,'prev'=>$char.'123222');
				$num[]=array('parent'=>1232222,'child'=>4,'prev'=>$char.'123222');
				$num[]=array('parent'=>1232223,'child'=>4,'prev'=>$char.'123222');
				$num[]=array('parent'=>123223,'child'=>4,'prev'=>$char.'12322');
				$num[]=array('parent'=>1232231,'child'=>4,'prev'=>$char.'123223');
				$num[]=array('parent'=>1232232,'child'=>4,'prev'=>$char.'123223');
				$num[]=array('parent'=>1232233,'child'=>4,'prev'=>$char.'123223');
				$num[]=array('parent'=>12323,'child'=>4,'prev'=>$char.'1232');
				$num[]=array('parent'=>123231,'child'=>4,'prev'=>$char.'12323');
				$num[]=array('parent'=>1232311,'child'=>4,'prev'=>$char.'123231');
				$num[]=array('parent'=>1232312,'child'=>4,'prev'=>$char.'123231');
				$num[]=array('parent'=>1232313,'child'=>4,'prev'=>$char.'123231');
				$num[]=array('parent'=>123232,'child'=>4,'prev'=>$char.'12323');
				$num[]=array('parent'=>1232321,'child'=>4,'prev'=>$char.'123232');
				$num[]=array('parent'=>1232322,'child'=>4,'prev'=>$char.'123232');
				$num[]=array('parent'=>1232323,'child'=>4,'prev'=>$char.'123232');
				$num[]=array('parent'=>123233,'child'=>4,'prev'=>$char.'12323');
				$num[]=array('parent'=>1232331,'child'=>4,'prev'=>$char.'123233');
				$num[]=array('parent'=>1232332,'child'=>4,'prev'=>$char.'123233');
				$num[]=array('parent'=>1232333,'child'=>4,'prev'=>$char.'123233');
				$num[]=array('parent'=>12324,'child'=>4,'prev'=>$char.'1232');
				$num[]=array('parent'=>123241,'child'=>4,'prev'=>$char.'12324');
				$num[]=array('parent'=>1232411,'child'=>4,'prev'=>$char.'123241');
				$num[]=array('parent'=>1232412,'child'=>4,'prev'=>$char.'123241');
				$num[]=array('parent'=>1232413,'child'=>4,'prev'=>$char.'123241');
				$num[]=array('parent'=>123242,'child'=>4,'prev'=>$char.'12324');
				$num[]=array('parent'=>1232421,'child'=>4,'prev'=>$char.'123242');
				$num[]=array('parent'=>1232422,'child'=>4,'prev'=>$char.'123242');
				$num[]=array('parent'=>1232423,'child'=>4,'prev'=>$char.'123242');
				$num[]=array('parent'=>123243,'child'=>4,'prev'=>$char.'12324');
				$num[]=array('parent'=>1232431,'child'=>4,'prev'=>$char.'123243');
				$num[]=array('parent'=>1232432,'child'=>4,'prev'=>$char.'123243');
				$num[]=array('parent'=>1232433,'child'=>4,'prev'=>$char.'123243');
				$num[]=array('parent'=>1233,'child'=>4,'prev'=>$char.'123');
				$num[]=array('parent'=>12331,'child'=>4,'prev'=>$char.'1233');
				$num[]=array('parent'=>12332,'child'=>4,'prev'=>$char.'1233');
				$num[]=array('parent'=>1234,'child'=>4,'prev'=>$char.'123');
				$num[]=array('parent'=>12341,'child'=>4,'prev'=>$char.'1234');
				$num[]=array('parent'=>12342,'child'=>4,'prev'=>$char.'1234');
				foreach($num as $val){
					$array[]=array('user_login'=>'ABC'.$val['parent'],
					               'user_email'=>'ABC'.$val['parent'].'@gmail.com',
					               'user_pass'=>'test',
					               'phone'=>$val['parent'],
					               'parent'=>$val['prev']);
				}
				echo '<pre>';
				print_r($array);
				echo '</pre>';

				foreach($array as $val){
					echo '<pre>';
					print_r($val);
					echo '</pre>';
					$userdata      = array(
						'ID'         => 0,    //(int) User ID. If supplied, the user will be updated.
						'user_pass'  => $val['user_pass'],   //(string) The plain-text user password.
						'user_login' => $val['user_login'],   //(string) The user's login username.
						'user_email' => $val['user_email'],   //(string) The user email address.
						'show_admin_bar_front' => false,   //(string) The user email address.
					);
					$userId        = wp_insert_user( $userdata );
					update_user_meta( $userId, UserController::METAKEY_PHONE, $val['phone'] );
					wp_update_user( array( 'ID' => $userId, 'user_email' => $val['user_email'] ) );
					$parent=get_userdatabylogin($val['parent']);
					echo '<pre>';
					print_r($parent->ID);
					echo '</pre>';
					$user                  = new UserModel();
					$user->user_id         = $userId;
					$user->referral_parent = $parent->ID;
					$user->referral_code   = $user->get_referralcode();
					$user->save();
				}
				echo '<pre>';
				print_r($user);
				echo '</pre>';
				// enabled
			} else {
				// not enabled
			}
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