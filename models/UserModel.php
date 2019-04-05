<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\models;


use credglv\core\BaseObject;
use credglv\core\components\RoleManager;

class UserModel extends BaseObject {
	/**
	 * @return string Profile url
	 */
	public function getProfileUrl() {
		return site_url();
	}


	/**
	 * Add user column referrer
	 */
	function column_register_referrer( $columns ) {

		$columns['referrer_col'] = 'Referrer';

		return $columns;
	}

	function column_display_referrer( $value, $column_name, $user_id ) {

		$user_info = get_field( 'referrer', 'user_' . $user_id );

		if ( $column_name == 'referrer_col' ) {
			return $user_info['user_firstname'];
		}

		return $value;

	}

	/**
	 * add_registered_for_referrer
	 */
	public function add_registered_for_referrer( $user_id, $role, $old_roles ) {
		/*TODO::if user in role*/
		if ( $role == RoleManager::CREDGLV_MEMBER_1 ) {
			add_user_meta( $user_id, 'referrer_unikey', md5( $user_id . 'referrer_unikey' ), true );
		}
	}


	/**
	 * Get author link
	 * if this is member return to member profile
	 * either return student profile
	 *
	 * @param $link
	 * @param $authorId
	 *
	 * @return mixed
	 */
	public function authorLink( $link, $authorId ) {
		/** @var \WP_User $user */
		$user  = get_user_by( 'ID', $authorId );
		$roles = ! empty( $user->roles ) ? $user->roles : [];
		if ( in_array( 'credglv_member', $roles ) ) {
//			$member = new Instructor( $user );
//			$link       = $member->getProfileUrl();
		} else if ( in_array( 'credglv_student', $roles ) ) {
//			$student = new Student( $user );
//			$link    = $student->getProfileUrl();
		}

		return 'link author';
	}

	/**
	 * @param $redirect_to
	 * @param $request
	 * @param $user
	 *
	 * @return bool
	 */
	public function gotoProfile( $redirect_to, $user ) {
		if ( empty( $redirect_to ) || $redirect_to == '/' || get_admin_url() == $redirect_to || $redirect_to == site_url() ) {
			/** @var \WP_User $user */
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				if ( in_array( 'credglv_member', $user->roles ) ) {
//					$member = new Instructor( $user );
					return 'link profile';
//					return $member->getProfileUrl();
				} else if ( in_array( 'credglv_student', $user->roles ) ) {
//					$student = new Student( $user );
					return 'link profile';
//					return $student->getProfileUrl();
				}
			}
		}

		return $redirect_to;
	}

	/**
	 * @param $redirect_to
	 * @param $request
	 * @param $user
	 *
	 * @return bool
	 */
	public static function gotoProfileStatic( $redirect_to, \WP_User $user ) {
		if ( empty( $redirect_to ) || $redirect_to == '/' || get_admin_url() == $redirect_to || $redirect_to == site_url() ) {
			/** @var \WP_User $user */
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				if ( in_array( 'credglv_member', $user->roles ) ) {
//					$member = new Instructor( $user );
//
//					return $member->getProfileUrl();
				} else if ( in_array( 'credglv_student', $user->roles ) ) {
//					$student = new Student( $user );
//
//					return $student->getProfileUrl();
				}
			}
		}

		return $redirect_to;
	}

	public function redirectLoginUrl( $login_url, $redirect, $force_reauth ) {

		$login_url = site_url( credglv()->config->getUrlConfigs( 'credglv_login' ), 'login' );

		if ( ! empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
		}

		if ( $force_reauth ) {
			$login_url = add_query_arg( 'reauth', '1', $login_url );
		}

		return $login_url;
	}

	public function redirectLogout() {
		wp_redirect( home_url() );
		exit();
	}

	public function showAdminBar() {
		$user          = wp_get_current_user();
		$allowed_roles = array( 'credglv_member', 'credglv_student' );
		if ( empty( $user->ID ) || array_intersect( $allowed_roles, $user->roles ) ) {
			return false;
		}

		return true;
	}
}