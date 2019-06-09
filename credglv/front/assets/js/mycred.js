jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.preventinputtext_usernamefield_transfer = function (form) {
        form = $(form);
        form.find('#reg_username').bind({
            keypress: function (e) {
                //alert(e.charCode)
                if (e.charCode > 47 && e.charCode < 58) {
                    return true;
                }
                if (e.charCode > 64 && e.charCode < 91) {
                    return true;
                }
                if (e.charCode > 96 && e.charCode < 123) {
                    return true;
                }
                if (e.charCode === 8){
                    return true;
                }
                return false;
            }
        });
    }
    

    $(document).ready(function () {
        credglv.preventinputtext_usernamefield_transfer('form.mycred-transfer');
    });
});