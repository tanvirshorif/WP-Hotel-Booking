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
        },
        booking_price_statistic: function () {
            var _doc = $(document),
                _ctx = _doc.getElementById('hotel_canvas_report_price').getContext('2d');

            // window.myLine = new Chart(ctx).Line( <?php echo json_encode( $hb_report->series() ) ?>, {
            //     responsive: true
            // });
        }
    };

    function _ready() {
        WPHB_Statistic_Datepicker.init();

        WPHB_Canvas.init();
    }

    _doc.ready(_ready);

})(jQuery);