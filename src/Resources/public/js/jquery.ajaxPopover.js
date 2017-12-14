;
(function ($) {

    $.fn.ajaxPopover = function (options) {
        options = $.extend({}, $.fn.ajaxPopover.defaults, options);
        var instance = new pluginInstance(options);
        $.fn.ajaxPopover.instance(options.instanceId, instance);
    };

    $.fn.ajaxPopover.instance = function (id, instance) {
        if (typeof(instance) == 'undefined') {
            return $(document).data('ajax-popover-' + id);
        }
        $(document).data('ajax-popover-' + id, instance);
        return this;
    };

    $.fn.ajaxPopover.defaults = {
        instanceId: 'default',
        selector: '[data-ajax-popover]',
        urlData: 'ajax-popover',
        topTreshold: 75,
        headerHeight: 0,
        placement: function (element, options) {
            var scrollTop = $('body').scrollTop();
            var offset = $(element).offset();
            var headerHeight = options.headerHeight;
            var availableSpace = offset.top - headerHeight - scrollTop;
            return availableSpace > options.topTreshold ? 'top' : 'bottom';
        },
        popover: {
            html: true,
            trigger: 'hover',
            animation: true
        },
        handlers: {
            click: null
        },

        request: {
            param: 'isAjaxPopover',
            data: 1,
            type: 'POST'
        }
    };

    $.fn.ajaxPopover.cache = {};

    var pluginInstance = function (options) {

        var instance = this;

        var body = $('body');

        var plugin = {

            bind: function () {
                body
                    .on('mouseover', options.selector, this.mouseOver)
                    .on('mouseleave', options.selector, this.mouseLeave);
                if (typeof(options.handlers.click) == 'function') {
                    body.on('click', options.selector, instance, this.click);
                }
            },

            unbind: function () {
                body
                    .off('mouseover', options.selector, this.mouseOver)
                    .off('mouseleave', options.selector, this.mouseLeave);
                //.on('mouseover', options.selector)
                //.on('mouseleave', options.selector);
                if (typeof(options.handlers.click) == 'function') {
                    body.off('click', options.selector, this.click);
                    //$('body').off('click', options.selector);
                }
            },

            mouseOver: function (event) {
                $(this).data('stop', false);
                plugin.getData(this);
            },

            mouseLeave: function (event) {
                $(this).data('stop', true);
                if ($(this).data('pop') == true) {
                    $(this).popover('hide');
                    $(this).data('pop', false);
                }
            },

            click: function () {
                options.handlers.click.apply(this, arguments);
            },

            hasCache: function (url) {
                return typeof($.fn.ajaxPopover.cache[url]) != 'undefined';
            },

            getCache: function (url) {
                return $.fn.ajaxPopover.cache[url];
            },

            setCache: function (url, data) {
                $.fn.ajaxPopover.cache[url] = data;
            },

            getData: function (element) {
                var url;
                if (typeof($(element).data(options.urlData)) != 'undefined') {
                    url = $(element).data(options.urlData);
                } else if (element.href) {
                    url = element.href;
                } else {
                    throw 'Request url not found.';
                }
                if (this.hasCache(url)) {
                    this.showPopover(this.getCache(url), element);
                    return;
                }
                var that = this;
                var requestData = {};
                requestData[options.request.param] = options.request.data;
                $.ajax({
                    type: options.request.type,
                    url: url,
                    data: requestData,
                    dataType: 'json'
                }).done(function (data) {
                    that.setCache(url, data);
                    that.showPopover(data, element);
                });
            },

            showPopover: function (data, element) {
                if ($(element).data('stop') == true) {
                    return;
                }

                var config = $.extend({}, options.popover, data, {
                    placement: function () {
                        if (typeof(options.placement) == 'function') {
                            return options.placement.call(this, element, options);
                        }
                        return options.placement;
                    }
                });

                $(element).popover(config).popover('show');
                $(element).data('pop', true);
            }

        };

        this.options = options;

        this.plugin = plugin;

        this.unbind = plugin.unbind;

        plugin.bind();
    };

})(jQuery);