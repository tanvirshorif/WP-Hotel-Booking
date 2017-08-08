;(function ($) {

    var $doc = $(document);

    function parseJSON(data) {
        if (!$.isPlainObject(data)) {
            var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/);
            try {
                if (m) {
                    data = $.parseJSON(m[1]);
                } else {
                    data = $.parseJSON(data);
                }
            } catch (e) {
                data = {};
            }
        }
        return data;
    }

    function applyCoupon() {
        var $coupon = $('input[name="hb-coupon-code"]');
        var table = $coupon.parents('table');
        if (!$coupon.val()) {
            alert(hotel_booking_i18n.enter_coupon_code)
            $coupon.focus();
            return false;
        }
        $.ajax({
            type: 'POST',
            url: hotel_settings.ajax,
            data: {
                action: 'wphb_coupon_apply_coupon',
                code: $coupon.val()
            },
            dataType: 'text',
            beforeSend: function () {
                table.hb_overlay_ajax_start();
            },
            success: function (code) {
                table.hb_overlay_ajax_stop();
                try {
                    var response = parseJSON(code);
                    if (response.result === 'success') {
                        window.location.href = window.location.href;
                    } else {
                        alert(response.message);
                    }
                } catch (e) {
                    alert(e)
                }
            },
            error: function () {
                table.hb_overlay_ajax_stop();
                alert('error')
            }
        });
    }

    $doc.on('click', '#hb-apply-coupon', function () {
        applyCoupon();
    }).on('click', '#hb-remove-coupon', function (evt) {
        evt.preventDefault();
        var table = $(this).parents('table');
        $.ajax({
            url: hotel_settings.ajax,
            type: 'post',
            dataType: 'html',
            data: {
                action: 'wphb_coupon_remove_coupon'
            },
            beforeSend: function () {
                table.hb_overlay_ajax_start();
            },
            success: function (response) {
                table.hb_overlay_ajax_stop();
                response = parseJSON(response);
                if (response.result === 'success') {
                    window.location.href = window.location.href
                }
            }
        });
    });

})(jQuery);