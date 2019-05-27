jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    // Dropdown list


    credglv.redeem = function () {
        var form=$('form');
        form.find('button[type="submit"]').on('click', function (e) {
                e.preventDefault();
                var amount = form.find('input[name="amount"]').val();
                var type = form.find('input[name="type"]');
                var data = {
                    amount: amount,
                    action: 'credglv_order_add_item'
                };
                if (type.length) {
                    data.type = type.val();
                }
                $(this).toggleClass('running');
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
                            location.reload();

                        }
                    }
                });
            }
        )
    };
    $(document).ready(function () {

        credglv.redeem();
    });

});