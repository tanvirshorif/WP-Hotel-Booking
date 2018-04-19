(function ($) {

    var _doc = $(document);

    var WPHB_Canvas = {
        init: function () {
            var _self = this;

            // show canvas statistic by price
            _self.booking_price_statistic();
        },
        booking_price_statistic: function () {
            var ctx = document.getElementById('booking_statistic_chart').getContext('2d');

            var price_statisctic = new Chart(ctx, {
                type: 'bar',
                data: $.parseJSON(wphb_statistic_price.series)
            });
        }
    };

    function _ready() {
        WPHB_Canvas.init();
    }

    _doc.ready(_ready);

})(jQuery);