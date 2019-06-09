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

/**
 * Add Custom References
 * @mycred
 * @version 1.0
 */
add_filter( 'mycred_all_references', 'mycredpro_add_custom_references' );
function mycredpro_add_custom_references( $list ) {

	$list['register_fee'] = 'Register Fee';

	return $list;

}

add_filter( 'get_avatar' , 'my_custom_avatar' , 1 , 5 );
function my_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );	
    }

    if ( $user && is_object( $user ) ) {
    	$custom_avatar = get_user_meta($user->data->ID,'avatar',true) ? get_user_meta($user->data->ID,'avatar',true) : get_avatar_url($user->data->ID);
        $avatar = "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

    }

    return $avatar;
}

add_filter('the_password_form','get_the_password_custom_form');
function get_the_password_custom_form() {
	//$post   = get_post( $post );
	global $post;
	$label  = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
	<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
	<p><label for="' . $label . '">' . __( 'Password:' ) . ' <br><input name="post_password" id="' . $label . '" type="password" style="width: 100%" /></label></p><p><input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>
	';

	/**
	 * Filters the HTML output for the protected post password form.
	 *
	 * If modifying the password field, please note that the core database schema
	 * limits the password field to 20 characters regardless of the value of the
	 * size attribute in the form input.
	 *
	 * @since 2.7.0
	 *
	 * @param string $output The password form HTML output.
	 */

	return $output;
}
