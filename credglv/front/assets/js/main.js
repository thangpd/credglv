jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.exmplemain= function () {

    };
    $(document).ready(function () {
        credglv.exmplemain();
        check_img();
    });

});
function check_img(){
    var src = jQuery('.update_img').attr('src');
    var src_pp = jQuery('.update_img_pp').attr('src');
    var src_iden = jQuery('.update_img_iden').attr('src');
    var src_ava = jQuery('.update_img_ava').attr('src');
    if(src === ''){
        jQuery('.update_img').hide();
    }else{
        jQuery('.update_img').show();
    }
    if(src_pp === ''){
        jQuery('.update_img_pp').hide();
    }else{
        jQuery('.update_img_pp').show();
    }
    if(src_iden === ''){
        jQuery('.update_img_iden').hide();
    }else{
        jQuery('.update_img_iden').show();
    }
    if(src_ava === ''){
        jQuery('.update_img_ava').hide();
    }else{
        jQuery('.update_img_ava').show();
    }
}