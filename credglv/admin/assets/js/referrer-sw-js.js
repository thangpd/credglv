jQuery.noConflict();

jQuery(document).ready(function ($) {
    "use strict";


    var TabBlock = {
        s: {
            animLen: 200
        },

        init: function () {
            TabBlock.bindUIActions();
            TabBlock.hideInactive();
        },

        bindUIActions: function () {
            $('.tabBlock-tabs').on('click', '.tabBlock-tab', function () {
                TabBlock.switchTab($(this));
            });
        },

        hideInactive: function () {
            var $tabBlocks = $('.tabBlock');

            $tabBlocks.each(function (i) {
                var
                    $tabBlock = $($tabBlocks[i]),
                    $panes = $tabBlock.find('.tabBlock-pane'),
                    $activeTab = $tabBlock.find('.tabBlock-tab.is-active');

                $panes.hide();
                $($panes[$activeTab.index()]).show();
            });
        },

        switchTab: function ($tab) {
            var $context = $tab.closest('.tabBlock');

            if (!$tab.hasClass('is-active')) {
                $tab.siblings().removeClass('is-active');
                $tab.addClass('is-active');

                TabBlock.showPane($tab.index(), $context);
            }
        },

        showPane: function (i, $context) {
            var $panes = $context.find('.tabBlock-pane');

            // Normally I'd frown at using jQuery over CSS animations, but we can't transition between unspecified variable heights, right? If you know a better way, I'd love a read it in the comments or on Twitter @johndjameson
            $panes.slideUp(TabBlock.s.animLen);
            $($panes[i]).slideDown(TabBlock.s.animLen);
        }
    };

    TabBlock.init();

    var credglv = window.credglv || {};
    credglv.ajax_active_user = function () {
        $('input[name="credglv_active_user"]').on('click', function (e) {
            var active;
            if ($(this).is(":checked")) {
                active = 1;
            } else {
                active = 0;
            }
            var data = {
                user_id: $(this).data('user_id'),
                active: active,
                action: 'ajax_active_user'
            };
            $.ajax({
                type: 'POST',
                url: credglvConfig.ajaxurl,
                data: data,
                async: false,
                success: function (res) {
                    if (res.code === 200) {
                        console.log(res);
                    } else if (res.code === 404) {
                        console.log(res);
                    }
                }
            });
        });
    };

    $(document).ready(function () {
        credglv.ajax_active_user();
    })


});