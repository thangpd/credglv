<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$user    = new \credglv\models\UserModel();
$user_id = get_current_user_id();

if ( $user->check_actived_referral( $user_id ) ) {

	$wmc_html = '<div id="credglv-qr-code">
                <h2>' . __( 'Your share link QR code', 'credglv' ) . '</h2>
                <div class="wmc-banners">';

	$wmc_html .= '<div class="qr_code">' . do_shortcode( '[credglv_generateqr]' ) . '</div>';

	$wmc_html .= '</div>';

	echo $wmc_html;
	?>
    <div class="link-url">
        <h2><?php echo __( 'Your share link url:', 'credglv' ); ?></h2>
		<?php echo $user->get_url_share_link() ?>
    </div>
	<?php
} else {
	echo 'Your account is not active. Contact admin or transfer at least ' . credglv()->config->credglv_joining_fee . ' to this account to active';

};


?>


<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
