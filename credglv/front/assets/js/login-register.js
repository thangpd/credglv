jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};
    credglv.preventinputtext_mobilefield = function (form) {
        form = $(form);
        form.find('input.input-number-mobile').bind({
            keydown: function (e) {
                if (e.shiftKey === true) {
                    if (e.which === 9) {
                        return true;
                    }
                    return false;
                }
                if (e.which > 57) {
                    return false;
                }
                if (e.which === 32) {
                    return false;
                }
                return true;
            }
        });
        form.find('input.input-number-mobile').on('keyup', function (e) {
            var str_search = $(this).val();
            var patt = /[a-z]/g;
            if (str_search.match(patt) !== null || str_search === '') {
                form.find('.list_countrycode').addClass('hide');
                $(this).removeClass('width80');
                $(this).addClass('width100');
            } else {
                form.find('.list_countrycode').removeClass('hide');
                $(this).addClass('width80');
                $(this).removeClass('width100');

            }

        });
        form.find('.woocommerce-phone-countrycode').on('click', function (e) {
            $(form).find('.digit_cs-list').show();
            event.stopPropagation();
        });
        $(document).on('click', function (e) {
            var list = $('.digit_cs-list');
            list.hide();
        });
        form.find('.dig-cc-visible').on('click', function (e) {
            $(this).parent().prev().val($(this).data('value'));
        })
    };

    credglv.checkrequirement = function (form) {
        var form_register = $(form);
        form_register.validate(
            {
                rules: {
                    cred_billing_phone: {
                        required: true,
                        minlength: 6
                    },
                    username: {
                        required: true,
                        minlength: 1
                    },
                    email: {
                        required: true,
                        minlength: 5
                    },
                    cred_otp_code: {
                        required: true,
                        minlength: 4
                    },
                },
                messages: {
                    cred_billing_phone: {
                        required: "Required.",
                        // minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                    user: {
                        required: "Required.",
                        // minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                    email: {
                        required: "Required.",
                        // minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                    cred_otp_code: {
                        required: "We sent verify code to your phone.",
                        // minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                }
            }
        )
    };
    credglv.checkrequirement_login = function (form) {
        var form_register = $(form);
        form_register.validate(
            {
                rules: {
                    cred_billing_phone: {
                        required: true,
                        minlength: 6
                    },
                    cred_otp_code: {
                        required: true,
                        minlength: 4
                    },
                },
                messages: {
                    cred_billing_phone: {
                        required: "Required.",
                        // minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                    cred_otp_code: {
                        required: "We sent verify code to your phone.",
                        minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                }
            }
        )
    };

    credglv.validate_submitform = function (form) {
        $(form).on('submit', function (e) {

            var otp_div = $(this).find('.otp-code');

            if ($(this).valid() && otp_div.data('phone') === 'yes' && otp_div.is(':hidden')) {
                console.log('login 1');
                credglv.sendmessage_otp(form);
                e.preventDefault();
            } else {
                if ($(this).valid() && form === 'form.login' && otp_div.data('phone') === 'yes') {
                    console.log('login2');
                    e.preventDefault();
                    credglv.ajax_login(form);
                }
            }


        })
    };
    credglv.ajax_login = function (form) {
        var data = {
            phone: credglv.get_phone_data(form),
            otp: $(form).find('#cred_otp_code_login').val(),
            action: 'credglv_login'
        };
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            success: function (res) {

                $(form).find('.error_log').text(res.message);
                if (res.code === 200) {
                    var otp_div = $(this).find('.otp-code');
                    otp_div.toggle('hide');

                    location.reload();
                }
            }
        });
    };


    credglv.sendmessage_otp = function (form) {

        var data = {phone: credglv.get_phone_data(form), action: 'sendphone_message'};
        console.log(data);
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            success: function (res) {
                $(form).find('.error_log').text(res.message);
            }
        });
    };
    credglv.get_phone_data = function (form) {
        var phone_div = $(form).find('.login_countrycode');
        var country_code = phone_div.find('input[name="number_countrycode"]');
        console.log(country_code);
        if (country_code.length) {
            if (country_code.val()) {
                country_code = country_code.val();
            } else {
                country_code.val('+84');
                country_code = '+84';
            }
        }
        var phone_num = phone_div.find('input[name="cred_billing_phone"]');
        if (phone_num.length) {
            phone_num = phone_num.val();
        }
        return country_code + phone_num;

    }

    credglv.login_toggle_login = function (form) {
        $(form).find('a.login-with-what').on('click', function (e) {
            $(form).find('.phone_login').toggle('hide');
            $(form).find('.myaccount-login-page').toggle('hide');
            var otp = $(form).find('.otp-code');
            if (otp.is(':visible')) {
                otp.toggle('hide');
            }
            console.log(otp.attr("data-phone"));


            if (otp.attr("data-phone") === 'yes') {
                otp.attr("data-phone", "no");
            } else {
                otp.attr("data-phone", "yes");
            }
            e.preventDefault();

        });
    };
    $(document).ready(function () {
        credglv.preventinputtext_mobilefield('form.register');
        credglv.preventinputtext_mobilefield('form.login');
        credglv.validate_submitform('form.register');
        credglv.checkrequirement('form.register');


        credglv.login_toggle_login('form.login');
        credglv.validate_submitform('form.login');
        credglv.checkrequirement_login('form.login');
    });
});