jQuery.noConflict();

jQuery(document).ready(function ($) {
    "use strict";


    var credglv = window.credglv || {};
    credglv.ajax_active_order = function () {
        $('input[name="credglv_active_order"]').on('click', function (e) {
            var active;
            if ($(this).is(":checked")) {
                active = 1;
            } else {
                active = 0;
            }
            var data = {
                order_id: $(this).data('order_id'),
                active: active,
                action: 'credglv_ajax_active_order'
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
                        console.log(res.message);
                    }
                }
            });
        });
    };

    $(document).ready(function () {
        credglv.ajax_active_order();
    })


});