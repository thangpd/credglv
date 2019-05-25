jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};


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

        credglv.onclick_avatar('#user_avatar','input[name="user_avatar"]');
        credglv.onchange_avatar('#user_avatar','input[name="user_avatar"]');

    });
});