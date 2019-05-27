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

    credglv.onclick_avatar = function(img,input) {
        $(img).on('click',function() {
            $(input).trigger('click')
        })
    }

    credglv.onchange_avatar = function (img,input,form){
        $(input).change(function() {
          readURL(this);
        });
        function readURL(input) {
            if (input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
                var reader = new FileReader();
                reader.onload = function(e) {
                  $(img).attr('src', e.target.result);
                  $(img).attr('width', '70');
                  $(img).attr('height', '70');
                }
                reader.readAsDataURL(input.files[0]);
                console.log($(form))
            }else{
                alert('Please choose an image!');
            }
        }
    }


    $(document).ready(function () {
        credglv.account_details_toggle_otp('form.edit-account');
        credglv.onchange_otp('form.edit-account');
        credglv.onclick_avatar('#user_avatar','input[name="user_avatar"]');
        credglv.onchange_avatar('#user_avatar','input[name="user_avatar"]');
    });
});