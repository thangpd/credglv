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
            $(this).parents('.phone_login').removeClass('phone_login-fc');

        })
    };



    credglv.checkrequirement_login = function (form) {
        var form_register = $(form);
        form_register.validate(
            {
                rules: {
                    cred_billing_phone: {
                        required: true,
                        minlength: 6, maxlength: 10,
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
                        required: "",
                        minlength: jQuery.validator.format("At least {0} characters required!")
                    },
                },
                submitHandler: function (form) {

                    //credglv.toggle_loading_button(form);
                    var login_user = $('#login-with-user');
                    if (login_user.prop('checked')) {
                        form.submit();
                    }
                }
            }
        )
    };


    credglv.toggle_loading_button = function (form, hide) {


        var button = $(form).find('button[type="submit"]');
        var i = 0;
        //button.toggleClass('running');
        $('#spinning2').toggle('hide');
        var spinning = setInterval(function(){
            $('#spinning2').toggle('hide');
            clearInterval(spinning);
        },500);
        credglv.checkstatus_button(1);
    }

    credglv.checkstatus_button = function stateChange(newState) {
        setTimeout(function () {
            var button = $(document).find('button.ld-ext-right.running');
            button.toggleClass('running');
        }, 2000);
    }

    credglv.validate_submitform = function (form) {
        $(form).on('submit', function (e) {

            var otp_div = $(document).find('.otp-code');
            var login_phone = $('#login-with-phone');


            if ($(this).valid() && login_phone.prop('checked') && otp_div.is(':hidden')) {
                console.log('login 1');
                credglv.sendmessage_otp(form);
                e.preventDefault();
            } else if ($(this).valid() && form === 'form.login' && login_phone.prop('checked')) {
                console.log('login2');
                e.preventDefault();


                credglv.ajax_login(form);
            }


        })
    };

    credglv.ajax_login = function (form) {
        credglv.toggle_loading_button();
        var data = {
            phone: credglv.get_phone_data(form),
            otp: $(form).find('#cred_otp_code_login').val(),
            action: 'credglv_ajax_login'
        };
        $.ajax({
            type: 'POST',
            url: credglvConfig.ajaxurl,
            data: data,
            async: false,
            beforeSend: function (res) {
            },
            success: function (res) {
                $(form).find('#reg_phone-error').text(res.message);
                if (res.code === 200) {
                    location.reload();

                } else if (res.code === 400) {
                    //toggle loading button
                    $('#cred_otp_code_login').val('');
                    $(form).find('#reg_phone-error').text(res.message);

                } else if (res.code === 403) {
                    //expired otp
                    var otp = $(form).find('input[name="cred_otp_code"]');
                    otp.val('');
                }


            }
        });


    };


    credglv.sendmessage_otp = function (form) {
        credglv.toggle_loading_button();
        var delay = setInterval(function(){
            var data = {phone: credglv.get_phone_data(form), action: 'credglv_sendphone_message_login'};
            console.log(data);
            $.ajax({
                type: 'POST',
                url: credglvConfig.ajaxurl,
                data: data,
                async: false,
                success: function (res) {
                    if (res.code === 200) {
                        $('.error').text('An OTP was sent to your phone.');
                        var otp_div = $(form).find('.otp-code');
                        otp_div.toggle('hide');
                        $('button[type="submit"]').toggle('hide');
                    } else {
                        $(form).find('.error_log').text(res.message);
                    }
                }
            });
            clearInterval(delay);
        },500);

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

    credglv.login_toggle_login = function (form) {
       
        $(form).find('#login-with-phone').on('click', function (e) {
            setTimeout(function() {
                $(form).find('#label-login-with-phone').css('display','none');
            },250);
            setTimeout(function() {
                $(form).find('#label-login-with-user').css('display','block');
            }, 1500);
            
            var phone_login = $(form).find('.phone_login');
            if (phone_login.is(':hidden')) {
                phone_login.toggle('show');
            }
            var user = $(form).find('.phone_login').nextUntil('.otp-code');
            if (user.is(':visible')) {
                user.toggle('hide');
            }
          
        });
        $(form).find('#login-with-user').on('click', function (e) {
            
            setTimeout(function() {
                $(form).find('#label-login-with-phone').css('display','block');
            }, 1500);
            setTimeout(function(){
                $(form).find('#label-login-with-user').css('display','none');
            },250);
            var otp = $(form).find('.otp-code');
            if (otp.is(':visible')) {
                otp.toggle('hide');
            }
            var phone_login = $(form).find('.phone_login');
            if (phone_login.is(':visible')) {
                phone_login.toggle('hide');
            }
            var user = $(form).find('.phone_login').nextUntil('.otp-code');
            if (user.is(':hidden')) {
                user.toggle('show');
            }
            // var autofocus = setInterval(function () {
            //     $('#username').trigger('focus');
            //     console.log('focus');
            //     clearInterval(autofocus);
            // }, 1000);
            $('button[type="submit"]').css('display','block');
            $(form).find('.error_log').text('');
        });
        /*$(form).find('a.login-with-what').on('click', function (e) {
            $(form).find('.phone_login').toggle('hide');
            $(form).find('.phone_login').nextUntil('.otp-code').toggle('show');
            var otp = $(form).find('.otp-code');
            if (otp.is(':visible')) {
                otp.toggle('hide');
            }
            var text_toggle = $('.login-with-what');
            console.log(text_toggle.data("phone"));
            if (text_toggle.data("phone") === 'yes') {
                text_toggle.data("phone", "no");
                text_toggle.text('Login with phone number');
                var autofocus = setInterval(function () {
                    $('#username').trigger('focus');
                    console.log('focus');
                    clearInterval(autofocus);
                }, 1000);
            } else {
                text_toggle.data("phone", "yes");
                text_toggle.text('Login with username/email');
                var autofocus = setInterval(function () {
                    $('#reg_phone').trigger('focus');
                    console.log('focus');
                    clearInterval(autofocus);
                }, 1000);
            }
            e.preventDefault();

        });*/
    };

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
                console.log('otp reach 4 key');
                $('#spinning1').toggleClass('hide');
                var submit = setInterval(function () {
                    $('#spinning1').toggleClass('hide');
                    $(form).submit();
                    clearInterval(submit);
                }, 500);
                //$(form).submit();

                // ref.attr('disabled', 'disabled');
            }
        }

    }
    credglv.button_sumit = function (form) {
        $(form).find('.woocommerce-Button.button.btn.btn-default.ld-ext-right').on('click', function () {
            setTimeout(function(){
                $('.woocommerce-Button.button.btn.btn-default.ld-ext-right').addClass('login-click');
                console.log('test');
                
                setTimeout(function(){
                    if($('input').val() == ''){
                        $('.woocommerce-Button.button.btn.btn-default.ld-ext-right').removeClass('login-click');
                    }
                    if($('.woocommerce-Button.button.btn.btn-default.ld-ext-right').parents('form').find('.phone_login').find('.login_countrycode').find('.error')){
                        console.log('error');
                        $('.woocommerce-Button.button.btn.btn-default.ld-ext-right').removeClass('login-click');
                    }
                }, 1000);
               
            }, 200);
         
        });
       
    }

    $(document).ready(function () {

        
        credglv.button_sumit('form.login');
        credglv.onchange_otp('form.login');
        credglv.preventinputtext_mobilefield('form.login');
        credglv.login_toggle_login('form.login');
        credglv.validate_submitform('form.login');
        credglv.checkrequirement_login('form.login');

        // $('form.login').find('.phone_login').nextUntil('.otp-code').hide();
        var autofocus_ready = setInterval(function () {
            $('#hide_button').trigger('click');
            clearInterval(autofocus_ready);
        }, 1000)

        var opts={
            left: '65%',
            top: '45%'
        };

        var target2 = document.getElementById("spinning2");
        var spinner2 = new Spinner(opts).spin(target2);

        var target1 = document.getElementById("spinning1");
        var spinner1 = new Spinner().spin(target1);

    });
});