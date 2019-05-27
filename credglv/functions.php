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
