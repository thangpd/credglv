<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\admin\controllers;


use credglv\core\interfaces\AdminControllerInterface;
use credglv\models\Instructor;
use credglv\models\UserModel;

class UserController extends AdminController implements AdminControllerInterface {
	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function userColumns( $columns ) {
		$columns['referrer_col'] = 'Referrer';

		return $columns;
	}

	/**
	 * @param $val
	 * @param $column_name
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function userColumnValue( $val, $column_name, $user_id ) {
		$user_referrer = get_the_author_meta( 'referrer', $user_id );
		$user_referrer = get_userdata( $user_referrer );

		if ( $column_name == 'referrer_col' && ! empty( $user_referrer ) ) {
			return $user_referrer->user_nicename;
		}

		return $val;
	}


	public function ajax_active_user() {
		if ( isset( $_POST['user_id'] ) ) {
			$user_id = $_POST['user_id'];

			$user = UserModel::getInstance();

			$user->update_active_status( $user_id, $_POST['active'] );
			$settings = mycred_part_woo_settings();
			$mycred   = mycred( $settings['point_type'] );

			// Excluded from usage
			if ( $mycred->exclude_user( $user_id ) ) {
				$this->responseJson( array( 'code' => 403, 'User excluded' ) );
			}
			if ( $user->check_actived_referral( $user_id, 0 ) && ! $mycred->has_entry( 'register_fee', 1, $user_id ) ) {
				$mycred->add_creds( 'register_fee',
					$user_id,
					0,
					__( 'Joining fee active by admin', 'credglv' ),
					1,
					'',
					$settings['point_type'] );
				$this->responseJson( array( 'code' => 200, 'Updated user' ) );

			}

		} else {
			$this->responseJson( array( 'code' => 404, 'message' => 'No user_id' ) );
		}
	}

	function extra_user_profile_fields( $user ) { ?>
        <h3><?php _e( "CREDGLV INFORMATION", "blank" ); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="phone"><?php _e( "PHONE" ); ?></label></th>
                <td>
                    <input type="text" name="<?php echo \credglv\front\controllers\UserController::METAKEY_PHONE ?>"
                           id="<?php echo \credglv\front\controllers\UserController::METAKEY_PHONE ?>"
                           value="<?php echo esc_attr( get_user_meta( $user->data->ID, \credglv\front\controllers\UserController::METAKEY_PHONE, true ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php _e( "Please enter your phone here." ); ?></span>
                </td>
            </tr>
        </table>
	<?php }

	function save_extra_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, \credglv\front\controllers\UserController::METAKEY_PHONE, $_POST[ \credglv\front\controllers\UserController::METAKEY_PHONE ] );
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {

		//show
		add_action( 'show_user_profile', [ self::getInstance(), 'extra_user_profile_fields' ] );
		add_action( 'edit_user_profile', [ self::getInstance(), 'extra_user_profile_fields' ] );


		//save meta field
		add_action( 'personal_options_update', [ self::getInstance(), 'save_extra_user_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ self::getInstance(), 'save_extra_user_profile_fields' ] );


		return [
			'ajax'    => [
				'ajax_active_user' => [ self::getInstance(), 'ajax_active_user' ],
			],
			'actions' => [
//				'manage_users_columns'       => [ self::getInstance(), 'userColumns' ],
//				'manage_users_custom_column' => [ self::getInstance(), 'userColumnValue', 15, 3 ],
//				'personal_options_update'    => [ self::getInstance(), 'save_extra_user_profile_fields', 10, 1 ],
//				'edit_user_profile_update'   => [ self::getInstance(), 'save_extra_user_profile_fields', 10, 1 ],
//				'show_user_profile'          => [ self::getInstance(), 'custom_user_profile_fields', 10, 1 ],
//				'edit_user_profile'          => [ self::getInstance(), 'custom_user_profile_fields', 10, 1 ],
			]
		];
	}
}