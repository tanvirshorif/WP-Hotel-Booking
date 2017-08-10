(function ($) {

    var $doc = $(document);

    // set default option for datepicker
    $.datepicker.setDefaults({
        dateFormat: wphb_admin_js.date_time_format,
        monthNames: wphb_admin_js.monthNames,
        monthNamesShort: wphb_admin_js.monthNamesShort,
        dayNames: wphb_admin_js.dayNames,
        dayNamesShort: wphb_admin_js.dayNamesShort,
        dayNamesMin: wphb_admin_js.dayNamesMin
    });

    var WPHB_Admin_Booking = {
        init: function () {
            var _doc = $(document),
                _self = this;

            // add room item for booking
            _doc.on('click', '#add_room_item', _self.add_room_item)
            // edit booking room item
                .on('click', '#booking-items .actions .edit', _self.edit_booking_room)
                // delete booking room item
                .on('click', '#booking-items .actions .remove', _self.delete_booking_room)
                // check room available
                .on('wphb_check_room_available', _self.check_room_available)
                // handle modal open
                .on('wphb_modal_open', _self.modal_open_callback)
                // save add booking room item
                .on('wphb_submit_modal', _self.save_item);

            // select customer in admin booking
            _self.select_booking_customer();

            // datepicker for filter booking by date
            _self.booking_date_filter();

        },
        add_room_item: function (e) {
            e.preventDefault();
            var _self = $(this),
                _booking_id = _self.data('booking-id');
            _self.wphb_modal({
                tmpl: 'hb-add-room',
                settings: {
                    'order_id': _booking_id
                }
            });
            return false;
        },
        edit_booking_room: function (e) {
            e.preventDefault();
            var _self = $(this),
                _booking_id = _self.data('booking-id'),
                _booking_item_id = _self.data('booking-item-id'),
                _booking_item_type = _self.data('booking-item-type'),
                _icon = _self.find('.fa');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    booking_id: _booking_id,
                    booking_item_id: _booking_item_id,
                    booking_item_type: _booking_item_type,
                    action: 'wphb_admin_load_booking_item',
                    nonce: hotel_settings.nonce
                },
                beforeSend: function () {
                    _icon.addClass('fa-spin');
                }
            }).done(function (response) {
                _icon.removeClass('fa-spin');
                _self.wphb_modal({
                    tmpl: 'hb-add-room',
                    settings: response
                })
            });
        },
        delete_booking_room: function (e) {
            e.preventDefault();
            var _self = $(this),
                _booking_id = _self.data('booking-id'),
                _booking_item_id = _self.data('booking-item-id');

            _self.wphb_modal({
                tmpl: 'hb-confirm',
                settings: {
                    booking_id: _booking_id,
                    booking_item_id: _booking_item_id,
                    action: 'wphb_admin_remove_booking_item'
                }
            })

        },
        check_room_available: function (e, target, form) {
            e.preventDefault();
            e.stopPropagation();

            var _self = $(this),
                _button = $('.form_footer .check_available');
            form.push({
                name: 'action',
                value: 'wphb_admin_check_room_available'
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: form,
                beforeSend: function () {
                    _button.append('<i class="fa fa-spinner fa-spin"></i>');
                    $('select[name="qty"]').remove();
                }
            }).done(function (res) {
                _button.find('.fa').remove();
                if (typeof res.status === 'undefined') {
                    return;
                }
                if (res.status === false && typeof res.message !== 'undefined') {
                    alert(res.message);
                    return;
                }
                $('#hb_modal_dialog .section:last-child').append(wp.template('hb-qty')(res));
            });
        },
        modal_open_callback: function (e, target, form) {
            e.preventDefault();
            if (target === 'hb-add-room') {
                var _check_in = form.find('.check_in_date'),
                    _check_out = form.find('.check_out_date'),
                    _select = form.find('.booking_search_room_items');

                // select2
                _select.select2({
                    placeholder: wphb_admin_js.select_room,
                    minimumInputLength: 3,
                    // z-index: 10000,
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        type: 'POST',
                        quietMillis: 50,
                        data: function (room) {
                            return {
                                room: room.term,
                                action: 'wphb_load_room_ajax',
                                nonce: hotel_settings.nonce
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.post_title,
                                        id: item.ID
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                });

                // date picker
                _check_in.datepicker({
                    onSelect: function () {
                        var _self = $(this),
                            date = _self.datepicker('getDate'),
                            timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                        _self.parent().find('input[name="check_in_date_timestamp"]').val(timestamp);

                        _check_out.datepicker('option', 'minDate', date);
                    }
                });
                _check_out.datepicker({
                    onSelect: function () {
                        var _self = $(this),
                            date = _self.datepicker('getDate'),
                            timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                        _self.parent().find('input[name="check_out_date_timestamp"]').val(timestamp);

                        _check_in.datepicker('option', 'maxDate', date);
                    }
                });

            }
        },
        save_item: function (e, target, form) {
            var _form = $('#booking-details'),
                _overlay = _form.find('.modal_overlay');

            form.push({
                name: 'action',
                value: 'wphb_admin_add_booking_item'
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: form,
                beforeSend: function () {
                    _overlay.addClass('active');
                }
            }).done(function (response) {
                _overlay.removeClass('active');
                if (typeof response.status !== 'undefined') {
                    if (true === response.status) {
                        _form.html(response.html);
                    } else if (typeof  response.message !== 'undefined') {
                        alert(response.message);
                    }
                }
            });
        },
        select_booking_customer: function () {
            $('#_hb_user_id').select2({
                placeholder: wphb_admin_js.select_booking_customer,
                minimumInputLength: 3,
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    type: 'POST',
                    quietMillis: 50,
                    data: function (user_name) {
                        return {
                            user_name: user_name.term,
                            action: 'wphb_load_booking_user',
                            nonce: hotel_settings.nonce
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.user_login + '(#' + item.ID + ' ' + item.user_email + ')',
                                    id: item.ID
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        },
        booking_date_filter: function () {
            $('#hb-booking-date-from').datepicker({
                onSelect: function () {
                    var _self = $(this),
                        date = _self.datepicker('getDate'),
                        timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                    _self.parent().find('input[name="date-from-timestamp"]').val(timestamp);
                    $('#hb-booking-date-to').datepicker('option', 'minDate', date)
                }
            });
            $('#hb-booking-date-to').datepicker({
                onSelect: function () {
                    var _self = $(this),
                        date = _self.datepicker('getDate'),
                        timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                    _self.parent().find('input[name="date-to-timestamp"]').val(timestamp);
                    $('#hb-booking-date-from').datepicker('option', 'maxDate', date)
                }
            });
            $('form#posts-filter').submit(function () {
                var counter = 0;
                $('#hb-booking-date-from, #hb-booking-date-to, select[name="filter-type"]').each(function () {
                    if ($(this).val()) counter++;
                });
                if (counter > 0 && counter < 3) {
                    alert(wphb_admin_js.filter_error);
                    return false;
                }
            });
        }
    };

    var WPHB_Admin_Pricing_Plan = {
        init: function () {
            var _self = this,
                _doc = $(document);

            // show room pricing plan
            _doc.on('change', '#hb-room-select', _self.show_room_pricing)
            // add new plan
                .on('click', '.add_new_plan', _self.add_new_plan)
                // remove plan
                .on('click', '.hb-pricing-controls a', _self.remove_plan);

        },
        show_room_pricing: function (e) {
            e.preventDefault();
            var _self = this,
                _location = window.location.href;
            _location = _location.replace(/[&]?hb-room=[0-9]+/, '');
            if (_self.value !== 0) {
                _location += '&hb-room=' + _self.value;
            }
            window.location.href = _location;
        },
        add_new_plan: function (e) {
            e.preventDefault();
            var _self = this,
                _button = $('.add_new_plan'),
                _table = _button.parent().siblings('.hb-pricing-table'),
                _cloned = $(wp.template('hb-pricing-table')()),
                _inputs = _cloned.find('.hb-pricing-price');

            WPHB_Admin_Pricing_Plan.init_pricing_plan(_cloned);
            _table.find('.hb-pricing-price').each(function (i) {
                _inputs.eq(i).val(_self.value);
            });
            if (_table.hasClass('regular-price')) {
                _cloned.removeClass('regular-price');
                $('.hb-pricing-table-title > span', _cloned).html(wphb_admin_js.date_range);
                $('#hb-pricing-plan-list').append(_cloned);
            } else {
                _cloned.insertAfter(_table);
            }
            $('#hb-no-plan-message').hide();
        },
        remove_plan: function (e) {
            e.preventDefault();
            var _self = this,
                _table = _self.closest('.hb-pricing-table');

            if (confirm(wphb_admin_js.confirm_remove_pricing_table)) {
                if (_table.siblings('.hb-pricing-table').length === 0) {
                    $('#hb-no-plan-message').show();
                }
            }
        },
        init_pricing_plan: function (_plan) {
            _plan.find('.datepicker').datepicker({
                onSelect: function () {
                    var _self = $(this),
                        _date = _self.datepicker('getDate'),
                        _timestamp = new Date(_date).getTime() / 1000 - ( new Date(_date).getTimezoneOffset() * 60 ),
                        _name = _self.attr('name');
                    var _hidden_name = false;
                    if (_name.indexOf('date-start') === 0) {
                        _hidden_name = name.replace('date-start', 'date-start-timestamp');
                    } else if (_name.indexOf('date-end') === 0) {
                        _hidden_name = _name.replace('date-end', 'date-end-timestamp');
                    }
                    if (_hidden_name) {
                        _plan.find('input[name="' + _hidden_name + '"]').val(_timestamp);
                    }
                }
            });
            // $(plan).find('.datepicker').datepicker('disable');
        }
    };

    function _ready() {
        WPHB_Admin_Booking.init();

        WPHB_Admin_Pricing_Plan.init();
    }

    $doc.ready(_ready);

})(jQuery);

/**
 * Create admin modal
 */
(function ($, Backbone, _) {

    var _doc = $(document);

    $.fn.wphb_modal = function (options) {
        var modal_options = $.extend({}, {
            tmpl: '',
            settings: {}
        }, options);

        if (modal_options.tmpl) {
            WPHB_Modal.view(modal_options.tmpl, modal_options.settings);
        }
    };

    var WPHB_Modal = {
        view: function (target, options) {
            var view = Backbone.View.extend({
                id: 'hb_modal_dialog',
                options: options,
                target: target,
                events: {
                    'click .modal_close': 'close_modal',
                    'click .modal_overlay': 'close_modal',
                    'click .form_submit': 'submit_modal',
                    'click .check_room_available': 'check_room_available'
                },
                // construct function
                initialize: function (data) {
                    this.render();
                },
                render: function () {
                    var _template = wp.template(this.target);

                    _template = _template(this.options);

                    $('body').append(this.$el.html(_template));

                    var _content = $('.hb_modal'),
                        _width = _content.outerWidth(),
                        _height = _content.outerHeight();

                    _content.css({'margin-top': '-' + _height / 2 + 'px', 'margin-left': '-' + _width / 2 + 'px'});

                    _doc.trigger('wphb_modal_open', [this.target, _content.find('form')]);
                },
                submit_modal: function () {
                    _doc.trigger('wphb_submit_modal', [this.target, this.modal_data()]);

                    this.close_modal();

                    return false;
                },
                close_modal: function () {
                    _doc.trigger('wphb_close_modal', [this.target, this.modal_data()]);

                    this.$el.remove();

                    return false;
                },
                check_room_available: function () {
                    _doc.trigger('wphb_check_room_available', [this.target, this.modal_data()]);

                    return false;
                },
                modal_data: function () {
                    return $(this.$el).find('form:first-child').serializeArray();
                }
            });

            return new view(options);
        }
    }
})(jQuery, Backbone, _);