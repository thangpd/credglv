jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.redeem = function () {
        $('form').on('submit', function (e) {
            e.preventDefault();
            var amount = $(this).find('input[name="amount"]').val();
            var type = $(this).find('input[name="type"]');

            var data = {
                amount: amount,
                action: 'credglv_order_add_item'
            };
            if (type.length) {
                data.type = type.val();
            }
            $.ajax({
                type: 'POST',
                url: credglvConfig.ajaxurl,
                data: data,
                async: false,
                success: function (res) {
                    if (res.code === 200) {
                        console.log(res.message);

                        location.reload();

                    } else {
                        alert(res.message);
                    }
                }
            });
        })
    };
    $(document).ready(function () {

        credglv.redeem();
    });

});