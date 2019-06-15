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
                
                <div class="wmc-banners" style="display: flex">';

	$wmc_html .= '<div class="qr_code">' . do_shortcode( '[credglv_generateqr]' ) . '</div>';

    $wmc_html .= '<div style="">
                    <video width="300" height="170" controls>
                      <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                      <source src="https://www.w3schools.com/html/mov_bbb.ogg" type="video/ogg">
                      Your browser does not support HTML5 video.
                    </video>
                 </div>';
    $wmc_html .= '</div>';

	echo $wmc_html;
	?>
    <!-- The button used to copy the text -->
    <button data-clipboard-text="<?php echo $user->get_url_share_link() ?>" id="btn_copy" class="woocommerce-Button button btn btn-default ld-ext-right"><?php echo __( 'Copy link', 'credglv' ); ?><div class="ld" id="spinning" style="top: 65%; right: 0"></div></button>
    <button onclick="showAndroidShare()"><?php echo __( 'Share', 'credglv' ); ?></button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script type="text/javascript">
        var target = document.getElementById("spinning1");
        console.log(target);
        var spinner = new Spinner().spin(target);

        var clipboard = new ClipboardJS('button');
        clipboard.on('success', function (){
            jQuery('#btn_copy').toggleClass('running');
            var toggle = setInterval(function(){
                jQuery('#btn_copy').toggleClass('running');
                clearInterval(toggle);
            },1000)
            var load_toaster = setInterval(function(){
                toaster('success','','The Share URL was coppied to your clipboard. Now go and share it!');
                clearInterval(load_toaster);
            },1000);
            // jQuery('#success_noti').stop();
            // jQuery('#success_noti').fadeOut({queue:false,complete: function(){
            //     jQuery('#success_noti').fadeIn({duration: 1500, complete: function(){
            //         jQuery('#success_noti').fadeOut(3000);
            //     }});
            // }});
        })
        function myFunction() {
            jQuery('#btn_copy').toggleClass('running');
            var toggle = setInterval(function(){
                jQuery('#btn_copy').toggleClass('running');
                clearInterval(toggle);
            },1000)
            var load_toaster = setInterval(function(){
                toaster('success','','The Share URL was coppied to your clipboard. Now go and share it!');
                clearInterval(load_toaster);
            },1000);
            // jQuery('#success_noti').stop();
            // jQuery('#success_noti').fadeOut({queue:false,complete: function(){
            //     jQuery('#success_noti').fadeIn({duration: 1500, complete: function(){
            //         jQuery('#success_noti').fadeOut(3000);
            //     }});
            // }});

            var el = document.getElementById("myInput");
            el.select();
            var oldContentEditable = el.contentEditable,
                oldReadOnly = el.readOnly,
                range = document.createRange();

            el.contentEditable = true;
            el.readOnly = false;
            range.selectNodeContents(el);

            var s = window.getSelection();
            s.removeAllRanges();
            s.addRange(range);

            el.setSelectionRange(0, 999999); // A big number, to cover anything that could be inside the element.

            el.contentEditable = oldContentEditable;
            el.readOnly = oldReadOnly;


            document.execCommand('copy');
            el.blur();

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
