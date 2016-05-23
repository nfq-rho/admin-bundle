$(document).ready(function () {
    $(document).on('focusin', function(e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    $.fn.bindSelect2 = function () {
        $('.ajax-select2').each(function () {
            var _this = $(this);

            _this.select2({
                minimumInputLength: 2,
                allowClear: true,
                placeholder: _this.data('placeholder'),
                ajax: {
                    url: _this.data('href'),
                    dataType: 'json',
                    type: "PUT",
                    quietMillis: 250,
                    data: function (term) {
                        return {
                            q: term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    }
                },
                initSelection: function (item, callback) {
                    var id = item.val();
                    var text = item.data('option');

                    if (typeof text === 'undefined') {
                        if (id && _this.data('href')) {
                            $.getJSON(
                                _this.data('href'),
                                {'q': id, '_init': true},
                                function (response) {
                                    var data = response[0];
                                    callback(data);
                                }
                            );
                        }
                    } else {
                        var data = {id: id, text: text};
                        callback(data);
                    }
                }
            });
        });

        return this;
    };

    $(this).bindSelect2();

    // Support for AJAX loaded modal window.
    // Focuses on first input textbox after it loads the window.
    $('[data-toggle~=modal]').click(function (e) {
        e.preventDefault();
        var _this = $(this),
            spinner = $('.spinner'),
            backdrop = (typeof _this.data('backdrop') === "undefined") ? 'static' : _this.data('backdrop'),
            url = (typeof _this.attr('href') === "undefined")
                ? (typeof _this.data('href') === "undefined") ? null : _this.data('href')
                : _this.attr('href');

        if (!url) {
            return false;
        }

        if (url.indexOf("?") >= 0) {
            if (url.indexOf('isModal') < 0) {
                url += '&isModal=1';
            }
        } else {
            url += '?isModal=1';
        }

        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            spinner.removeClass('hide');
            $.get(url, function (response) {
                spinner.addClass('hide');
                var _divModal = $('<div class="modal fade"></div>'),
                    divModal = $(_divModal);

                if (response.status == 'success') {
                    divModal.append(response.content);

                    divModal
                        .modal({
                            backdrop: backdrop
                        })
                        .on('shown.bs.modal', function () {
                            modalShowCallback(divModal)
                        })
                        .on('hidden.bs.modal', function () {
                            modalHideCallback(divModal);
                        });
                } else if (response.status == 'redirect') {
                    window.location.href = response.content;
                } else {
                    modalFailureCallback(divModal, response);
                }
            });
        }
    });

    //After click event is bind
    reopenForEditing();

    $(document).on('submit', '.modal_form', function () {
        var _this = $(this),
            formId = _this.attr('id'),
            formNumberParts = formId.split('_'),
            formNumber = formNumberParts[formNumberParts.length - 1],
            url = _this.attr('action');

        if (url.indexOf("?") >= 0) {
            if (url.indexOf('isModal') < 0) {
                url += '&isModal=1';
            }
        } else {
            url += '?isModal=1';
        }

        _this.attr('action', url);

        $('#modal_frame_' + formNumber).on('load', function () {
            var _this = $(this),
                response = _this.contents().find('body');

            if (response.length > 0) {
                try {
                    response = $.parseJSON(response.text());

                    if (response.status == 'redirect') {
                        window.location = response.content;
                    } else {
                        var content = $(response.content).find('.content');

                        if (content.length > 0) {
                            var _modal = $('.modal');
                            _modal.find('.content').replaceWith('<div class="content">' + content.html() + '</div>');

                            modalShowCallback(_modal);
                        }
                    }
                } catch (err) {
                    console.log(err);
                }
            }
        });
        return true;
    });

    var hasPopovers = false;
    $("[data-popup=popover]").each(function () {
        $(this).popover('show').click(function () {
            $(this).popover('disable').popover('hide');
        });

        hasPopovers = true;
    });

    if (hasPopovers) {
        window.setTimeout(function () {
            $("[data-popup=popover]").popover('hide');
        }, 10000);
    }

    bindTooltips($(this));
    xeditableBind($('.myeditable'));
    preselectToggleButtons($("[data-toggle=buttons]"));

    // Datepickers
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        autoclose: true
    });

    $('.dt-received-from').datepicker().on('changeDate', function (e) {
        $('.dt-received-to').datepicker('setStartDate', e.date);
    });

    $('.dt-checkin-from').datepicker().on('changeDate', function (e) {
        $('.dt-checkin-to').datepicker('setStartDate', e.date);
    });

    $('.dt-checkout-from').datepicker().on('changeDate', function (e) {
        $('.dt-checkout-to').datepicker('setStartDate', e.date);
    });

    $('.dt-added-date-from').datepicker().on('changeDate', function (e) {
        $('.dt-added-date-to').datepicker('setStartDate', e.date);
    });
    // end of Datepickers

    $(document).on('change', '.tr-search-form input, .tr-search-form select', function () {
        $(this).closest('form').submit();
    });

    // Do not allow to enter dates with keyboard
    $(document).on('keydown', '.dt-added-date-from, .dt-added-date-to', function (e) {
        e.preventDefault();
    });

    // Validates that the input string is a valid date formatted as "yyyy-mm-dd"
    $.fn.isValidDate = function(dateString)
    {
        // First check for the pattern
        if (!/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            return false;
        }

        // Parse the date parts to integers
        var parts = dateString.split("-");
        var year = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10);
        var day = parseInt(parts[2], 10);

        // Check the ranges of month and year
        if (year < 1000 || year > 3000 || month == 0 || month > 12) {
            return false;
        }

        var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

        // Adjust for leap years
        if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
            monthLength[1] = 29;
        }

        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    };
});

var bs_alert = function(message, type) {
    var type = 'alert-' + type;

    $('#status-messages').html('<div class="alert ' + type + ' alert-dismissable">' +
        '<a class="close" data-dismiss="alert">×</a><span>' + message + '</span>' +
    '</div>');
};

var bindCountable = function() {
    $.each($(document).find('.countable'), function (idx, el) {
        var $element = $(el),
            params = {
                counter: $element.data('counterContainer') ? $element.data('counterContainer') : '#counter',
                maxCount: $element.attr('maxlength') ? $element.attr('maxlength') : 100,
                strictMax: true
            };
        $element.simplyCountable(params);
    });
};

var reopenForEditing = function() {
    var reopenId = getUrlParameter('reopen_id');
    if (reopenId !== undefined) {
        $('.reopenable').find('tr[data-id="' + reopenId + '"] > td:eq(1)').trigger('click');
    }
};

var modalFailureCallback = function(divModal, response) {
    $(divModal).remove();
};

var modalHideCallback = function(divModal) {
    if (typeof tinymce !== 'undefined') {
        tinymce.remove();
    }

    $(divModal).remove();
};

var modalShowCallback = function(divModal) {
    divModal.bindSelect2();

    xeditableBind(divModal.find('.myeditable'));
    datepickerBind(divModal.find('.datepicker'));

    runHolder();
    bindCountable();

    bindTooltips(divModal);
    preselectToggleButtons($("[data-toggle=buttons]"));
};

var bindTooltips = function($element) {
    $element.tooltip({
        selector:  "[data-toggle~=tooltip]",
        container: "body"
    });
};

var preselectToggleButtons = function($element) {
    $element.find('input').each(function() {
        var _this = $(this);

        if (_this.is(':checked')) {
            _this.parent().addClass('active');
        }
    })
};

var postFormEmpty = function ($form, callback) {
    $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        success: function (data) {
            callback(data);
        }
    });

};

var postForm = function ($form, callback) {
    var values = {};
    $.each($form.serializeArray(), function (i, field) {
        values[field.name] = field.value;
    });

    $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: values,
        success: function (data) {
            callback(data);
        }
    });

};

var runHolder = function() {
    if (typeof Holder !== 'undefined' ) {
        Holder.run();
    }
};

var xeditableBind = function ($element) {
    $.fn.editable.defaults.mode = 'inline';

    $element.editable({
        inputclass: 'xeditable-input-mod',
        ajaxOptions: {
            type: "PUT"
        }
    });
};

var getUrlParameter = function(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
};

var datepickerBind = function ($element) {
    $element.datepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        autoclose: true
    });

    $('.dt-start-date').datepicker().on('changeDate', function (e) {
        $('.dt-end-date').datepicker('setStartDate', e.date);
    });
};