$(function () {
    $(document).tooltip({
        trigger: 'hover',
        selector: '[rel*="tooltip"]',
        container: 'body'
    });
    $(document).popover({
        trigger: 'hover',
        selector: '[rel*="popover"]',
        container: 'body'
    });
    $(document).on('click', '[data-trigger="filter"]', function(event) {
        var element = $(this);
        var field = element.data('filter-field');
        var value = element.data('filter-value');
        $('#'+field).val(value).trigger('change');
    });
    $(document).on('refresh', '[data-refresh]', function (e, params) {
        params = params || {};
        if (params.element) {
            $(params.element).tooltip('hide');
            $(params.element).popover('hide');
        }
        $(this).block({
            message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>',
            element: $(this)
        });
        var that = this, action = $(this).data('refresh');
        $.ajax({
            url: action,
            type: 'GET'
        }).done(function (html) {
            $(that).unblock();
            $(that).html(html);
            $(that).trigger('refreshed');
        });
    });
    $(document).on('click', '[data-trigger="refresh"]', function (e) {
        $(this).parents('[data-refresh]').trigger('refresh', {
            element: $(this)
        });
    });
    $(document).on('ajax-action', 'a', function (e) {
        e.preventDefault();
        if ($(this).data('disabled')) {
            return;
        }
        $(this).data('disabled', true);
        var that = this;
        var action = $(this).attr('href');
        var html = $(this).html();
        $(this).find('i').remove();
        $(this).prepend('<i class="fa fa-spinner fa-pulse"></i>');
        $.ajax({
            url: action,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            $(that).data('disabled', false);
            $(that).html(html);
            $.ajaxHandler.handle(data, that);
        });
        return false;
    });
    $(document).on('ajax-action', 'form', function (e) {
        e.preventDefault();
        if ($(this).data('disabled')) {
            return;
        }
        $(this).data('disabled', true);
        $(this).block();
        var that = this;
        var action = $(this).attr('action');
        var method = $(this).attr('method');
        var button = $(this).find('button[type="submit"]:focus');
        var html = button.html();
        $(this).data('clicked-btn-html', html);
        button.find('i').remove();
        button.prepend('<i class="fa fa-spinner fa-pulse"></i>');
        $.ajax({
            url: action,
            type: method,
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function (data) {
            $(that).data('disabled', false);
            var html = $(that).data('clicked-btn-html');
            $(button).html(html).data('original-html', true);
            $(that).unblock(that);
            $.ajaxHandler.handle(data, that);
        });
        return false;
    });
    $(document).on('click', 'a.ajax-action, [data-ajax-action="true"]', function (e) {
        e.preventDefault();
        $(this).trigger('ajax-action');
    });
    $(document).on('submit', 'form.ajax-action, [data-ajax-action="true"]', function (e) {
        e.preventDefault();
        $(this).trigger('ajax-action');
    });
    $(document).on('click', 'a[data-mfp]', function (e) {
        e.preventDefault();
        $(this).magnificPopup({
            type: 'ajax',
            // alignTop: true,
            overflowY: 'scroll', // as we know that popup content is tall we set scroll overflow by default to avoid jump
            focus: 'input, select, textarea'
        }).magnificPopup('open');
    });
    jQuery(document).on('submit', 'form[data-mfp], .mfp-content form', function () {
        var container = $(this).parents('.mfp-content'), action = $(this).attr('action');
        $(this).trigger('before_submit');
        $.ajax({
            url: action,
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize()
        }).done(function (data) {
            $.magnificPopup.instance.close();
            $.ajaxHandler.handle(data, container);
        });
        container.html('<div class="container"><div style="text-align: center;"><i class="fa fa-spinner fa-spin fa-spin-fast" style="font-size: 32px;"></i></div></div>');
        return false;
    });
    $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Close (Esc)',
        tLoading: '<div class="container"><div style="text-align: center;"><i class="fa fa-spinner fa-spin fa-spin-fast" style="font-size: 32px;"></i></div></div>'
    });
    $(document).on('remove', '[data-refresh]', function (e) {
        $(this).slideUp(250, function () {
            var afterRemove = function () {
            };
            if (typeof($(this).data('after-remove')) == 'function') {
                afterRemove = $(this).data('after-remove');
            }
            $(this).remove();
            afterRemove();
        });
    });
    $(document).on('click', '[data-confirm]', function (e) {
        var msg = $(this).data('confirm');
        if (!$(this).data('confirmed')) {
            e.preventDefault();
            var that = $(this);
            bootbox.confirm({
                title: 'Please confirm action',
                message: msg,
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn btn-default'
                    },
                    confirm: {
                        label: 'Confirm',
                        className: 'btn btn-primary'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax({
                            url: that.attr('href'),
                            type: 'POST',
                            data: {
                                is_confirmed: 1
                            },
                            dataType: 'json'
                        }).done(function (data) {
                            that.trigger('confirmed', data);
                            $.ajaxHandler.handle(data, that);
                        });
                        that.data('confirmed', true);
                    }
                }
            });
        }
    });
    $(document).on('change', 'form.autosubmit input, form.autosubmit select', function (event) {
        $(this).parents('form').submit();
    });
    $('textarea.autosize').autosize();
    $('select.select2').select2();
});