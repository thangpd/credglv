jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};
    credglv.preventinputtext_mobilefield = function () {
        $('input.input-number-mobile').bind({
            keydown: function (e) {
                if (e.shiftKey === true) {
                    if (e.which === 9) {
                        return true;
                    }
                    return false;
                }
                if (e.which > 57) {
                    return false;
                }
                if (e.which === 32) {
                    return false;
                }
                return true;
            }
        });
    };
    $(document).ready(function () {
        credglv.preventinputtext_mobilefield();
    });

    $(document).ready(function () {

        $('input.input-number-mobile').on('keyup', function (e) {
            var str_search = $(this).val();
            var patt = /[a-z]/g;
            if (str_search.match(patt) !== null || str_search === '') {
                $('.list_countrycode').addClass('hide');
                $(this).removeClass('width80');
                $(this).addClass('width100');
            } else {
                $('.list_countrycode').removeClass('hide');
                $(this).addClass('width80');
                $(this).removeClass('width100');

            }

        });
        $('.woocommerce-phone-countrycode').on('click', function (e) {
            $('.digit_cs-list').show();
            event.stopPropagation();
        });
        $(document).on('click', function (e) {
            var list = $('.digit_cs-list');
            if (list.css('display') === 'block') {
                list.hide();
            }

        });
        $('.dig-cc-visible').on('click', function (e) {
            $(this).parent().prev().val($(this).data('value'));
        })

        $('.loginViaContainer .dig_wc_mobileLogin').on('click', function (e) {

            if (check_mobile_login()) {

                console.log('true');
                var data = {};
                var data_form = {
                    data: data,
                    action: 'credglv_login'
                };
                $.ajax({
                    type: 'POST',
                    url: credglvConfig.ajaxurl,
                    data: data_form,
                    async:
                        false,
                    success:

                        function (res) {
                            if (res.code === 200) {
                                location.reload(true);
                            } else if (res.code === 403) {

                            }

                        }
                });
            } else {
                alert('invalid mobile number');
                console.log('false');
            }
        });

    });

});