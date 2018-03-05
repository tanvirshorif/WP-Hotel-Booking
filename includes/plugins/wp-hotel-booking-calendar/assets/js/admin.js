(function ($) {

    var $doc = $(document);

    var WPHB_Booking_Calendar = {
        init: function () {
            var _doc = $(document),
                _self = this;

            _self.init_booking_calendar();
        },

        init_booking_calendar: function () {
            $('#calendar').fullCalendar({
                header: {
                    left: '',
                    right: ''
                },
                width: "50",
                contentHeight: 650,
                ignoreTimezone: false,
                handleWindowResize: true,
                editable: false,
                defaultView: 'month',
                events: wphb_calendar_booking.booking
            })
        }
    };

    function _ready() {
        WPHB_Booking_Calendar.init();
    }

    $doc.ready(_ready);

})(jQuery);
