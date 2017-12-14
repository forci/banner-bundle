;
(function ($) {

    var handlers = {
        reload: function (seconds) {
            if (seconds) {
                setTimeout(function () {
                    window.location.reload();
                }, seconds * 1000);
                return;
            }
            window.location.reload();
        },
        redirect: function (data) {
            if ('string' == $.type(data)) {
                window.location.href = data;
                return;
            }
            if (data.delay) {
                setTimeout(function () {
                    window.location.href = data.url;
                }, data.delay * 1000);
                return;
            }
            window.location.href = data.url;
        },
        replace: function (data, element) {
            var $parent = $(element).parent();
            $(element).after(data).remove();
            $parent.trigger('replaced');
        },
        refresh: function (data, element) {
            var refreshTarget = $(element).data('refresh-target');
            var target;
            if ($(refreshTarget).length) {
                target = $(refreshTarget);
            } else {
                target = $(element).parents('[data-refresh]');
            }
            target.trigger('refresh', {
                element: element
            });
        },
        remove: function (data, element) {
            $(element).parents('[data-refresh]').remove();
        },
        append: function (html, element) {
            $(element).append(html);
        },
        prepend: function (html, element) {
            $(element).prepend(html);
        },
        injectHtml: function (html) {
            $('body').append(html);
        },
        witter: function (data) {
            $.witter(data);
        },
        mfp: function (html) {
            if ($.magnificPopup.instance) {
                $.magnificPopup.instance.close();
            }
            $.magnificPopup.open({
                items: {
                    src: html, // can be a HTML string, jQuery object, or CSS selector
                    type: 'inline'
                }
            });
        },
        bootbox: function (data) {
            bootbox.alert(data);
        }
    };

    var addHandler = function (name, handler) {
        handlers[name] = handler;
    };

    var removeHandler = function (name) {
        delete handlers[name];
    };

    var hasHandler = function (name) {
        return typeof(handlers[name]) == 'function';
    };

    var getHandler = function (name) {
        return handlers[name];
    };

    var handle = function (data, element, customHandlers) {
        customHandlers = customHandlers || {};
        $.each(data, function (name, value) {
            var handler;
            if (typeof(customHandlers[name]) == 'function') {
                handler = customHandlers[name];
                handler(value, element);
            } else if (hasHandler(name)) {
                handler = getHandler(name);
                handler(value, element);
            }
        });
    };

    $.extend({
        ajaxHandler: {
            handle: handle,
            addHandler: addHandler,
            removeHandler: removeHandler,
            hasHandler: hasHandler,
            getHandler: getHandler
        }
    });

})(jQuery);