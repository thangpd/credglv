(function ($) {


    var successClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Success';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            //position: null,
            type: "success",
            //allow_dismiss: true,
            //newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutRight'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var infoClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Info';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "info",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated bounceInDown',
                exit: 'animated bounceOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var warningClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Warning';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "warning",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var dangerClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Danger';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "danger",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated flipInY',
                exit: 'animated flipOutX'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var primaryClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Primary';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "success",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated lightSpeedIn',
                exit: 'animated lightSpeedOut'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var defaultClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Default';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "warning",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated rollIn',
                exit: 'animated rollOut'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    var linkClick = function (title, message, url, target) {
        if (title === '') {
            title = 'Link';
        }
        if (message === '') {
            message = 'Default Message';
        }
        if (url === '') {

        }
        if (target === '') {
            target = '_blank';
        }

        $.notify({
            // options
            title: '<strong>' + title + '</strong>',
            message: "<br>" + message,
            icon: 'glyphicon glyphicon-ok',
            url: url,
            target: target,
        }, {
            // settings
            element: 'body',
            position: null,
            type: "danger",
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 3300,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }
    toaster = function (status, title, message, url, target) {
        switch (status) {
            case 'success':
                successClick(title, message, url, target);

                break;
            case 'link':
                successClick(title, message, url, target);

                break;
            case 'default':
                successClick(title, message, url, target);

                break;
            case 'danger':
                dangerClick(title, message, url, target);

                break;
            case 'warning':
                warningClick(title, message, url, target);

                break;
            case 'info':
                infoClick(title, message, url, target);
                break;
        }
    };

})(jQuery);
