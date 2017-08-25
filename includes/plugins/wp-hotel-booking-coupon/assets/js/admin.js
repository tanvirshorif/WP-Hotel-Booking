;(function ($) {

    var _doc = $(document);

    WPHB_Coupon_Admin_Booking = {

        init: function () {
            var _doc = $(document),
                _self = this;

            // add coupon
            _doc.on('click', '#add_coupon', _self.add_coupon)
            //remove coupon
                .on('click', '#remove_coupon', _self.remove_coupon)
                // on open trigger
                .on('wphb_modal_open', this.openCallback)
            ;
        },

        add_coupon: function (e, target, data) {
            e.preventDefault();
            var _self = $(this),
                _order_id = _self.attr('data-order-id');

            _self.wphb_modal({
                tmpl: 'hb-coupons',
                settings: {
                    order_id: _order_id
                }
            });

            return false;
        },


        remove_coupon: function (e, target, data) {
            e.preventDefault();
            var _self = $(this),
                _order_id = _self.attr('data-order-id'),
                _coupon_id = _self.attr('data-coupon-id');
            $(this).hb_modal_box({
                tmpl: 'hb-confirm',
                settings: {
                    order_id: _order_id,
                    coupon_id: _coupon_id,
                    action: 'wphb_coupon_remove_booking_coupon'
                }
            });

            return false;
        },

        openCallback: function (e, target, form) {
            if (target === 'hb-coupons') {
                var _select = form.find('.booking_coupon_code');
                // select2
                _select.select2({
                    placeholder: wphb_coupon.select_coupon,
                    minimumInputLength: 3,
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        type: 'POST',
                        quietMillis: 50,
                        data: function (coupon) {
                            return {
                                coupon: coupon.term,
                                action: 'wphb_coupon_load_coupon_ajax',
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

            }
        }

    };

    function _ready() {
        WPHB_Coupon_Admin_Booking.init();
    }

    _doc.ready(_ready);

})(jQuery);