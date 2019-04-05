/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */
;(function($, credglv){
    function Toaster() {
        var $container;
        var listener;
        var toastId = 0;
        var toastType = {
            error: 'error',
            info: 'info',
            success: 'success',
            warning: 'warning'
        };

        var toastr = {
            clear: clear,
            remove: remove,
            error: error,
            getContainer: getContainer,
            info: info,
            options: {},
            subscribe: subscribe,
            success: success,
            version: '2.1.3',
            warning: warning
        };

        var previousToast;

        return toastr;

        ////////////////

        function error(message, title, optionsOverride) {
            return notify({
                type: toastType.error,
                iconClass: getOptions().iconClasses.error,
                message: message,
                optionsOverride: optionsOverride,
                title: title
            });
        }

        function getContainer(options, create) {
            if (!options) { options = getOptions(); }
            $container = $('#' + options.containerId);
            if ($container.length) {
                return $container;
            }
            if (create) {
                $container = createContainer(options);
            }
            return $container;
        }

        function info(message, title, optionsOverride) {
            return notify({
                type: toastType.info,
                iconClass: getOptions().iconClasses.info,
                message: message,
                optionsOverride: optionsOverride,
                title: title
            });
        }

        function subscribe(callback) {
            listener = callback;
        }

        function success(message, title, optionsOverride) {
            return notify({
                type: toastType.success,
                iconClass: getOptions().iconClasses.success,
                message: message,
                optionsOverride: optionsOverride,
                title: title
            });
        }

        function warning(message, title, optionsOverride) {
            return notify({
                type: toastType.warning,
                iconClass: getOptions().iconClasses.warning,
                message: message,
                optionsOverride: optionsOverride,
                title: title
            });
        }

        function clear($toastElement, clearOptions) {
            var options = getOptions();
            if (!$container) { getContainer(options); }
            if (!clearToast($toastElement, options, clearOptions)) {
                clearContainer(options);
            }
        }

        function remove($toastElement) {
            var options = getOptions();
            if (!$container) { getContainer(options); }
            if ($toastElement && $(':focus', $toastElement).length === 0) {
                removeToast($toastElement);
                return;
            }
            if ($container.children().length) {
                $container.remove();
            }
        }

        // internal functions

        function clearContainer (options) {
            var toastsToClear = $container.children();
            for (var i = toastsToClear.length - 1; i >= 0; i--) {
                clearToast($(toastsToClear[i]), options);
            }
        }

        function clearToast ($toastElement, options, clearOptions) {
            var force = clearOptions && clearOptions.force ? clearOptions.force : false;
            if ($toastElement && (force || $(':focus', $toastElement).length === 0)) {
                $toastElement[options.hideMethod]({
                    duration: options.hideDuration,
                    easing: options.hideEasing,
                    complete: function () { removeToast($toastElement); }
                });
                return true;
            }
            return false;
        }

        function createContainer(options) {
            $container = $('<div/>')
                .attr('id', options.containerId)
                .addClass(options.positionClass);

            $container.appendTo($(options.target));
            return $container;
        }

        function getDefaults() {
            return {
                tapToDismiss: true,
                toastClass: 'toast',
                containerId: 'toast-container',
                debug: false,

                showMethod: 'fadeIn', //fadeIn, slideDown, and show are built into jQuery
                showDuration: 300,
                showEasing: 'swing', //swing and linear are built into jQuery
                onShown: undefined,
                hideMethod: 'fadeOut',
                hideDuration: 1000,
                hideEasing: 'swing',
                onHidden: undefined,
                closeMethod: false,
                closeDuration: false,
                closeEasing: false,
                closeOnHover: true,

                extendedTimeOut: 1000,
                iconClasses: {
                    error: 'toast-error',
                    info: 'toast-info',
                    success: 'toast-success',
                    warning: 'toast-warning'
                },
                iconClass: 'toast-info',
                positionClass: 'toast-top-right',
                timeOut: 5000, // Set timeOut and extendedTimeOut to 0 to make it sticky
                titleClass: 'toast-title',
                messageClass: 'toast-message',
                escapeHtml: false,
                target: 'body',
                closeHtml: '<button type="button">&times;</button>',
                closeClass: 'toast-close-button',
                newestOnTop: true,
                preventDuplicates: false,
                progressBar: false,
                progressClass: 'toast-progress',
                rtl: false
            };
        }

        function publish(args) {
            if (!listener) { return; }
            listener(args);
        }

        function notify(map) {
            var options = getOptions();
            var iconClass = map.iconClass || options.iconClass;

            if (typeof (map.optionsOverride) !== 'undefined') {
                options = $.extend(options, map.optionsOverride);
                iconClass = map.optionsOverride.iconClass || iconClass;
            }

            if (shouldExit(options, map)) { return; }

            toastId++;

            $container = getContainer(options, true);

            var intervalId = null;
            var $toastElement = $('<div/>');
            var $titleElement = $('<div/>');
            var $messageElement = $('<div/>');
            var $progressElement = $('<div/>');
            var $closeElement = $(options.closeHtml);
            var progressBar = {
                intervalId: null,
                hideEta: null,
                maxHideTime: null
            };
            var response = {
                toastId: toastId,
                state: 'visible',
                startTime: new Date(),
                options: options,
                map: map
            };

            personalizeToast();

            displayToast();

            handleEvents();

            publish(response);

            if (options.debug && console) {
                credglv.log(response);
            }

            return $toastElement;

            function escapeHtml(source) {
                if (source == null) {
                    source = '';
                }

                return source
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function personalizeToast() {
                setIcon();
                setTitle();
                setMessage();
                setCloseButton();
                setProgressBar();
                setRTL();
                setSequence();
                setAria();
            }

            function setAria() {
                var ariaValue = '';
                switch (map.iconClass) {
                    case 'toast-success':
                    case 'toast-info':
                        ariaValue =  'polite';
                        break;
                    default:
                        ariaValue = 'assertive';
                }
                $toastElement.attr('aria-live', ariaValue);
            }

            function handleEvents() {
                if (options.closeOnHover) {
                    $toastElement.hover(stickAround, delayedHideToast);
                }

                if (!options.onclick && options.tapToDismiss) {
                    $toastElement.click(hideToast);
                }

                if (options.closeButton && $closeElement) {
                    $closeElement.click(function (event) {
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        } else if (event.cancelBubble !== undefined && event.cancelBubble !== true) {
                            event.cancelBubble = true;
                        }

                        if (options.onCloseClick) {
                            options.onCloseClick(event);
                        }

                        hideToast(true);
                    });
                }

                if (options.onclick) {
                    $toastElement.click(function (event) {
                        options.onclick(event);
                        hideToast();
                    });
                }
            }

            function displayToast() {
                $toastElement.hide();

                $toastElement[options.showMethod](
                    {duration: options.showDuration, easing: options.showEasing, complete: options.onShown}
                );

                if (options.timeOut > 0) {
                    intervalId = setTimeout(hideToast, options.timeOut);
                    progressBar.maxHideTime = parseFloat(options.timeOut);
                    progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
                    if (options.progressBar) {
                        progressBar.intervalId = setInterval(updateProgress, 10);
                    }
                }
            }

            function setIcon() {
                if (map.iconClass) {
                    $toastElement.addClass(options.toastClass).addClass(iconClass);
                }
            }

            function setSequence() {
                if (options.newestOnTop) {
                    $container.prepend($toastElement);
                } else {
                    $container.append($toastElement);
                }
            }

            function setTitle() {
                if (map.title) {
                    var suffix = map.title;
                    if (options.escapeHtml) {
                        suffix = escapeHtml(map.title);
                    }
                    $titleElement.append(suffix).addClass(options.titleClass);
                    $toastElement.append($titleElement);
                }
            }

            function setMessage() {
                if (map.message) {
                    var suffix = map.message;
                    if (options.escapeHtml) {
                        suffix = escapeHtml(map.message);
                    }
                    $messageElement.append(suffix).addClass(options.messageClass);
                    $toastElement.append($messageElement);
                }
            }

            function setCloseButton() {
                if (options.closeButton) {
                    $closeElement.addClass(options.closeClass).attr('role', 'button');
                    $toastElement.prepend($closeElement);
                }
            }

            function setProgressBar() {
                if (options.progressBar) {
                    $progressElement.addClass(options.progressClass);
                    $toastElement.prepend($progressElement);
                }
            }

            function setRTL() {
                if (options.rtl) {
                    $toastElement.addClass('rtl');
                }
            }

            function shouldExit(options, map) {
                if (options.preventDuplicates) {
                    if (map.message === previousToast) {
                        return true;
                    } else {
                        previousToast = map.message;
                    }
                }
                return false;
            }

            function hideToast(override) {
                var method = override && options.closeMethod !== false ? options.closeMethod : options.hideMethod;
                var duration = override && options.closeDuration !== false ?
                    options.closeDuration : options.hideDuration;
                var easing = override && options.closeEasing !== false ? options.closeEasing : options.hideEasing;
                if ($(':focus', $toastElement).length && !override) {
                    return;
                }
                clearTimeout(progressBar.intervalId);
                return $toastElement[method]({
                    duration: duration,
                    easing: easing,
                    complete: function () {
                        removeToast($toastElement);
                        clearTimeout(intervalId);
                        if (options.onHidden && response.state !== 'hidden') {
                            options.onHidden();
                        }
                        response.state = 'hidden';
                        response.endTime = new Date();
                        publish(response);
                    }
                });
            }

            function delayedHideToast() {
                if (options.timeOut > 0 || options.extendedTimeOut > 0) {
                    intervalId = setTimeout(hideToast, options.extendedTimeOut);
                    progressBar.maxHideTime = parseFloat(options.extendedTimeOut);
                    progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
                }
            }

            function stickAround() {
                clearTimeout(intervalId);
                progressBar.hideEta = 0;
                $toastElement.stop(true, true)[options.showMethod](
                    {duration: options.showDuration, easing: options.showEasing}
                );
            }

            function updateProgress() {
                var percentage = ((progressBar.hideEta - (new Date().getTime())) / progressBar.maxHideTime) * 100;
                $progressElement.width(percentage + '%');
            }
        }

        function getOptions() {
            return $.extend({}, getDefaults(), toastr.options);
        }

        function removeToast($toastElement) {
            if (!$container) { $container = getContainer(); }
            if ($toastElement.is(':visible')) {
                return;
            }
            $toastElement.remove();
            $toastElement = null;
            if ($container.children().length === 0) {
                $container.remove();
                previousToast = undefined;
            }
        }

    };
    var Ui = {
        run : function () {
            credglv.log('Cred UI is running');
            this.setup();
            this.dropdownList();
            this.modal();
        },
        loading : {
            show : function(message=''){
                var loading = $('#credglv-loading');
                var loader = credglv.config.ui.preloader ? credglv.config.ui.preloader : '<div class="credglv-loader"></div>';
                if (loading.length == 0) {
                    loading = $('<div/>', {
                        id : 'credglv-loading'
                    });
                    if(message===''){
                        loading.html('<div id="credglv-loading-container">' + loader +'</div>');
                    }else{
                        loading.html('<div id="credglv-loading-container" style="background-color: white">' + loader +'<div class="message_loading"> '+ message +'</div></div>');
                    }
                    $('body').append('<div id="credglv-backdrop"></div>');
                    $('body').append(loading);
                    $('body').css("overflow","hidden");
                }
                loading.fadeIn();
                $('#credglv-backdrop').show();
            },
            hide : function () {
                var loading = $('#credglv-loading');
                if (loading.length > 0) {
                    loading.hide();
                    if (!$('.credglv-popup').is(':visible')) $('#credglv-backdrop').hide();
                    $('body').css("overflow", "");
                }
                
            }
        },
        select2 :  function () {
            if ($.fn.select2) {
                $('select[data-select-2]').each(function(i, e){
                    $(e).select2({

                    });
                });
                $('select[data-select2-ajax]').select2({
                    ajax: {
                        url: credglvConfig.ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            var data = $(this).data();
                            return {
                                q: params.term,
                                page: params.page,
                                action : data.action
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;

                            return {
                                results: data.data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 1
                });

            }
        },
        toaster : new Toaster(),
        setup : function () {
            var self = this;
            /**
             * Register pjax for link and form
             */
            if ($.pjax) {
                $.pjax.defaults.timeout = 15000;
                $(document).on('click', 'a[data-pjax]', function(event) {
                    var container = $(this).closest('[data-pjax-container]');
                    var containerSelector = '#' + container.id
                    $.pjax.click(event, {container: containerSelector})
                });

                $(document).on('pjax:start', function (xhr, options) {
                    //self.loading.show();
                });
                $(document).on('pjax:complete', function () {
                    //self.loading.hide();
                });
                /*$(document).bind("ajaxSend", '.credglv-ajax', function(event, jqxhr, settings){
                    self.loading.show();
                }).bind("ajaxComplete", '.credglv-ajax', function(){
                    self.loading.hide();
                });*/
                $(document).on('pjax:success', function(event, data, status, xhr, options) {
                    // run "custom_success" method passed to PJAX if it exists
                    if(typeof options.after_completed === 'function'){
                        options.after_completed();
                    }
                });

                $(document).on('submit', 'form[data-pjax]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var data = $(this).serialize();
                    $('input[type=checkbox]').each(function () {
                        if (!this.checked) {
                            data += '&' + encodeURI(this.name) + '=0';

                        } else {
                            data += '&' + encodeURI(this.name) + '=1';
                        }
                    });
                    var button = $(this).find('button[type=submit]');
                    button.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled');
                    $.pjax.submit(event, $(this).data('container'), {
                        timeout: 6000,
                        push: false,
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: data
                    });
                    return false;

                });


            }
            this.select2();
            // register form credglv-ajax post ajax
            $(document).on('change', 'select[data-autosubmit]', function(){
                $(this).closest('form').submit();
            });

            $(document).on('click', 'a[data-url_key]', function (e) {
               var data = $(this).data();
               if (data.url_key) {
                   var newUrl = credglv.utils.updateQueryStringParameter(window.location.href, data.url_key, data.url_value);
                   history.pushState({}, '', newUrl);
               }
            });

            $(document).on('click', '[data-href]', function (e) {
               e.stopPropagation();
               e.preventDefault();
               var url = $(this).data('href');
               if (url) {
                   window.location.href = url;
               }
            });
            $(document).on('submit','form[credglv-ajax]',function(e){
                e.preventDefault();
                e.stopPropagation();
                var form = $(this);
                var btn = $(this).find('[type=submit]');
                var btnHtml = btn[0].outerHTML;
                var data = form.serialize();
                // effect when submit
                btn.find('.fa').attr('class','fa fa-spin fa-spinner');
                form.find(':input').prop('disabled',true);

                $.ajax({
                    type : 'POST',
                    url : credglvConfig.ajaxurl,
                    data : data
                }).success(function(res){
                    if( ( $('#'+form.data('html')).length ) ){
                        // replace html if exist element with ID data-html
                        $('#'+form.data('html')).replaceWith(res.html);
                    }else{
                        form.find(':input').prop('disabled',false);
                        btn.replaceWith(btnHtml);
                        $('.wrapper').fadeOut();
                    }
                })
            });

            //modal
            $(document).ready(function() {
                jQuery.fn.draggit = function (el) {
                    var thisdiv = this;
                    var thistarget = $(el);
                    var relX;
                    var relY;
                    var targetw = thistarget.width();
                    var targeth = thistarget.height();
                    var docw;
                    var doch;

                    thistarget.css('position','absolute');


                    thisdiv.bind('mousedown', function(e){
                        var pos = $(el).offset();
                        var srcX = pos.left;
                        var srcY = pos.top;

                        docw = $(document).width();
                        doch = $(document).height();

                        relX = e.pageX - srcX;
                        relY = e.pageY - srcY;

                        ismousedown = true;
                    });

                    $(document).bind('mousemove', function(e){ 
                        if(ismousedown)
                        {
                            targetw = thistarget.width();
                            targeth = thistarget.height();

                            var maxX = docw - targetw - 10;
                            var maxY = doch - targeth - 10;

                            var mouseX = e.pageX;
                            var mouseY = e.pageY;

                            var diffX = mouseX - relX;
                            var diffY = mouseY - relY;

                            // check if we are beyond document bounds ...
                            if(diffX < 0)   diffX = 0;
                            if(diffY < 0)   diffY = 0;
                            if(diffX > maxX) diffX = maxX;
                            if(diffY > maxY) diffY = maxY;

                            $(el).css('top', (diffY)+'px');
                            $(el).css('left', (diffX)+'px');
                        }
                    });

                    $(window).bind('mouseup', function(e){
                        ismousedown = false;
                    });

                    return this;
                } // end jQuery draggit function //

                $(".credglv-modal > div").append("<div class='credglv-modal-button'><a role=\"button\">Close</a></div>");
                $(".credglv-modal-button a").click(function(event) {
                   event.preventDefault();
                   $(this).parents('.wrapper').fadeOut();
                });

                $('.credglv-modal-container').each(function(i, e){
                   $(e).width($(window).width()).height($(window).height());
                });
                $(document).on('click','[data-modal]',function() {
                   var toload = $(this).attr("data-modal");
                    $("#"+toload).show().parent('.wrapper').fadeIn();
                });         
            });

            $(document).on('click',function(e){
                var elems = document.getElementsByClassName('wrapper');
                if($(e.target).is(".wrapper")){
                    $(".wrapper").fadeOut();
                }
            });

            $(document).on('click', '.ajax-button', function (e) {
                e.stopPropagation();
                e.preventDefault();
                var button = $(this);
                var bkText = button.html();
                $(this).html('<i class="fa fa-spin fa-spinner"></i> loading').prop('disabled', true);
                var data = button.data();
                if (data.items) {
                    $('[' + data.items + ']').each(function(i, e){
                        data[$(e).attr('name')] = $(e).val();
                    });
                }
                credglv.request.post(credglvConfig.ajaxurl, data, function(response){
                    button.prop('disabled', false).html(bkText);
                    if (response.code === 200) {
                        if (data.target) {
                            $(data.target).replaceWith(response.data);
                        }
                    }
                });
                return false;
            });

            $(document).ajaxSuccess(function() {

            });

            $(document).on('click', '[data-show]', function (e) {
                var target = $(this).data('show');
                $(target).show();
            });
            $(document).on('click', '[data-hide]', function (e) {
                var target = $(this).data('hide');
                $(target).hide();
            });
        },
        dropdownList : function() {
            var backdrop = '.dropdown-backdrop';
            var toggle = '[data-toggle="credglv-dropdown"]';
            var Dropdown = function(element) {
                $(element).on('click.bs.credglv-dropdown', this.toggle);
            };

            Dropdown.VERSION = '3.3.6';

            function getParent($this) {
                var selector = $this.attr('data-target');

                if (!selector) {
                    selector = $this.attr('href');
                    selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, ''); // strip for ie7
                }

                var $parent = selector && $(selector);

                return $parent && $parent.length ? $parent : $this.parent();
            }

            function clearMenus(e) {
                if (e && e.which === 3) return;
                $(backdrop).remove();
                $(toggle).each(function() {
                    var $this = $(this);
                    var $parent = getParent($this);
                    var relatedTarget = { relatedTarget: this };

                    if (!$parent.hasClass('open')) return;

                    if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return;

                    $parent.trigger(e = $.Event('hide.bs.credglv-dropdown', relatedTarget));

                    if (e.isDefaultPrevented()) return;

                    $this.attr('aria-expanded', 'false');
                    $parent.removeClass('open').trigger($.Event('hidden.bs.credglv-dropdown', relatedTarget));
                });
            }

            Dropdown.prototype.toggle = function(e) {
                var $this = $(this);

                if ($this.is('.disabled, :disabled')) return;

                var $parent = getParent($this);
                var isActive = $parent.hasClass('open');

                clearMenus();

                if (!isActive) {
                    if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
                        // if mobile we use a backdrop because click events don't delegate
                        $(document.createElement('div'))
                            .addClass('dropdown-backdrop')
                            .insertAfter($(this))
                            .on('click', clearMenus);
                    }

                    var relatedTarget = { relatedTarget: this };
                    $parent.trigger(e = $.Event('show.bs.credglv-dropdown', relatedTarget));

                    if (e.isDefaultPrevented()) return;

                    $this
                        .trigger('focus')
                        .attr('aria-expanded', 'true');

                    $parent
                        .toggleClass('open')
                        .trigger($.Event('shown.bs.credglv-dropdown', relatedTarget));
                }

                return false;
            };

            Dropdown.prototype.keydown = function(e) {
                if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return;

                var $this = $(this);

                e.preventDefault();
                e.stopPropagation();

                if ($this.is('.disabled, :disabled')) return;

                var $parent = getParent($this);
                var isActive = $parent.hasClass('open');

                if (!isActive && e.which != 27 || isActive && e.which == 27) {
                    if (e.which == 27) $parent.find(toggle).trigger('focus');
                    return $this.trigger('click');
                }

                var desc = ' li:not(.disabled):visible a';
                var $items = $parent.find('.dropdown-menu' + desc);

                if (!$items.length) return;

                var index = $items.index(e.target);

                if (e.which == 38 && index > 0) index--; // up
                    if (e.which == 40 && index < $items.length - 1) index++; // down
                        if (!~index) index = 0;

                $items.eq(index).trigger('focus');
            };


            // DROPDOWN PLUGIN DEFINITION
            // ==========================

            function Plugin(option) {
                return this.each(function() {
                    var $this = $(this);
                    var data = $this.data('bs.credglv-dropdown');

                    if (!data) $this.data('bs.credglv-dropdown', (data = new Dropdown(this)));
                    if (typeof option == 'string') data[option].call($this);
                });
            }

            var old = $.fn.dropdown;

            $.fn.dropdown = Plugin;
            $.fn.dropdown.Constructor = Dropdown;


            // DROPDOWN NO CONFLICT
            // ====================

            $.fn.dropdown.noConflict = function() {
                $.fn.dropdown = old;
                return this;
            };


            // APPLY TO STANDARD DROPDOWN ELEMENTS
            // ===================================

            $(document)
                .on('click.bs.credglv-dropdown.data-api', clearMenus)
                .on('click.bs.credglv-dropdown.data-api', '.credglv-dropdown form', function(e) { e.stopPropagation(); })
                .on('click.bs.credglv-dropdown.data-api', toggle, Dropdown.prototype.toggle)
                .on('keydown.bs.credglv-dropdown.data-api', toggle, Dropdown.prototype.keydown)
                .on('keydown.bs.credglv-dropdown.data-api', '.dropdown-menu', Dropdown.prototype.keydown);
        },
        modal : function(){
            /*$(".credglv-modal").wrap("<div class='wrapper'>");
            $(".credglv-modal").append("<div class='credglv-modal-button'><a href='#'>Close</a></div>");
            $(".credglv-modal-button a").click(function(event) {
               event.preventDefault();
               $(this).parents('.wrapper').fadeOut();
            });*/
        }

    };
    credglv.registerComponent('ui', Ui);
    credglv.ui = Ui;
})(jQuery, credglv);