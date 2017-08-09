(function ($) {

    var _doc = $(document);

    // check validate integer
    function isInteger(a) {
        return Number(a) || ( a % 1 === 0 );
    }

    var WPHB_Flex = {
        init: function () {

            var _doc = $(document),
                _self = this,
                _today = new Date(),
                _tomorrow = new Date();

            var _min = hotel_settings.min_booking_date;
            if (!isInteger(_min)) {
                _min = 1;
            }

            _tomorrow.setDate(_today.getDate() + _min);

            _self.datetime_picker_check_in(_today);
            _self.datetime_picker_check_out(_tomorrow);
        },
        datetime_picker_check_in: function (_today) {
            var _check_in = $('input[id^="check_in_date"]');

            _check_in.datepicker('destroy');

            _check_in.datetimepicker({
                minDate: _today,
                dateFormat: wphb_js.date_time_format,
                defaultTime: '00:00'
            });
        },
        datetime_picker_check_out: function (_tomorrow) {
            var _check_out = $('input[id^="check_out_date"]');

            _check_out.datepicker('destroy');

            _check_out.datetimepicker({
                minDate: _tomorrow,
                dateFormat: wphb_js.date_time_format,
                defaultTime: '00:00'
            });
        }
    };

    function _ready() {
        WPHB_Flex.init();
    }

    _doc.ready(_ready);


})(jQuery);