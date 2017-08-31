(function ($) {

    var _doc = $(document);

    // check validate integer
    function isInteger(a) {
        return Number(a) || ( a % 1 === 0 );
    }

    function timeToSecond(time) {
        var hour = time.substring(0, 2),
            session = time.slice(2),
            seconds = hour * 60 * 60 - 1;

        if (session === 'PM') {
            seconds += 12 * 60 * 60;
        }
        return seconds;
    }

    var WPHB_Flex = {
        init: function () {

            var _doc = $(document),
                _self = this;

            _self.check_in_time_picker();
            _self.check_out_time_picker();
        },
        check_in_time_picker: function () {
            var _check_in = $('input[id^="check_in_time"]'),
                _time = _check_in.siblings('input[name="hb_check_in_time"]');

            _check_in.timepicker({
                'timeFormat': 'H:i A',
                'step': '60'
            }).on('changeTime', function () {
                _time.val(timeToSecond(_check_in.val()));
            });
        },
        check_out_time_picker: function () {
            var _check_out = $('input[id^="check_out_time_"]'),
                _time = _check_out.siblings('input[name="hb_check_out_time"]');

            _check_out.timepicker({
                'timeFormat': 'H:i A',
                'step': '60'
            }).on('changeTime', function () {
                _time.val(timeToSecond(_check_out.val()));
            });
        }
    };

    function _ready() {
        WPHB_Flex.init();
    }

    _doc.ready(_ready);


})(jQuery);