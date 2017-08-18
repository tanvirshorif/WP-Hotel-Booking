(function ($) {

    // set default option for datepicker
    $.datepicker.setDefaults({
        dateFormat: wphb_statistic_js.date_time_format,
        monthNames: wphb_statistic_js.monthNames,
        monthNamesShort: wphb_statistic_js.monthNamesShort,
        dayNames: wphb_statistic_js.dayNames,
        dayNamesShort: wphb_statistic_js.dayNamesShort,
        dayNamesMin: wphb_statistic_js.dayNamesMin,
        maxDate: '+365D',
        numberOfMonths: 1
    });

    var _doc = $(document);

    var WPHB_Statistic_Datepicker = {
        init: function () {
            var _self = this,
                _doc = $(document);

            _self.select_date();
        },
        select_date: function () {
            $('.wphb_statistic_check_in_date').datepicker({
                onSelect: function () {
                    var _self = $(this),
                        _date = _self.datepicker('getDate'),
                        _timestamp = new Date(_date) / 1000 - ( new Date().getTimezoneOffset() * 60 ),
                        _checkout = $('.wphb_statistic_check_out_date');

                    _checkout.datepicker('option', 'minDate', _date);
                    _self.parent().find('input[name="check_in_timestamp"]').val(_timestamp);
                }
            });
            $('.wphb_statistic_check_out_date').datepicker({
                onSelect: function () {
                    var _self = $(this),
                        _date = _self.datepicker('getDate'),
                        _timestamp = new Date(_date) / 1000 - ( new Date().getTimezoneOffset() * 60 ),
                        _checkout = $('.wphb_statistic_check_in_date');

                    _checkout.datepicker('option', 'maxDate', _date);
                    _self.parent().find('input[name="check_out_timestamp"]').val(_timestamp);
                }
            });
        }

    };

    var WPHB_Canvas = {
        init: function () {
            var _self = this;

            // show canvas statistic by price
            _self.booking_price_statistic();
            // show canvas statistic by room
            _self.booking_room_statistic();
            // tokenize for select room
            _self.room_id_tokenize();
        },
        booking_price_statistic: function () {
            var ctx = document.getElementById('statistic_booking_price').getContext('2d');

            var price_statisctic = new Chart(ctx, {
                type: 'bar',
                data: $.parseJSON(wphb_statistic_price.series)
            });


        },
        booking_room_statistic: function () {
            var ctx = document.getElementById('hotel_canvas_report_room').getContext('2d');

            window.myBar = new Chart(ctx).Bar($.parseJSON(wphb_statistic_room.series), {
                responsive: true,
                scaleGridLineColor: "rgba(0,0,0,.05)"
            });
        },
        room_id_tokenize: function () {
            $('#tp-hotel-booking-room_id').tokenize();
        }
    };

    function _ready() {
        WPHB_Statistic_Datepicker.init();

        WPHB_Canvas.init();
    }

    _doc.ready(_ready);

})(jQuery);