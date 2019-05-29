jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.account_details_toggle_otp = function (form) {
        console.log($(form));
        $(form).find('button[name="save_account_details"]').on('click', function (e) {

            var otp = $(form).find('.otp-code');

            if (!otp.is(':visible')) {

                otp.toggle('show');
                var data = {action: 'sendphone_message'};
                credglv.account_details_sendmessage_otp(form, data);
                e.preventDefault();
            }
        });
    };


    credglv.account_details_sendmessage_otp = function (form, data) {

        // var data = {phone: credglv.get_phone_data(form), action: 'sendphone_message'};
        // console.log(data);
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            success: function (res) {
                if (res.code === 200) {
                    $(form).find('.error_log').text(res.message);

                    console.log(res.code);
                } else {
                    $(form).find('.error_log').text(res.message);
                }
            }
        });
    };

    credglv.onchange_otp = function (form) {
        $(form).find('input#cred_otp_code').on('keyup', function () {
            limitText(this, 4)
        });

        function limitText(field, maxChar) {
            var ref = $(field),
                val = ref.val();
            if (val.length >= maxChar) {
                $(form).submit();
                ref.attr('disabled', 'disabled');
            }
        }

    }

    credglv.loadingbutton_login= function (form) {
        var form_register = $(form);

        console.log(form_register);

        form_register.validate(
            {
                submitHandler: function (form) {
                    form_register.find('button[type="submit"]').toggleClass('running');
                    form.submit();

                }
            }
        )


    };



    $(document).ready(function () {
        credglv.account_details_toggle_otp('form.edit-account');
        credglv.onchange_otp('form.edit-account');
        credglv.loadingbutton_login('form.edit-account');

    });
});