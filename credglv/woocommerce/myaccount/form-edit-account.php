<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>


    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span
                    class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name"
               id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>"/>
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
        <label for="cred_billing_phone">
			<?php _e( 'Mobile Number', 'woocommerce' ); ?> <span class="required">*</span>
        </label>

        <input type="text" class="input-text" name="cred_billing_phone" id="cred_billing_phone"
               value="<?php
		       echo esc_attr( get_user_meta( $user->data->ID, \credglv\front\controllers\UserController::METAKEY_PHONE, true ) ); ?>"
               maxlength="10"/>
    </p>
    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span
                    class="required">*</span></label>
        <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email"
               id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>"/>
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
        <label for="cred_user_pin">
			<?php _e( 'Pin', 'woocommerce' );
			echo get_user_meta( $user->data->ID, \credglv\front\controllers\UserController::METAKEY_PIN, true )
			?>
            <span
                    class="required">*</span>
        </label>

        <input type="password" class="input-text"
               name="<?php echo \credglv\front\controllers\UserController::METAKEY_PIN; ?>" id="cred_user_pin"
               value=""
               maxlength="4"/>
    </p>
    <div class="clear"></div>


    <fieldset>
        <legend><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text"
                   name="password_current" id="password_current" autocomplete="off"/>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1"
                   id="password_1" autocomplete="off"/>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2"
                   id="password_2" autocomplete="off"/>
        </p>
    </fieldset>
    <div class="clear"></div>
    <p class="form-row form-row-wide otp-code hide" data-phone="yes">
        <label for="cred_otp_code">
			<?php _e( 'OTP', 'credglv' ); ?> <span class="required">*</span>
        </label>
        <input type="tel" pattern="[0-9]*" class="input-otp-code"
               name="cred_otp_code"
               id="cred_otp_code"
               value="" maxlength="4"/>
        <span class="error_log"></span>
    </p>
	<?php do_action( 'woocommerce_edit_account_form' ); ?>

    <p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
        <button type="submit" class="woocommerce-Button button btn btn-default ld-ext-right" name="save_account_details"
                value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?>
            <div class="ld ld-spinner ld-spin"></div>
        </button>
        <input type="hidden" name="action" value="save_account_details"/>
    </p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
