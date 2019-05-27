jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.validate_login_form = function () {
        var validation_holder = 0;
        $("form.form-login-cred #email").on('blur', function (e) {

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
        $("form.form-login-cred #password").on('blur', function (e) {
            var password = $(this).val();
            if (password === "") {
                $("span.password").html("This field is required.").addClass('validate');
                validation_holder++;
            } else {
                validation_holder--;
                $("span.password").html("");
            }
        });
        $('#submit-button').on('click', function (e) {
            var error = $('.error-msg-front');
            if (validation_holder === 1) {
                e.preventDefault();
                $(this).html("Get Thing Right!").addClass('validate');
            } else {
                // can't form serilize cause captcha of google.
                e.preventDefault();
                var data = {};
                data.captcha = $('#login-form').serialize();

                if (credglv.checksubmit_form()) {
                    var password = $('#login-form #password');
                    if (password.val()) {
                        data.password = password.val();
                    }
                    var email = $('#login-form #email');
                    if (email.val()) {
                        data.email = email.val();
                    }

                    var data_form = {
                            data: data,
                            action: 'credglv_login'
                        }
                    ;

                    $.ajax({
                        type: 'POST',
                        url: credglvConfig.ajaxurl,
                        data: data_form,
                        async:
                            false,
                        success:

                            function (res) {
                                if (res.code === 200) {
                                    error.text(res.message);
                                    location.reload(true);
                                } else if (res.code === 403) {
                                    error.text(res.message);
                                }

                            }
                    });
                } else {
                    error.text('One or more fields cannot be blank');
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
    credglv.checksubmit_form = function (data) {

        var error = 0;
        var password = $('#login-form #password');
        if (password.val()) {
        } else {
            error = 1;
        }
        var email = $('#login-form #email');
        if (email.val()) {
            
        } else {
            error = 1;
        }

        if (error) {
            return false;
        } else {
            return true;
        }
    };

    $(document).ready(function () {

        credglv.validate_login_form();
    });


})
;