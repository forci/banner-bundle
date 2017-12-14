/*
 * Witter for jQuery
 *
 * Copyright (c) 2014 Martin Kirilov
 * Dual licensed under the MIT and GPL licenses.
 *
 * A Gritter for jQuery ripoff - http://www.boedesign.com/
 *
 * Copyright (c) 2012 Jordan Boesch
 * Dual licensed under the MIT and GPL licenses.
 */
;(function ($) {

    $.witter = function (options) {
        try {
            return new Witter(options || {});
        } catch (e) {
            var err = 'Witter Error: ' + e;
            (typeof(console) != 'undefined' && console.error) ? console.error(err, options) : alert(err);
        }
    };

    $.witter.defaults = {
        title: '',
        position: 'top-right',
        theme: 'dark',
        fade: {
            in: {
                speed: 'medium',
                easing: ''
            },
            out: {
                speed: 1000,
                easing: ''
            }
        },
        close_selector: '.witter-close',
        time: 6000,
        image: '',
        sticky: false,
        restore: true,
        callbacks: {
            before_open: function () {
                //
            },
            after_open: function () {
                //
            },
            /**
             *
             * @param options - An object of options passed to the fade method
             * @param is_forced - Indicates whether it was closed by clicking on the close button
             */
            fade: function (is_forced, options) {
                //
            },
            before_close: function () {
                //
            },
            after_close: function () {
                //
            },
            html: function () {
                //
            }
        },
        templates: {
            close: '<a class="witter-close" href="#" tabindex="1"><i class="fa fa-times"></i></a>',
            simple: '<div class="witter-item-simple">{close}<div class="image">{image}</div><div class="witter-title">{title}</div><p class="witter-text">{text}</p></div>',
            item: '<div id="witter-item-{id}" class="witter-item-wrapper {theme} html" style="display:none" role="alert"><div class="witter-item">{html}<div style="clear:both"></div></div></div>',
            wrapper: '<div id="witter-wrappers"></div>'
        }
    };

    $.witter.parse = function (template, data) {
        return template.replace(/\{([\w\.]*)\}/g, function (str, key) {
            var keys = key.split("."), v = data[keys.shift()];
            for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
            return (typeof v !== "undefined" && v !== null) ? v : "";
        });
    };

    $.witter.registry = {
        count: 0,
        instances: {},
        active: [],
        /**
         * Increments instances count, then uses it as the instance ID and returns it
         * @param instance
         * @returns number
         */
        add: function (instance) {
            var id = ++this.count;
            this.instances[id] = instance;
            this.setActive(id);
            return id;
        },
        get: function (instance) {
            if (instance instanceof Witter) {
                return instance;
            }
            return this.instances[instance];
        },
        setActive: function (id) {
            this.active.push(id);
        },
        setInactive: function (id) {
            this.active = $.grep(this.active, function (value) {
                return value != id;
            });
        },
        getActiveIds: function () {
            return this.active;
        }
    };

    /**
     * Remove a witter notification from the screen
     */
    $.witter.remove = function (instance, options) {
        $.witter.registry.get(instance).remove(options);
    };

    /**
     * Remove a witter notification from the screen instantly
     */
    $.witter.removeNow = function (instance) {
        $.witter.registry.get(instance).removeElement();
    };

    /**
     * Remove all notifications
     */
    $.witter.removeAll = function (options) {
        var ids = $.witter.registry.getActiveIds();
        $.each(ids, function (index, instanceId) {
            $.witter.registry.get(instanceId).fade(true, options);
        });
    };

    /**
     * Remove all notifications instantly
     */
    $.witter.removeAllNow = function () {
        var ids = $.witter.registry.getActiveIds();
        $.each(ids, function (index, instanceId) {
            $.witter.registry.get(instanceId).removeElement();
        });
    };

    $(function () {
        $.witter.wrappers = $('#witter-wrappers');
        if ($.witter.wrappers.length == 0) {
            $('body').append($.witter.defaults.templates.wrapper);
            $.witter.wrappers = $($.witter.wrappers.selector);
            $(['top-right', 'bottom-right', 'bottom-left', 'top-left', 'top', 'bottom']).each(function (index, className) {
                var div = $('<div/>').addClass('wrapper').addClass(className).appendTo($.witter.wrappers);
            });
        }
    });

    var Witter = function (options) {
        if (!(this instanceof arguments.callee)) {
            return new arguments.callee(options);
        }

        if (typeof(options) == 'string') {
            options = {text: options};
        }

        if (options.text === null) {
            throw 'You must supply "text" parameter.';
        }

        options = $.extend(true, {}, $.witter.defaults, options);

        this.options = options;

        var instance = this;

        var id = $.witter.registry.add(this);

        this.id = id;

        this.callbacks = options.callbacks;

        var html;

        if (options.html) {
            html = options.html;
        } else {
            var image_str = options.image ? '<img src="' + options.image + '" class="witter-image" />' : '';

            html = $.witter.parse(options.templates.simple, {
                close: options.templates.close,
                image: image_str,
                title: options.title,
                text: options.text
            });
        }

        var itemTemplate = $.witter.parse(options.templates.item, {
            html: html,
            id: id,
            theme: options.theme
        });

        if (this.callbacks.before_open.apply(this) === false) {
            // if 'before_open' callback returns false - do not show at all
            return this;
        }

        $.witter.wrappers.find('.' + options.position).append(itemTemplate);

        this.element = $('#witter-item-' + id);

        this.element.fadeIn({
            duration: options.fade.in.speed,
            easing: options.fade.in.easing,
            complete: function () {
                instance.callbacks.after_open.apply(instance);
            }
        });

        if (!options.sticky) {
            this.setFadeTimer();
        }

        $(this.element).on('mouseenter', function () {
            if (!options.sticky && options.restore) {
                instance.restoreItemIfFading();
            }
            $(this).addClass('hover');
        }).on('mouseleave', function () {
            if (!options.sticky && options.restore) {
                instance.setFadeTimer(instance, options);
            }
            $(this).removeClass('hover');
        });

        $(this.element).on('click', options.close_selector, function (event) {
            event.preventDefault();
            instance.fade(options, true);
        });
    };

    Witter.prototype.title = function (title) {
        if (!title) {
            return this.element.find('.witter-item-simple .witter-title').text();
        }

        this.element.find('.witter-item-simple .witter-title').text(title);

        return this;
    };

    Witter.prototype.text = function (text) {
        if (!text) {
            return this.element.find('.witter-item-simple .witter-text').text();
        }

        this.element.find('.witter-item-simple .witter-text').text(text);

        return this;
    };

    Witter.prototype.html = function (html) {
        if (!html) {
            return this.element.find('.witter-item').html();
        }

        var callback = this.callbacks.html.apply(this, [html]);

        if (false === callback) {
            // if 'html' callback returns false, do not change html
            return;
        }

        if (callback) {
            html = callback;
        }

        this.element.find('.witter-item').html(html);


        return this;
    };

    Witter.prototype.remove = function (options) {
        this.fade(true, options);

        return this;
    };

    /**
     * Set the notification to fade out after a certain amount of time
     */
    Witter.prototype.setFadeTimer = function () {
        var that = this;
        this.fadeTimer = setTimeout(function () {
            that.fade();
        }, this.options.time);

        return this;
    };

    /**
     * Fade out an element after it's been on the screen for x amount of time
     * @param params
     * @param isForced
     */
    Witter.prototype.fade = function (isForced, params) {
        var opts = $.extend(true, {}, this.options, params || {});

        this.callbacks.fade.apply(this, [isForced, opts]);

        if (isForced) {
            this.element.off('mouseenter mouseleave');
            clearTimeout(this.fadeTimer);
        }

        var instance = this;
        if (opts.fade.out.speed) {
            this.element.animate({
                opacity: 0
            }, opts.fade.out.speed, function () {
                $(this).slideUp(300, function () {
                    instance.removeElement();
                });
            });
            return;
        }

        this.removeElement();

        return this;
    };

    Witter.prototype.removeElement = function () {
        this.callbacks.before_close.apply(this);
        $(this.element).remove();
        $.witter.registry.setInactive(this.id);
        this.callbacks.after_close.apply(this);

        return this;
    };

    Witter.prototype.restoreItemIfFading = function () {
        clearTimeout(this.fadeTimer);
        this.element.stop().css({opacity: '', height: ''});

        return this;
    };

})(jQuery);