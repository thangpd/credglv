/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */

jQuery.noConflict();

jQuery(document).ready(function ($) {
    "use strict";
    $(document).on('click', '.credglv-btn-cart.checkout-button', function (e) {
        e.preventDefault();
        let button = $(this);
        let textButton = button.html();

        button.prop('disabled', true);
        button.html('<i class="fa fa-spin fa-spinner"></i>' + textButton);
        /*
            data-post_type_id="<?php echo esc_html( 119229 ); ?>"
            data-post_type="credglv_bundle"
            data-price="<?php echo esc_html( 15 ); ?>"
            data-params="">
            */
        let price = button.data('price');
        let params = button.data('params');
        let post_type_id = button.data('post_type_id');
        let post_type = button.data('post_type');
        let expire_date = button.data('expire_date');
        let url_return = encodeURIComponent(window.location.pathname);
        let data = {
            action: 'ajax_checkout_button',
            step: 'checkout_bundle',
            price: price,
            post_type_id: post_type_id,
            post_type: post_type,
            expire_date: expire_date,
            url_return: url_return,
            params: params
        };

        $.post(ajaxurl, data, function (res) {
            if (res.success) {
                window.location = res.checkout_url;
            } else {
                console.log(res.message);
            }
        }).always(function () {
            button.html(textButton);
            button.prop('disabled', false);
        });
    });
});