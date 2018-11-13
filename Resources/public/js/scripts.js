$(document).ready(function () {
    $(document).on('focusin', function (e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    $.fn.select2.defaults.set("theme", "bootstrap");

    $.fn.bindSelect2 = function () {
        $('.ajax-select2').each(function () {
            let _this = $(this);

            _this.select2({
                minimumInputLength: 2,
                allowClear: true,
                placeholder: _this.data('placeholder'),
                ajax: {
                    url: _this.data('href'),
                    dataType: 'json',
                    type: "PUT",
                    quietMillis: 250,
                    data: function (params) {
                        let query = {
                            q: params.term
                        };

                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data,
                        };
                    }
                    // results: function (response) {
                    //     return {
                    //         results: response
                    //     };
                    // }
                }
                // initSelection: function (item, callback) {
                //     let id = item.val();
                //     let text = item.data('option');
                //
                //     if (typeof text === 'undefined') {
                //         if (id && _this.data('href')) {
                //             $.getJSON(
                //                 _this.data('href'),
                //                 {'q': id, '_init': true},
                //                 function (response) {
                //                     let data = response[0];
                //                     callback(data);
                //                 }
                //             );
                //         }
                //     } else {
                //         let data = {id: id, text: text};
                //         callback(data);
                //     }
                // }
            });
        });

        return this;
    };

    $.fn.unbindModals = function(namespace) {
        $('[data-toggle~=modal]').off('click.' + namespace);
    };

    $.fn.bindModals = function(namespace) {
        $('[data-toggle~=modal]').on('click.' + namespace, function (e) {
            e.preventDefault();
            let _this = $(this),
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
                url += url.indexOf('#') === 0 ? '' : '?isModal=1';
            }

            if (url.indexOf('#') === 0) {
                $(url).modal('open');
            } else {
                spinner.removeClass('hide');
                $.get(url, function (response) {
                    spinner.addClass('hide');
                    let _divModal = $('<div class="modal fade"></div>'),
                        divModal = $(_divModal);

                    if (response.status === 'success') {
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
                    } else if (response.status === 'redirect') {
                        window.location.href = response.content;
                    } else {
                        modalFailureCallback(divModal, response);
                    }
                });
            }
        });
    };

    $(this)
        .bindSelect2()
        .bindModals('body');

    // Support for AJAX loaded modal window.
    // Focuses on first input textbox after it loads the window.

    //After click event is bind
    reopenForEditing();

    $(document).on('submit', '.modal_form', function () {
        let _this = $(this),
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
            let _this = $(this),
                response = _this.contents().find('body');

            if (response.length > 0) {
                try {
                    response = $.parseJSON(response.text());

                    if (response.status === 'redirect') {
                        window.location = response.content;
                    } else {
                        let content = $(response.content).find('.content');

                        if (content.length > 0) {
                            let _modal = $('.modal');
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

    let hasPopovers = false;
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

    // bindTooltips($(this));
    runHolder();
    bindCountable();
    bindXeditable($('.myeditable'));
    bindDatepickers($(".datepicker"));
    preselectToggleButtons($("[data-toggle=buttons]"));

    $(document).on('change', '.tr-search-form input, .tr-search-form select', function () {
        $(this).closest('form').submit();
    });

    // Do not allow to enter dates with keyboard
    $(document).on('keydown', '.dt-added-date-from, .dt-added-date-to', function (e) {
        e.preventDefault();
    });

    // Validates that the input string is a valid date formatted as "yyyy-mm-dd"
    $.fn.isValidDate = function (dateString) {
        // First check for the pattern
        if (!/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            return false;
        }

        // Parse the date parts to integers
        let parts = dateString.split("-");
        let year = parseInt(parts[0], 10);
        let month = parseInt(parts[1], 10);
        let day = parseInt(parts[2], 10);

        // Check the ranges of month and year
        if (year < 1000 || year > 3000 || month == 0 || month > 12) {
            return false;
        }

        let monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        // Adjust for leap years
        if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
            monthLength[1] = 29;
        }

        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    };
});

let bs_alert = function (message, type) {
    let className = 'alert-' + type;

    $('#status-messages').html('<div class="alert ' + className + ' alert-dismissable">' +
        '<a class="close" data-dismiss="alert">×</a><span>' + message + '</span>' +
        '</div>');
};

let bindCountable = function () {
    $.each($(document).find('.countable'), function (idx, el) {
        let $element = $(el),
            params = {
                counter: $element.data('counterContainer') ? $element.data('counterContainer') : '#counter',
                maxCount: $element.attr('maxlength') ? $element.attr('maxlength') : 100,
                strictMax: true
        };
        $element.simplyCountable(params);
    });
};

let reopenForEditing = function () {
    let reopenId = getUrlParameter('reopen_id');
    if (reopenId !== undefined) {
        $('.reopenable').find('tr[data-id="' + reopenId + '"] > td:eq(1)').trigger('click');
    }
};

let modalFailureCallback = function (divModal, response) {
    $(divModal).remove();
};

let modalHideCallback = function (divModal) {
    if (typeof tinymce !== 'undefined') {
        tinymce.remove();
    }

    divModal
        .unbindModals('modal')
        .remove();
};

let modalShowCallback = function (divModal) {
    divModal
        .bindSelect2()
        .bindModals('modal');

    bindXeditable(divModal.find('.myeditable'));
    bindDatepickers(divModal.find('.datepicker'));

    runHolder();
    bindCountable();

    // bindTooltips(divModal);
    preselectToggleButtons($("[data-toggle=buttons]"));
};

let bindTooltips = function ($element) {
    $element.tooltip({
        selector: "[data-toggle~=tooltip]",
        container: "body"
    });
};

let preselectToggleButtons = function ($element) {
    $element.find('input').each(function () {
        let _this = $(this);

        if (_this.is(':checked')) {
            _this.parent().addClass('active');
        }
    })
};

let postFormEmpty = function ($form, callback) {
    $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        success: function (data) {
            callback(data);
        }
    });

};

let postForm = function ($form, callback) {
    let values = {};
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

let runHolder = function () {
    if (typeof Holder !== 'undefined') {
        Holder.run();
    }
};

let bindXeditable = function ($element) {
    if ($element.length <= 0) {
        return;
    }

    $.fn.editable.defaults.mode = 'inline';

    $element.editable({
        inputclass: 'xeditable-input-mod',
        ajaxOptions: {
            type: "PUT"
        }
    });
};

let getUrlParameter = function (sParam) {
    let sPageURL = window.location.search.substring(1);
    let sURLVariables = sPageURL.split('&');
    for (let i = 0; i < sURLVariables.length; i++) {
        let sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
};

let bindDatepickers = function ($element) {
    if ($element.length <= 0) {
        return;
    }

    $element.daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        minYear: 2018,
        maxYear: parseInt(moment().format('YYYY'), 10),
        locale: {
            format: 'DD.MM.YYYY',
        },
        weekStart: 1,
        autoclose: true,
    }).on('apply.daterangepicker', function (ev, picker) {
        debugger;
        $(this).val(picker.startDate.format('DD.MM.YYYY'));
    });
};
