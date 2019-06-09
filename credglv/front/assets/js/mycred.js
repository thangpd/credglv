jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.preventinputtext_usernamefield = function (form) {
        form = $(form);
        form.find('#reg_username').bind({
            keydown: function (e) {
                if (e.which > 47 && e.which < 58) {
                    return true;
                }
                if (e.which > 64 && e.which < 91) {
                    return true;
                }
                if (e.which > 96 && e.which < 123) {
                    return true;
                }
                return false;
            }
        });
    }
    

    $(document).ready(function () {
        // credglv.preventinputtext_usernamefield('form.mycred-transfer');
    });
});