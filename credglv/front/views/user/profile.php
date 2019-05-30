<?php
$flag = 0;
function render_profile_html() {
	function get_img_ava() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'avatar' ) ) == 0 ) {
			$get_img_ava = [ '' ];
		} else {
			$get_img_ava = get_user_meta( $user_id, 'avatar' );
		}

		return $get_img_ava[0];
	}
	function get_img_pp() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'passports' ) ) == 0 ) {
			$get_img_pp = [ '' ];
		} else {
			$get_img_pp = get_user_meta( $user_id, 'passports' );
		}

		return $get_img_pp[0];
	}

	function get_img_iden() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'iden' ) ) == 0 ) {
			$get_img_iden = [ '' ];
		} else {
			$get_img_iden = get_user_meta( $user_id, 'iden' );
		}

		return $get_img_iden[0];
	}

	function get_gender() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_gender' ) ) == 0 ) {
			$get_gender = [ '' ];
		} else {
			$get_gender = get_user_meta( $user_id, 'user_gender' );
		}

		return $get_gender[0];
	}

	function get_address() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_address' ) ) == 0 ) {
			$get_address = [ '' ];
		} else {
			$get_address = get_user_meta( $user_id, 'user_address' );
		}

		return $get_address[0];
	}

	function get_fullname() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_fullname' ) ) == 0 ) {
			$get_fullname = [ '' ];
		} else {
			$get_fullname = get_user_meta( $user_id, 'user_fullname' );
		}

		return $get_fullname[0];
	}

	function get_date() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_date' ) ) == 0 ) {
			$get_date = [ '' ];
		} else {
			$get_date = get_user_meta( $user_id, 'user_date' );
		}

		return $get_date[0];
	}

	function get_country() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_country' ) ) == 0 ) {
			$get_country = [ '' ];
		} else {
			$get_country = get_user_meta( $user_id, 'user_country' );
		}

		return $get_country[0];
	}

	$get_img_ava  = get_img_ava();
	$get_img_pp   = get_img_pp();
	$get_img_iden = get_img_iden();
	$get_gender   = get_gender();
	$get_address  = get_address();
	$get_fullname = get_fullname();
	$get_date     = get_date();
	$get_country  = get_country();

	$html = '';
	$html .= '<form class="profile-update" method="post" enctype="multipart/form-data">';
	$html .= '<input type="file" name="user_avatar" class="hide">';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_avatar">Avatar';
	$html .= '<p><img style="width:125px;height:auto" src="' . $get_img_ava . '" alt="" class="update_img_ava"></p>';
	$html .= '<input type="file" id="user_avatar" name="user_avatar" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_gender">Genders';
	$html .= '<select id="user_gender" name="user_gender">';
	$args = [ 'Mr.', 'Mrs.', 'Ms.' ];
	for ( $i = 0; $i < count( $args ); $i ++ ) {
		if ( $args[ $i ] == $get_gender ) {
			$html .= '<option selected="selected">' . $args[ $i ] . '</option>';
		} else {
			$html .= '<option>' . $args[ $i ] . '</option>';
		}

	}
	$html .= '</select>';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_fullname">Full Name';
	$html .= '<input type="text" id="user_fullnames" name="user_fullname" value="' . $get_fullname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_date">Date of Birth';
	$html .= '<input type="date" min="1900-01-01" max="2012-12-30" id="user_date" name="user_date" value="' . $get_date . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_fullname">Address';
	$html .= '<input type="text" id="user_address" name="user_address" value="' . $get_address . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_country">Country';
	$html .= '<select id="user_country" name="user_country">';
	$arg  = [
		'Vietnam',
		'Cambodia',
		'Indonesia',
		'Indonesia',
		'Myanmar',
		'Philippines',
		'Singapore',
		'Malaysia',
		'Thailand'
	];
	for ( $i = 0; $i < count( $arg ); $i ++ ) {
		if ( $arg[ $i ] == $get_country ) {
			$html .= '<option selected="selected">' . $arg[ $i ] . '</option>';
		} else {
			$html .= '<option>' . $arg[ $i ] . '</option>';
		}

	}
	$html .= '</select>';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_indentification">Identification Card';
	$html .= '<p><img style="width:125px;height:125px" src="' . $get_img_iden . '" alt="" class="update_img_iden"></p>';
	$html .= '<input type="file" id="user_indentification" name="user_indentification" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_passports">Passport';
	$html .= '<p><img style="width:125px;height:125px" src="' . $get_img_pp . '" alt="" class="update_img_pp"></p>';
	$html .= '<input type="file" id="user_passports" name="user_passports" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<button class="btn btn-default ld-ext-right" type="submit" name="uploadclick" >'.__('Update Profile','credglv').'<div class="ld ld-spinner ld-spin"></div></button>';
	$html .= '</form>';
	echo $html;
}

function render_profile_error_html() {
	global $flag;
	$user_gender   = $_POST['user_gender'];
	$user_date     = $_POST['user_date'];
	$user_address  = $_POST['user_address'];
	$user_country  = $_POST['user_country'];
	$user_fullname = $_POST['user_fullname'];
	function get_img_ava() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'avatar' ) ) == 0 ) {
			$get_img_ava = [ '' ];
		} else {
			$get_img_ava = get_user_meta( $user_id, 'avatar' );
		}

		return $get_img_ava[0];
	}
	function get_img_pp() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'passports' ) ) == 0 ) {
			$get_img_pp = [ '' ];
		} else {
			$get_img_pp = get_user_meta( $user_id, 'passports' );
		}

		return $get_img_pp[0];
	}

	function get_img_iden() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'iden' ) ) == 0 ) {
			$get_img_iden = [ '' ];
		} else {
			$get_img_iden = get_user_meta( $user_id, 'iden' );
		}

		return $get_img_iden[0];
	}

	function get_fullname() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_fullname' ) ) == 0 ) {
			$get_fullname = [ '' ];
		} else {
			$get_fullname = get_user_meta( $user_id, 'user_fullname' );
		}

		return $get_fullname[0];
	}

	function get_address() {
		$user_id = get_current_user_id();
		if ( sizeof( get_user_meta( $user_id, 'user_address' ) ) == 0 ) {
			$get_address = [ '' ];
		} else {
			$get_address = get_user_meta( $user_id, 'user_address' );
		}

		return $get_address[0];
	}

	$get_img_ava  = get_img_ava();
	$get_img_pp   = get_img_pp();
	$get_img_iden = get_img_iden();
	$get_fullname = get_fullname();
	$get_address  = get_address();


	$html = '';

	$html .= '<form class="profile-update"  method="post" enctype="multipart/form-data">';
	$html .= '<input type="file" name="user_avatar" class="hide">';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_avatar">Avatar';
	$html .= '<p><img style="width:125px;height:auto" src="' . $get_img_ava . '" alt="" class="update_img_ava"></p>';
	$html .= '<input type="file" id="user_avatar" name="user_avatar" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_gender">Genders';
	$html .= '<select id="user_gender" name="user_gender">';
	$args = [ 'Mr.', 'Mrs.', 'Ms.' ];
	for ( $i = 0; $i < count( $args ); $i ++ ) {
		if ( $args[ $i ] == $user_gender ) {
			$html .= '<option selected="selected">' . $args[ $i ] . '</option>';
		} else {
			$html .= '<option>' . $args[ $i ] . '</option>';
		}

	}
	$html .= '</select>';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_fullname">Full Name';
	if ( $flag == 1 ) {
		$html .= '<input type="text" id="user_fullnames" name="user_fullname" value="' . $get_fullname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	} else {
		$html .= '<input type="text" id="user_fullnames" name="user_fullname" value="' . $user_fullname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	}
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_date">Date of Birth';
	$html .= '<input type="date" min="2017-01-01" max="2017-12-30" id="user_date" name="user_date" value="' . $user_date . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_fullname">Address';
	if ( $flag == 3 ) {
		$html .= '<input type="text" id="user_address" name="user_address" value="' . $get_address . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	} else {
		$html .= '<input type="text" id="user_address" name="user_address" value="' . $user_address . '" class="woocommerce-Input woocommerce-Input--password input-text">';
	}
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_country">Country';
	$html .= '<select id="user_country" name="user_country">';
	$arg  = [
		'Vietnam',
		'Cambodia',
		'Indonesia',
		'Indonesia',
		'Myanmar',
		'Philippines',
		'Singapore',
		'Malaysia',
		'Thailand'
	];
	for ( $i = 0; $i < count( $arg ); $i ++ ) {
		if ( $arg[ $i ] == $user_country ) {
			$html .= '<option selected="selected">' . $arg[ $i ] . '</option>';
		} else {
			$html .= '<option>' . $arg[ $i ] . '</option>';
		}

	}
	$html .= '</select>';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_indentification">Identification Card';
	$html .= '<p><img style="width:125px;height:125px" src="' . $get_img_iden . '" alt="" class="update_img_iden"></p>';
	$html .= '<input type="file" id="user_indentification" name="user_indentification" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
	$html .= '<label for="user_passports">Passport';
	$html .= '<p><img style="width:125px;height:125px" src="' . $get_img_pp . '" alt="" class="update_img_pp"></p>';
	$html .= '<input type="file" id="user_passports" name="user_passports" class="woocommerce-Input woocommerce-Input--password input-text">';
	$html .= '</p>';
	$html .= '<button class="btn btn-default ld-ext-right" type="submit" name="uploadclick" >'.__('Update Profile','credglv').'<div class="ld ld-spinner ld-spin"></div></button>';
	$html .= '</form>';
	echo $html;
}

$upload     = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_dir = $upload_dir . '/credglv/img';
if ( ! is_dir( $upload_dir ) ) {
	wp_mkdir_p( $upload_dir );
}
if ( isset( $_POST['uploadclick'] ) ) {
	$user_id = get_current_user_id();
	if ( isset( $_FILES['user_passports'] ) ) {
		if ( $_FILES['user_passports']['error'] <= 0 ) {
			$type_pp = $_FILES['user_passports']['type'];
			$explode = explode( '/', $type_pp );
			if ( $explode[0] == 'image' ) {
				$from_pp  = $_FILES['user_passports']['tmp_name'];
				$to       = wp_upload_dir()['basedir'];
				$timezone = + 7;
				$time     = gmdate( "Y-m-j-H-i-s", time() + 3600 * ( $timezone + date( "I" ) ) );
				$name_pp  = $user_id . '_pp_' . $time . '_' . $_FILES['user_passports']['name'];
				$url      = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_pp;
				$src      = $to . '/credglv/img/' . $name_pp;
				if ( ! is_file( $src ) ) {
					move_uploaded_file( $from_pp, $src );
					update_user_meta( $user_id, 'passports', $url );
				}
			}
		}
	}
	if ( isset( $_FILES['user_indentification'] ) ) {
		if ( $_FILES['user_indentification']['error'] <= 0 ) {
			$type_iden = $_FILES['user_indentification']['type'];
			$explode   = explode( '/', $type_iden );
			if ( $explode[0] == 'image' ) {
				$from_iden = $_FILES['user_indentification']['tmp_name'];
				$to        = wp_upload_dir()['basedir'];
				$timezone  = + 7;
				$time      = gmdate( "Y-m-j-H-i-s", time() + 3600 * ( $timezone + date( "I" ) ) );
				$name_iden = $user_id . '_iden_' . $time . '_' . $_FILES['user_indentification']['name'];
				$url       = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_iden;
				$src       = $to . '/credglv/img/' . $name_iden;
				if ( ! is_file( $src ) ) {
					move_uploaded_file( $from_iden, $src );
					update_user_meta( $user_id, 'iden', $url );
				}
			}
		}
	}
	if ( isset( $_FILES['user_avatar'] ) ) {
		if ( $_FILES['user_avatar']['error'] <= 0 ) {
			$type_avatar = $_FILES['user_avatar']['type'];
			$explode   = explode( '/', $type_avatar );
			if ( $explode[0] == 'image' ) {
				$from_avatar = $_FILES['user_avatar']['tmp_name'];
				$to        = wp_upload_dir()['basedir'];
				$timezone  = + 7;
				$time      = gmdate( "Y-m-j-H-i-s", time() + 3600 * ( $timezone + date( "I" ) ) );
				$name_avatar = $user_id . '_ava_' . $time . '_' . $_FILES['user_avatar']['name'];
				$url       = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_avatar;
				$src       = $to . '/credglv/img/' . $name_avatar;
				if ( ! is_file( $src ) ) {
					move_uploaded_file( $from_avatar, $src );
					update_user_meta( $user_id, 'avatar', $url );
				}
			}
		}
	}
	$user_gender   = $_POST['user_gender'];
	$user_fullname = $_POST['user_fullname'];
	$user_date     = $_POST['user_date'];
	$user_address  = $_POST['user_address'];
	$user_country  = $_POST['user_country'];
	if ( empty( $user_fullname )  || empty( $user_address ) ) {
		if ( empty( $user_fullname ) ) {
			echo '<div style="background-color: red;text-align: center; color: white">Invalid Full Name</div>';
			global $flag;
			$flag = 1;
		} else if ( empty( $user_address ) ) {
			echo '<div style="background-color: red;text-align: center; color: white">Invalid Address</div>';
			global $flag;
			$flag = 3;
		}
	} else if ( ! empty( $user_fullname )  && ! empty( $user_address ) ) {
		update_user_meta( $user_id, 'user_fullname', $user_fullname );
		update_user_meta( $user_id, 'user_address', $user_address );
		update_user_meta( $user_id, 'user_gender', $user_gender );
		update_user_meta( $user_id, 'user_date', $user_date );
		update_user_meta( $user_id, 'user_country', $user_country );
		$layout = '';
		$layout .= '<div style="background-color: green;text-align: center;color: white">Update Success</div>';
		echo $layout;
		global $flag;
		$flag = 0;
	}

}
global $flag;
if ( $flag == 0 ) {
	render_profile_html();
} else {
	render_profile_error_html();
}


