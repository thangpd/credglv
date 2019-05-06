<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 * Global functions in LearnMaster plugin
 */


/**
 * Get instance of LearnMaster App plugin
 * @return \credglv\core\App
 */
function credglv() {
	return \credglv\core\App::getInstance();
}


/**
 * @param $str
 */
function credglv_do_shortcode( $str ) {
	if ( credglv_is_ready() ) {
		return credglv()->shortcodeManager->doShortcode( $str );
	}

	return false;

}


/**
 * Minify Css files
 *
 * @param $files
 * @param $name
 *
 * @return bool|string
 */
function credglv_minify_css( $files, $name ) {
	if ( credglv_is_ready() ) {
		return credglv()->resourceManager->releaseStyle( $files, $name );
	}

	return false;

}

/**
 * Minify script files
 *
 * @param $files
 * @param $name
 *
 * @return bool|string
 */
function credglv_minify_js( $files, $name ) {
	if ( credglv_is_ready() ) {
		return credglv()->resourceManager->releaseScript( $files, $name );
	}

	return false;

}

/**
 * @param $html
 *
 * @return mixed
 */
function credglv_minify_html( $html ) {
	if ( credglv_is_ready() ) {
		return credglv()->helpers->general->minifyHtml( $html );
	}

	return false;

}

add_filter( 'show_admin_bar', '__return_false' );

/**
 * @return bool
 */
function credglv_is_ready() {
	return credglv()->isReady();
}


add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'credglv', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'languages' );
}, 9 );


/**
 * get_woo_myaccount page
 */
function credglv_get_woo_myaccount() {
	if ( class_exists( 'WooCommerce' ) && get_option( 'woocommerce_myaccount_page_id' ) ) {
		return get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
	} else {
		return false;
	}
}


if ( ! function_exists( 'credglv_woocommerce_locate_template' ) ) {
	function credglv_woocommerce_locate_template( $template, $template_name, $template_path ) {
		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = plugin_dir_path( __FILE__ ) . '/woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(

			array(
				$template_path . $template_name,
				$template_name
			)
		);
		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		// Use default template
		if ( ! $template ) {
			$template = $_template;
		}

		// Return what we found
		return $template;
	}

	add_filter( 'woocommerce_locate_template', 'credglv_woocommerce_locate_template', 1, 3 );
}
function admin_post_profile(){
    $user_id = get_current_user_id();
    if ($_FILES['user_passports']['error'] <= 0) {
        $type_pp = $_FILES['user_passports']['type'];
        $explode = explode('/', $type_pp);
        if ($explode[0] == 'image') {
            $from_pp = $_FILES['user_passports']['tmp_name'];
            $to = wp_upload_dir()['basedir'];
            $timezone  = +7;
            $time = gmdate("Y-m-j-H-i-s", time() + 3600*($timezone+date("I")));
            $name_pp = $user_id . '_pp_' .$time.'_'. $_FILES['user_passports']['name'];
            $url = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_pp;
            $src = $to . '/credglv/img/' . $name_pp;
            if (!is_file($src)) {
                move_uploaded_file($from_pp, $src);
                update_user_meta($user_id, 'passports', $url);
            }
        }
    }
    if ($_FILES['user_indentification']['error'] <= 0) {
        $type_iden = $_FILES['user_indentification']['type'];
        $explode = explode('/', $type_iden);
        if ($explode[0] == 'image') {
            $from_iden = $_FILES['user_indentification']['tmp_name'];
            $to = wp_upload_dir()['basedir'];
            $timezone  = +7;
            $time = gmdate("Y-m-j-H-i-s", time() + 3600*($timezone+date("I")));
            $name_iden = $user_id . '_iden_' .$time.'_'. $_FILES['user_indentification']['name'];
            $url = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_iden;
            $src = $to . '/credglv/img/' . $name_iden;
            if (!is_file($src)) {
                move_uploaded_file($from_iden, $src);
                update_user_meta($user_id, 'iden', $url);
            }
        }
    }
    $user_gender = $_POST['user_gender'];
    $user_fullname = $_POST['user_fullname'];
    $user_date = $_POST['user_date'];
    $user_phone = $_POST['user_phone'];
    $user_address = $_POST['user_address'];
    $user_country = $_POST['user_country'];
    if(empty($user_fullname)|| empty($user_phone) || !is_numeric($user_phone) ||empty($user_address)) {
        try {
            if (empty($user_fullname)) {
                throw new Exception('Invalid Full Name');
            }
            if (empty($user_phone) || !is_numeric($user_phone)) {
                throw new Exception('Invalid Phone Number');
            }
            if (empty($user_address)) {
                throw new Exception('Invalid Address');
            }
        } catch (Exception $e) {
            $layout = '';
            $layout .= '<div style="background-color: red;text-align: center">' . $e->getMessage() . '</div>';
            echo $layout;
        }
    }else if(!empty($user_fullname) && !empty($user_phone) && is_numeric($user_phone) && !empty($user_address)){
        update_user_meta($user_id, 'user_fullname', $user_fullname);
        update_user_meta($user_id, 'user_phone', $user_phone);
        update_user_meta($user_id, 'user_address', $user_address);
        update_user_meta($user_id, 'user_gender', $user_gender);
        update_user_meta($user_id, 'user_date', $user_date);
        update_user_meta($user_id, 'user_country', $user_country);
        $layout = '';
        $layout .= '<div style="background-color: green;text-align: center;color: white">Update Success</div>';
        echo $layout;
    }
    wp_redirect(get_site_url().'/my-account');

}
add_action( 'admin_post_Update-Profile', 'admin_post_profile' );
add_action( 'admin_post_nopriv_Update-Profile', 'admin_post_profile' );