<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="u-columns col2-set" id="customer_login">

    <div class="u-column1 col-1">

		<?php endif; ?>

        <!-- <div align="center"><h2><?php //esc_html_e( 'Login', 'woocommerce' ); ?></h2></div> -->

        <form class="woocommerce-form woocommerce-form-login login" method="post">
           
			<?php do_action( 'woocommerce_login_form_start' ); ?>
            <div class="myaccount-login-page hide">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide f-p-focus mt-10 f-bd">
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text us" name="username" autocapitalize="none" pattern="[a-z]*"
                           id="username" autocomplete="username"
                           value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"/><?php // @codingStandardsIgnoreLine ?>
                    <label class="f-label">Username or address</label>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide f-p-focus custom-mg mt-40 f-bd">
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password"
                           id="password" autocomplete="current-password"/>
                           <label class="f-label">Password</label>
                </p>
            </div>
			<?php do_action( 'woocommerce_login_form' ); ?>

            <p class="form-row form-btn-login">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                <button type="submit" class="woocommerce-Button button btn btn-default ld-ext-right" name="login"
                        value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><span class="btn-label"><?php esc_html_e( 'LOG IN', 'woocommerce' ); ?></span> <div class="ld" id="spinning"></div></button>

            </p>
            <p class="form-row form-row-wide f-login-title">
                <label for="login-with-phone" id="label-login-with-phone" style="display: none"> <input type="radio" id="login-with-phone" name="selector" checked>
                    <a class="login-type" ><span class="login-type-1" ><?php echo __( 'Or log in with Mobile number', 'credglv' ); ?></span></a></label>
                <label for="login-with-user" id="label-login-with-user"> <input type="radio" id="login-with-user" name="selector">
                <a class="login-type"> <span class="login-type"><?php echo __( 'Or log in with Username/email', 'credglv' ); ?></a></span></label>
            </p>
            <p class="woocommerce-LostPassword lost_password f-lost-pass">
                <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . credglv()->config->getUrlConfigs( 'credglv_register' ) ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></a>

                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
            </p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

        </form>

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

    </div>


</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
