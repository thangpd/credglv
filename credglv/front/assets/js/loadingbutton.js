jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.loadingbutton_login= function (form) {
        var form_register = $(form);

        console.log(form_register);

        form_register.validate(
            {
                submitHandler: function (form) {
                    form_register.find('button[type="submit"]').toggleClass('running');
                    form.submit();
                }
            }
        )


    };
    $(document).ready(function () {
         credglv.loadingbutton_login('form.profile-update');
         credglv.loadingbutton_login('form.payment-edit');
         credglv.loadingbutton_login('form.edit-account');
         credglv.loadingbutton_login('form.local-redeem');
         credglv.loadingbutton_login('form.cash-redeem');


    });
});