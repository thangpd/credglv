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
            $(form).find('.error_log').text('');

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
        $('body').on('click', function (e) {
            var dropdown = form.find('.woocommerce-phone-countrycode');
            if(dropdown.length && e.target!=dropdown){
                    $('.digit_cs-list').hide();
            }
        });
        form.find('.dig-cc-visible').on('click', function (e) {
            $(this).closest('.list_countrycode').find('.woocommerce-phone-countrycode').val($(this).data('value'));
            $(this).closest('.list_countrycode').find('.woocommerce-phone-countrycode').attr('value',$(this).data('value'));
            $(this).closest('.list_countrycode').find('.woocommerce-phone-countrycode').attr('placeholder',$(this).data('value'));
        })
    };

    credglv.checkrequirement = function (form) {
        var form_register = $(form);
        form_register.validate(
            {
                rules: {
                    cred_billing_phone: {
                        required: true,
                        minlength: 6,
                        maxlength: 10,
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
                        maxlength: 4
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
                        required: "A one time password (OTP) was sent to your phone.",
                        minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                }, submitHandler: function (form) {

                    credglv.toggle_loading_button(form);
                }
            }
        )
    };

    credglv.toggle_loading_button = function (form, hide) {


        var button = $(form).find('button[type="submit"]');
        var i = 0;
        button.toggleClass('running');
        credglv.checkstatus_button(1);
    }
    credglv.checkstatus_button = function stateChange(newState) {
        setTimeout(function () {
            var button = $(document).find('button.ld-ext-right.running');
            button.toggleClass('running');
        }, 2000);
    }

    credglv.validate_submitform_register = function (form) {

        $(form).on('submit', function (e) {
            var otp_div = $(document).find('.otp-code');
            console.log(otp_div);
            if ($(this).valid() && otp_div.is(':hidden')) {
                console.log('login 1');
                credglv.sendmessage_otp(form);
                e.preventDefault();
            } else if ($(this).valid()) {
                console.log('login2');
                e.preventDefault();


                credglv.ajax_register(form);
            }

        })
    };

    credglv.ajax_register = function (form) {
        credglv.toggle_loading_button();
        var data = {
            phone: credglv.get_phone_data(form),
            email: $(form).find('#reg_email').val(),
            username: $(form).find('#reg_username').val(),
            otp: $(form).find('#cred_otp_code').val(),
            input_referral: $(form).find('#input_referral').val(),
            action: 'credglv_ajax_register'
        };
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            beforeSend: function (res) {
            },
            success: function (res) {
                $(form).find('.error_log').text(res.message);
                if (res.code === 200) {
                    location.reload();

                } else if (res.code === 400) {
                    //toggle loading button
                    $('#cred_otp_code_login').val('');
                    $(form).find('.error_log').text(res.message);

                } else if (res.code === 403) {
                    //expired otp
                    var otp = $(form).find('input[name="cred_otp_code"]');
                    otp.val('');
                }


            }
        });


    };

    credglv.sendmessage_otp = function (form) {

        var data = {phone: credglv.get_phone_data(form), action: 'credglv_ajax_sendphone_message_register'};
        console.log(data);
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            success: function (res) {
                if (res.code === 200) {
                    var otp_div = $(form).find('.otp-code');
                    otp_div.toggle('hide');
                } else {
                    $(form).find('.error_log').text(res.message);
                }
            }
        });

    };
    credglv.get_phone_data = function (form) {
        var phone_div = $(form).find('.login_countrycode');
        var country_code = phone_div.find('input[name="number_countrycode"]');
        // console.log(country_code);
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
        while (phone_num.charAt(0) === '0') {
            phone_num = phone_num.substr(1);
        }
        return country_code + phone_num;

    }


    credglv.select2login = function () {
        // multiple select with AJAX search
        $('form.register .input-referral').select2({
            theme: "classic",
            ajax: {
                url: credglvConfig.ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, // search query
                        action: 'referrer_ajax_search' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {

                        // data is the array of arrays, and each of them contains ID and the Label of the option
                        $.each(data.results, function (index, text) { // do not forget that "index" is just auto incremented value
                            options.push({id: text.id, text: text.text});
                        });

                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 3 // the minimum of symbols to input before perform a search
        });
    };
    credglv.onchange_otp = function (form) {
        $(form).find('input[name="cred_otp_code"]').on('keyup', function () {
            limitText(this, 4)
        });

        function limitText(field, maxChar) {
            var ref = $(field),
                val = ref.val();
            if (val.length >= maxChar) {
                $(form).submit();
                // ref.attr('disabled', 'disabled');
            }
        }

    }

    credglv.preventinputtext_usernamefield = function (form) {
        form = $(form);
        form.find('#reg_username').bind({
            keypress: function (e) {
                //alert(e.charCode)
                if (e.charCode > 47 && e.charCode < 58) {
                    return true;
                }
                if (e.charCode > 96 && e.charCode < 123) {
                    return true;
                }
                return false;
            }
        });
    }

    $(document).ready(function () {
        credglv.onchange_otp('form.register');
        credglv.preventinputtext_mobilefield('form.register');
        credglv.validate_submitform_register('form.register');
        credglv.checkrequirement('form.register');
        credglv.preventinputtext_usernamefield('form.register');
        credglv.select2login();

        $('form.login').find('.phone_login').nextUntil('.otp-code').hide();
        var autofocus_ready = setInterval(function () {
            $('#hide_button').trigger('click');
            clearInterval(autofocus_ready);
        }, 1000)

        $('.register .woocommerce-Button').on('click', function(){
            console.log('n');
            setTimeout(function(){
                $(this).addClass('login-click');
            },2000)
        });

    });
});