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

	function save_extra_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		if ( isset( $_POST['phone'] ) ) {
			update_user_meta( $user_id, 'phone', $_POST['phone'] );
		}
	}


	/**
	 * @param $redirect_to
	 * @param $request
	 * @param $user
	 *
	 * @return bool
	 */
	public function custom_user_profile_fields( $profileuser ) {

		$user_referrer      = get_the_author_meta( 'referrer', $profileuser->ID );
		$user_referrer      = get_userdata( $user_referrer );
		$user_referrer      = ! empty( $user_referrer ) ? $user_referrer->user_nicename : __( 'You\'re the top', 'credglv' );
		$user_referrer_link = get_user_meta( $profileuser->ID, 'referrer_unikey', true );
		$user_phone         = get_user_meta( $profileuser->ID, 'phone', true );
		$user_phone         = ! empty( $user_phone ) ? $user_phone : '';
		$user_referrer_link = home_url( '?ref=' . $user_referrer_link );
		?>

        <table class="form-table">
            <tr>
                <th>
                    <label for="phone"><?php esc_html_e( 'Phone', 'credglv' ); ?></label>
                </th>
                <td>
                    <input type="number" name="phone" id="phone"
                           value="<?php echo esc_attr( $user_phone ); ?>"
                           class="regular-text"/>
                    <br><span class="description"><?php esc_html_e( 'Your phone', 'credglv' ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="referrer"><?php esc_html_e( 'Your referrer', 'credglv' ); ?></label>
                </th>
                <td>
                    <input type="text" name="referrer" id="referrer" disabled
                           value="<?php echo esc_attr( $user_referrer ); ?>"
                           class="regular-text"/>
                    <br><span class="description"><?php esc_html_e( 'Your referrer', 'credglv' ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="user_location"><?php esc_html_e( 'Your invite link', 'credglv' ); ?></label>
                </th>
                <td>
                    <input type="text" name="referrer_link" id="referrer_link"
                           onClick="this.setSelectionRange(0, this.value.length)"
                           value="<?php echo esc_attr( $user_referrer_link ); ?>"
                           class="regular-text"/>
                    <br><span class="description"><?php esc_html_e( 'Link', 'credglv' ); ?></span>
                </td>
            </tr>
        </table>

		<?php
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {

		return [
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