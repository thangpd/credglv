jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.preventinputtext_usernamefield = function (form) {
        form = $(form);
        form.find('input[name="mycred_new_transfer[recipient_id]"]').bind({
            keydown: function (e) {
                console.log('a');
                if (e.shiftKey === true) {
                    if (e.which === 9) {
                        return true;
                    }
                    return false;
                }
                if (e.which > 57 && e.which < 65) {
                    return false;
                }
                if (e.which > 90 && e.which < 97) {
                    return false;
                }
                if (e.which > 122) {
                    return false;
                }
                if (e.which === 32) {
                    return false;
                }
                return true;
            }
        });
    }
    

    $(document).ready(function () {
        credglv.preventinputtext_usernamefield('form.mycred-transfer');
    });
});