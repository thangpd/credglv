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
                
                <div class="wmc-banners">';

	$wmc_html .= '<div class="qr_code">' . do_shortcode( '[credglv_generateqr]' ) . '</div>';

	$wmc_html .= '</div>';

	echo $wmc_html;
	?>
    <input type="text" value="<?php echo $user->get_url_share_link() ?>" id="myInput">
    <!-- The button used to copy the text -->
    <button onclick="myFunction()"><?php echo __( 'Copy text', 'credglv' ); ?></button>
    <button onclick="showAndroidShare()"><?php echo __( 'Share', 'credglv' ); ?></button>

    <script>
        function myFunction() {
            /* Get the text field */
            var copyText = document.getElementById("myInput");

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
    </script>
	<?php
} else {

// fong 
?>

<p>Your account is not fully activated. Some functions may not work properly. Beside, you may not enjoy some privilege that only Officially GLV Member has. </p>

<p>For automatic activation, please ask your introducer to give you the Activation Fee. In case you don't know who your inrtoducer is, please contact us for further support. </p>

<p>Learn more about <a href="https://member.goldleaf-ventures.com/member-activation-fee/">Activation Fee</a>.</p>

<?php 

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
