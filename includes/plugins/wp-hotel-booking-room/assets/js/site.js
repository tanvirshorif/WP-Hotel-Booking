(function ($) {

    function isInteger(a) {
        return Number(a) || ( a % 1 === 0 );
    }

    // set default option for datepicker
    $.datepicker.setDefaults({
        dateFormat: wphb_room_js.date_time_format,
        monthNames: wphb_room_js.monthNames,
        monthNamesShort: wphb_room_js.monthNamesShort,
        dayNames: wphb_room_js.dayNames,
        dayNamesShort: wphb_room_js.dayNamesShort,
        dayNamesMin: wphb_room_js.dayNamesMin,
        maxDate: '+365D',
        numberOfMonths: 1
    });

    var _doc = $(document);

    var WPHB_Booking_Room = {
        init: function () {
            var _self = this,
                _doc = $(document);

            // load booking form
            _doc.on('click', '#check_availability_room', _self.load_booking_form)
            // trigger lightbox open
                .on('booking_room_lightbox_init', _self.lightbox_init)
                // check room available
                .on('click', '.hb_button.check_available', _self.check_room_available)
        },
        load_booking_form: function (e) {
            e.preventDefault();

            var _self = $(this),
                _doc = $(document),
                _room_id = _self.attr('data-id'),
                _room_name = _self.attr('data-name'),
                _target = 'hb-room-load-form',
                _lightbox = '#single_booking_room_lightbox';

            $(_lightbox).html(wp.template(_target)({_room_id: _room_id, _room_name: _room_name}));
            $.magnificPopup.open({
                type: 'inline',
                items: {
                    src: '#single_booking_room_lightbox'
                },
                callbacks: {
                    open: function () {
                        _doc.triggerHandler('booking_room_lightbox_init', [_self, _lightbox, _target]);
                    }
                }
            });
            return false;
        },
        lightbox_init: function (e, button, lightbox, taget) {
            e.preventDefault();
            // search form
            if (taget === 'hb-room-load-form') {
                WPHB_Booking_Room.datepicker_init()
            }
        },
        check_room_available: function (e) {
            e.preventDefault();

            var _form = $('form[name="hb-search-single-room"]'),
                _button = _form.find('.hb_button.check_available'),
                _data = WPHB_Booking_Room.form_data(),
                _container = _form.find('.hb-search-results-form-container'),
                _check_in_text = _form.find('input[name="check_in_date_text"]'),
                _check_out_text = _form.find('input[name="check_out_date_text"]');

            var sanitize = WPHB_Booking_Room.sanitize();

            if (sanitize === false) {
                return false;
            }

            _data['action'] = 'wphb_room_check_single_room_available';

            $.ajax({
                url: hotel_settings.ajax,
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                type: 'POST',
                data: _data,
                dataType: 'json',
                beforeSend: function () {
                    _button.addClass('hb_loading');
                }
            }).done(function (res) {
                _button.removeClass('hb_loading');
                if (typeof res.status === 'undefined') {
                    return;
                }
                if (res.status === false && typeof res.messages !== 'undefined') {
                    WPHB_Booking_Room.append_messages(_form, res.messages);
                } else if (typeof res.qty !== 'undefined') {
                    _check_in_text.val(res.check_in_date_text);
                    _check_out_text.val(res.check_out_date_text);
                    if (res.qty) {
                        _container.append(wp.template('hb-room-load-form-cart')(res));
                    }
                }
            }).fail(function () {
                _button.removeClass('hb_loading');
            });
        },
        datepicker_init: function () {
            var _doc = $(document),
                _check_in = $('.hb-search-results-form-container input[name="check_in_date"]'),
                _check_out = $('.hb-search-results-form-container input[name="check_out_date"]'),
                _today = new Date(),
                _tomorrow = new Date();

            var _min = hotel_settings.min_booking_date;
            if (!isInteger(_min)) {
                _min = 1;
            }
            _tomorrow.setDate(_today.getDate() + _min);

            _check_in.datepicker({
                minDate: _today,
                onSelect: function (selected) {
                    var _checkout_date = _check_in.datepicker('getDate');

                    _checkout_date.setDate(_checkout_date.getDate() + _min);
                    _check_out.datepicker('option', 'minDate', _checkout_date);
                }
            });

            _check_out.datepicker({
                onSelect: function (selected) {
                    var _check_in_date = _check_out.datepicker('getDate');

                    _check_in_date.setDate(_check_in_date.getDate() - _min);
                    _check_in.datepicker('option', 'maxDate', _check_in_date);
                }
            });

            _doc.triggerHandler('hotel_booking_room_form_datepicker_init', _check_in, _check_out);
        },
        form_data: function () {
            var data = {},
                _form = $('.hotel-booking-single-room-action'),
                _data = _form.serializeArray();

            var _data_length = Object.keys(_data).length;
            for (var i = 0; i < _data_length; i++) {
                var _input = _data[i];
                if (_input.name === 'check_in_date' || _input.name === 'check_out_date') {
                    var _timestamp = _form.find('input[name="' + _input.name + '"]');
                    _timestamp = $(_timestamp).datepicker('getDate');
                    _timestamp = new Date(_timestamp);
                    _timestamp = _timestamp.getTime() / 1000 - ( _timestamp.getTimezoneOffset() * 60 );

                    data[_input.name + '_timestamp'] = _timestamp;
                }
                data[_input.name] = _input.value;
            }

            return data;
        },
        sanitize: function () {
            var _form = $('form[name="hb-search-single-room"]'),
                _check_in = _form.find('input[name="check_in_date"]'),
                _check_out = _form.find('input[name="check_out_date"]'),
                _errors = [];

            if (_check_in.datepicker('getDate') === null) {
                _check_in.addClass('error');
                _errors.push('<p>' + wphb_room_js.empty_check_in_date + '</p>');
            }

            if (_check_out.datepicker('getDate') === null) {
                _check_out.addClass('error');
                _errors.push('<p>' + wphb_room_js.empty_check_out_date + '</p>');
            }

            if (_errors.length > 0) {
                WPHB_Booking_Room.append_messages(_form, _errors);
                return false;
            } else {
                WPHB_Booking_Room.append_messages(_form);
            }

            return true;
        },
        append_messages: function (_form, _errors) {
            if (typeof _form !== 'undefined') {
                _form.find('.hotel_booking_room_errors').slideUp(300, function () {
                    $(this).remove();
                });
                _form.find('.error').removeClass('error');
            }

            if (typeof _form === 'undefined' || typeof _errors === 'undefined' || Object.keys(_errors).length === 0) {
                return;
            }

            var _mesg = [];

            for (var i = 0; i < Object.keys(_errors).length; i++) {
                _mesg[i] = '<p>' + _errors[i] + '</p>';
            }

            _form.find('.hb-booking-room-form-header').append('<div class="hotel_booking_room_errors">' + _errors.join('') + '</div>');
        }

    };

    function _ready() {
        WPHB_Booking_Room.init();
    }

    _doc.ready(_ready);

})(jQuery);