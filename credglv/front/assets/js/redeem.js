jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.redeem = function () {
        $('form').on('submit', function (e) {
            e.preventDefault();
            var amount = $(this).find('input[name="amount"]').val();
            var data = {
                amount: amount,
                action: 'credglv_order_add_item'
            };
            $.ajax({
                type: 'POST',
                url: credglvConfig.ajaxurl,
                data: data,
                async: false,
                success: function (res) {
                    if (res.code === 200) {
                        console.log(res);
                    } else {
                        console.log(res);
                    }
                }
            });
        })

    };
    $(document).ready(function () {

        credglv.redeem();
    });

});