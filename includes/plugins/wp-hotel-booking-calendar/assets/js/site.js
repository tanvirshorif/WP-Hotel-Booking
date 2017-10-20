(function ($) {

    var $doc = $(document);

    function _ready() {

        $('#calendar').fullCalendar({
            events: wphb_calendar_booking.booking
        })
    }

    $doc.ready(_ready);

})(jQuery);
