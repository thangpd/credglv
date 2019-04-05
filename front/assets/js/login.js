jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.select2login = function () {

        // multiple select with AJAX search
        $('.referrer-ajax-search').select2({
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
        $("form.form-login-cred #repassword").on('blur', function (e) {
            var repassword = $(this).val();
            if (repassword === "") {
                $("span.repassword").html("This field is required.").addClass('validate');
                validation_holder = 1;
            } else {
                if (repassword != password_general) {
                    $("span.repassword").html("Password does not match!").addClass('validate');
                    validation_holder = 1;
                } else {
                    validation_holder--;
                    $("span.repassword").html("");
                }
            }
        });
        $('.form-login-cred button[type=submit]').on('click', function (e) {
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
                var form_data = {
                        password: $('#password').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val(),
                        referrer: referrer
                    }
                ;
                var data = {data: form_data,action:''};
                console.log(data);

                /*$.ajax({
                    type: 'POST',
                    url: credglvConfig.ajaxurl,
                    data: data,
                    async:
                        false,
                    success:

                        function (data) {
                            if (data.nocaptcha === 'true') {
                                data_2 = 1;
                            } else if (data.spam === 'true') {
                                data_2 = 1;
                            } else {
                                data_2 = 0;
                            }

                        }
                })
                */
                /*if (data_2 != 0) {
                    e.preventDefault();
                    if (data_2 == 1) {
                        alert('Please check the captcha');
                    } else {
                        alert('Please Don’t spam');
                    }
                } else {
                    $('#commentform').submit()
                }*/
            }
        });

    };
    credglv.submit_form = function () {

    };
    $(document).ready(function () {

        credglv.select2login();
        credglv.validate_login_form();
        credglv.submit_form();

    });
});