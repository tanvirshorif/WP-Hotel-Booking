(function ($) {

    var $doc = $(document);

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

            _self.select_booking_customer();

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
                    dateFormat: wphb_admin_js.date_time_format,
                    monthNames: wphb_admin_js.monthNames,
                    monthNamesShort: wphb_admin_js.monthNamesShort,
                    dayNames: wphb_admin_js.dayNames,
                    dayNamesShort: wphb_admin_js.dayNamesShort,
                    dayNamesMin: wphb_admin_js.dayNamesMin,
                    onSelect: function () {
                        var _self = $(this),
                            date = _self.datepicker('getDate'),
                            timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                        _self.parent().find('input[name="check_in_date_timestamp"]').val(timestamp);

                        _check_out.datepicker('option', 'minDate', date);
                    }
                });
                _check_out.datepicker({
                    dateFormat: wphb_admin_js.date_time_format,
                    monthNames: wphb_admin_js.monthNames,
                    monthNamesShort: wphb_admin_js.monthNamesShort,
                    dayNames: wphb_admin_js.dayNames,
                    dayNamesShort: wphb_admin_js.dayNamesShort,
                    dayNamesMin: wphb_admin_js.dayNamesMin,
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
        }
    };

    function _ready() {
        WPHB_Admin_Booking.init();
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