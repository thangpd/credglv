jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.select2login = function () {

        // multiple select with AJAX search
        $('#register-form .referrer-ajax-search').select2({
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
    credglv.validate_login_form = function () {
        var validation_holder = 0;
        var password_general;
        $("form.form-register-cred #email").on('blur', function (e) {

            var email = $(this).val();
            var email_regex = /^[\w%_\-.\d]+@[\w.\-]+.[A-Za-z]{2,6}$/; // reg ex email check
            if (email === "") {
                $("span.email").html("This field is required.").addClass('validate');
                validation_holder++;
            } else {
                if (!email_regex.test(email)) { // if invalid email
                    $("span.email").html("Invalid Email!").addClass('validate');
                    validation_holder++;
                } else {
                    validation_holder--;

                    $("span.email").html("");
                }
            }

        });
        $("form.form-register #password").on('blur', function (e) {
            var password = $("#register-form #password").val();
            password_general = password;
            var password_regex = /^(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
            if (password === "") {
                $("span.password").html("This field is required.").addClass('validate');
                validation_holder++;
            } else {
                /* if (!password_regex.test(password)) {
                     // $("span.password").html("Password should contain at least (one digit, one lower case, one upper case, 8 from the mentioned characters)").addClass('validate');
                     console.error("Password should contain at least one digit, one lower case, one upper case, 8 from the mentioned characters");

                     validation_holder++;

                 } */
                validation_holder--;

                $("span.password").html("");
            }
        });
        $("#register-form #repassword").on('blur', function (e) {
            var repassword = $("#register-form #repassword").val();
            var password_general = $("#register-form #password").val();
            if (repassword == "") {
                $("span.repassword").html("This field is required.").addClass('validate');
                validation_holder = 1;
            } else {
                if (repassword !== password_general) {
                    $("span.repassword").html("Password does not match!").addClass('validate');
                    validation_holder = 1;
                } else {
                    validation_holder--;
                    $("span.repassword").html("");
                }
            }
        });
        $('.form-register-cred button[type=submit]').on('click', function (e) {
            if (validation_holder === 1) {
                e.preventDefault();
                $("span.submit").html("Get Thing Right!").addClass('validate');
            } else {
                // can't form serilize cause captcha of google.
                e.preventDefault();
                var data_2;
                var referrer = $('#referrer').val();
                if (referrer) {
                } else {
                    referrer = '';
                }
                var data = {data: $('#register-form').serialize(), action: 'verify_captcha'};

                $.ajax({
                    type: 'POST',
                    url: credglvConfig.ajaxurl,
                    data: data,
                    async:
                        false,
                    success:

                        function (data) {
                            console.log(data);
                            if (data.nocaptcha === 'true') {
                                data_2 = 1;
                            } else if (data.spam === 'true') {
                                data_2 = 1;
                            } else {
                                data_2 = 0;
                            }

                        }
                });
                if (data_2 != 0) {
                    e.preventDefault();
                    if (data_2 == 1) {
                        alert('Please check the captcha');
                    } else {
                        alert('Please Don’t spam');
                    }
                } else {


                    // $('#register-form').submit()

                }
            }
        });


        //event phone change
        var x = 1;
        $('#register-form #main-phone').on('keyup', function (e) {
            if (credglv.checksubmit_form()) {

            } else {
                $(this).val('');
                return;
            }
            if (!x) {
                return;
            }
            setInterval(function () {
                $('#register-form .verify-block').removeClass('hide');
            }, 2000);
            console.log('no');
            x--;
        });

    };
    credglv.checksubmit_form = function () {

        var error = 0;
        if ($('#register-form #sub-phone').val()) {

        } else {
            error = 1;
        }
        if ($('#register-form #main-phone').val()) {

        } else {
            error = 1;
        }
        if ($('#register-form #password').val()) {

        } else {
            error = 1;
        }
        if ($('#register-form #email').val()) {

        } else {
            error = 1;
        }
        var error_msg = $('#register-form .error-msg-front');
        if (error) {
            error_msg.text('One or more fields cannot be blank');
        } else {
            error_msg.text('');
        }
        if (error) {
            return false;
        } else {
            return true;
        }
    };

    credglv.otp_verify = function () {
        var form_data;
        var get_data_form = function () {
            var phone = get_phone_data();
            form_data = {
                password: $('#register-form #password').val(),
                email: $('#register-form #email').val(),
                phone: phone,
                referrer: $('#register-form #referrer').val(),
                otp: $('#register-form #otp').val(),
            };
            return form_data;
        };
        var get_phone_data = function () {
            var sub_phone = $('#register-form #sub-phone').val().replace('+', '');
            var main_phone = $('#register-form #main-phone').val();
            return sub_phone + "" + main_phone;
        };
        $('#register-form #otp').on('keyup', function () {
                console.log('otp verify');
                if (this.value.length > 3) {
                    $(this).attr('disabled', 'disabled');
                    console.log('ajax verify code');

                    var data = {data: get_data_form(), action: 'register_new_user'};
                    // var data = {data: form_data, action: 'verify_otp'};
                    $.ajax({
                        type: 'POST',
                        url: credglvConfig.ajaxurl,
                        data: data,
                        async: false,
                        success: function (res) {
                            var error_msg = $('#register-form .otp');

                            if (res.code === 403) {
                                $(this).attr('disabled', 'disabled');
                                $(this).val('');
                                error_msg.text(res.message + ". Please wait for a new OTP");
                                credglv.sendmessage_otp();
                            } else if (res.code === 400) {
                                $(this).attr('disabled', 'disabled');
                                $(this).val('');
                            } else if (res.code === 200) {
                                error_msg.text(res.message);
                                // window.location.replace(res.data.redirect)
                            } else {
                                console.error('cant get through');
                            }
                        }
                    });
                }
            }
        );
        credglv.sendmessage_otp = function () {
            var data = {phone: get_phone_data(), action: 'sendphone_message'};
            $.ajax({
                type: 'POST',
                url: credglvConfig.ajaxurl,
                data: data,
                async: false,
                success: function (res) {
                    var otp_mes = $('#register-form .otp');

                    if (res.code === 403) {
                        otp_mes.text(res.message);

                    } else if (res.code === 200) {
                        otp_mes.text(res.message);
                    }
                }
            });
        }


    }
    ;

    $(document).ready(function () {
        credglv.otp_verify();
        credglv.select2login();
        credglv.validate_login_form();

    });


})
;