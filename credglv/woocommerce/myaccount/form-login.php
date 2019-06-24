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
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;</label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Username or email address" name="username" autocapitalize="none" pattern="[a-z]*"
                           id="username" autocomplete="username"
                           value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"/><?php // @codingStandardsIgnoreLine ?>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;</label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password"
                           id="password" autocomplete="current-password"/>
                </p>
            </div>
			<?php do_action( 'woocommerce_login_form' ); ?>

            <p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                <button type="submit" class="woocommerce-Button button btn btn-default ld-ext-right" name="login"
                        value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'LOG IN', 'woocommerce' ); ?><div class="ld ld-spinner ld-spin"></div></button>

            </p>
            <p class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . credglv()->config->getUrlConfigs( 'credglv_register' ) ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></a> or

                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
            </p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

        </form>

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

    </div>


</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
