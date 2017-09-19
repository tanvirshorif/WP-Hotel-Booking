(function ($) {

    // overlay before ajax
    $.fn.hb_overlay_ajax_start = function () {
        var _self = this;
        _self.css({
            'position': 'relative',
            'overflow': 'hidden'
        });
        var overlay = '<div class="hb_overlay_ajax">';
        overlay += '</div>';

        _self.append(overlay);
    };

    // overlay after ajax
    $.fn.hb_overlay_ajax_stop = function () {
        var _self = this;
        var overlay = _self.find('.hb_overlay_ajax');

        overlay.addClass('hide');
        var timeOut = setTimeout(function () {
            overlay.remove();
            clearTimeout(timeOut);
        }, 400);
    };

    // compare date
    if (Date.prototype.compareWith === undefined) {
        Date.prototype.compareWith = function (d) {
            if (typeof d === 'string') {
                d = new Date(d);
            }

            var thisTime = parseInt(this.getTime() / 1000),
                compareTime = parseInt(d.getTime() / 1000);
            if (thisTime > compareTime) {
                return 1;
            } else if (thisTime < compareTime) {
                return -1;
            }
            return 0;
        }
    }

    // check validate integer
    function isInteger(a) {
        return Number(a) || ( a % 1 === 0 );
    }

    // check validate email
    function isEmail(email) {
        return new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test(email);
    }

    // check validate date
    function isDate(date) {
        date = new Date(date);
        return !isNaN(date.getTime());
    }

    // parse json
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

    // rating single room
    $.fn.rating = function () {
        var _ratings = this,
            _length = _ratings.length;

        for (var i = 0; i < _length; i++) {
            var _rating = $(_ratings[i]),
                _html = [];

            _html.push('<span class="rating-input" data-rating="1"></span>');
            _html.push('<span class="rating-input" data-rating="2"></span>');
            _html.push('<span class="rating-input" data-rating="3"></span>');
            _html.push('<span class="rating-input" data-rating="4"></span>');
            _html.push('<span class="rating-input" data-rating="5"></span>');
            _html.push('<input name="rating" id="rating" type="hidden" value="" />');
            _rating.html(_html.join(''));

            _rating.mousemove(function (e) {
                e.preventDefault();
                var parentOffset = _ratings.offset(),
                    relX = e.pageX - parentOffset.left,
                    star = $(this).find('.rating-input'),
                    star_width = star.width(),
                    rate = Math.ceil(relX / star_width);

                for (var y = 0; y < star.length; y++) {
                    var st = $(star[y]),
                        _data_star = parseInt(st.attr('data-rating'));
                    if (_data_star <= rate) {
                        st.addClass('high-light');
                    }
                }
            }).mouseout(function (e) {
                var parentOffset = _ratings.offset(),
                    star = $(this).find('.rating-input'),
                    rate = $(this).find('.rating-input.selected');

                if (rate.length === 0) {
                    star.removeClass('high-light');
                } else {
                    for (var y = 0; y < star.length; y++) {
                        var st = $(star[y]),
                            _data_star = parseInt(st.attr('data-rating'));

                        if (_data_star <= parseInt(rate.attr('data-rating'))) {
                            st.addClass('high-light');
                        } else {
                            st.removeClass('high-light');
                        }
                    }
                }
            }).mousedown(function (e) {
                var parentOffset = _ratings.offset(),
                    relX = e.pageX - parentOffset.left,
                    star = $(this).find('.rating-input'),
                    star_width = star.width(),
                    rate = Math.ceil(relX / star_width);
                star.removeClass('selected').removeClass('high-light');
                for (var y = 0; y < star.length; y++) {
                    var st = $(star[y]),
                        _data_star = parseInt(st.attr('data-rating'));
                    if (_data_star === rate) {
                        st.addClass('selected').addClass('high-light');
                        break;
                    } else {
                        st.addClass('high-light');
                    }
                }
                _ratings.find('input[name="rating"]').val(rate);
            });

        }
    };

    // stripe checkout submit
    function stripeSubmit(form) {
        var pl_key = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';
        if (typeof TPBooking_Payment_Stripe !== 'undefined') {
            pl_key = TPBooking_Payment_Stripe.stripe_publish;
        }

        var handler = StripeCheckout.configure({
            key: pl_key,
            image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
            locale: 'auto',
            token: function (token) {
                // Use the token to create the charge with a server-side script.
                // You can access the token ID with `token.id`
                stripe_payment_process(form, token);
            }
        });

        var first_name = form.find('input[name="first_name"]').val().trim();
        var last_name = form.find('input[name="last_name"]').val().trim();
        var email = form.find('input[name="email"]').val().trim();
        var currency = form.find('input[name="currency"]').val().trim();
        var price = 0;
        if (form.find('input[name="pay_all"]').is(':checked')) {
            price = form.find('input[name="total_price"]').val();
        } else {
            price = form.find('input[name="total_advance"]').val();
        }

        // Open Checkout with further options
        handler.open({
            name: first_name + ' ' + last_name,
            description: email,
            currency: currency,
            amount: price * 100
        });
    }

    // stripe checkout process
    function stripe_payment_process(form, token) {
        var data = {};
        var payment_data = form.serializeArray();
        var button = form.find('button[type="submit"]');

        $.each(payment_data, function (index, obj) {
            data[obj.name] = obj.value;
        });

        $.extend(token, data);

        $.ajax({
            url: hotel_settings.ajax,
            data: token,
            type: 'POST',
            dataType: 'html',
            beforeSend: function () {
                button.addClass('hb_loading');
            }
        }).done(function (res) {
            button.removeClass('hb_loading');
            res = parseJSON(res);

            if (typeof res.result !== 'undefined' && res.result == 'success') {
                if (typeof res.redirect !== 'undefined')
                    window.location.href = res.redirect;
            } else if (typeof res.message !== 'undefined') {
                alert(res.message);
            }
        }).fail(function () {
            button.removeClass('hb_loading');
        });
    }

    // set default option for datepicker
    $.datepicker.setDefaults({
        dateFormat: wphb_js.date_time_format,
        monthNames: wphb_js.monthNames,
        monthNamesShort: wphb_js.monthNamesShort,
        dayNames: wphb_js.dayNames,
        dayNamesShort: wphb_js.dayNamesShort,
        dayNamesMin: wphb_js.dayNamesMin,
        maxDate: '+365D',
        numberOfMonths: 1
    });

    var $doc = $(document);

    var WPHB_Site = {
        init: function () {
            var _self = this,
                _doc = $(document);

            // currencies switcher
            _doc.on('change', '.hb_form_currencies_switcher_select', _self.currencies_switcher);
        },
        currencies_switcher: function (e) {
            e.preventDefault();
            var _currency = $(this).val(),
                _href = window.location.href;

            _href = _href.replace(/[\&]?currency\=[a-z]{1,3}/gi, '');

            if (_href.slice(-1) === '/') {
                _href += '?currency=' + _currency;
            } else {
                _href += '&currency=' + _currency;
            }

            // add query to set storage
            window.location = href;
        }
    };

    var WPHB_Cart = {
        init: function () {
            var _doc = $(document),
                _self = this;
            // enable add cart
            _doc.on('change', '.number_room_select', _self.enable_add_cart)
            // add to cart
                .on('submit', '.hb-search-room-results', _self.add_to_cart)
                // remove cart room item in cart
                .on('click', '.hb_remove_cart_item', _self.remove_room_cart)
                // remove cart extra item in cart
                .on('click', '.hb_package_remove', _self.remove_extra_cart)
                // remove item form mini cart
                .on('click', '.hb_mini_cart_remove', _self.remove_mini_cart_item)
            ;
        },
        enable_add_cart: function (e) {
            e.preventDefault();

            var _self = $(this),
                _button = _self.parents('.hb-room-meta').find('.hb_add_to_cart');

            if (_self.val() !== 0) {
                _button.prop('disabled', false);
            } else {
                _button.prop('disabled', true);
            }
        },
        add_to_cart: function (e) {
            e.preventDefault();

            var _form = $(this),
                _button = _form.find('.hb_add_to_cart'),
                _number_room = _form.find('.number_room_select option:selected').val(),
                _room_title = _form.find('.hb-room-name');

            if (typeof _number_room === 'undefined' || _number_room === '') {
                //message
                _room_title.find('.hb_success_message').remove();
                _room_title.append('<label class="hb_success_message">' + wphb_js.warning.room_select + '</label>');
                var timeOut = setTimeout(function () {
                    _room_title.find('.hb_success_message').remove();
                }, 2000);
                return false;
            }

            var data = $(this).serializeArray();
            $.ajax({
                url: hotel_settings.ajax,
                type: 'POST',
                data: data,
                dataType: 'html',
                beforeSend: function () {
                    // _form.hb_overlay_ajax_start();
                    _button.addClass('hb_loading');
                },
                success: function (code) {
                    _form.hb_overlay_ajax_stop();
                    code = parseJSON(code);
                    if (typeof code.message !== 'undefined') {
                        //message
                        _room_title.find('.hb_success_message').remove();
                        _room_title.append(code.message);
                        var timeOut = setTimeout(function () {
                            _room_title.find('.hb_success_message').remove();
                        }, 3000);
                    }
                    if (typeof code.status !== 'undefined' && code.status === 'success') {
                        // update woo cart when add room to cart
                        $('body').trigger('hb_added_item_to_cart');
                        // add message successfully
                        if (typeof code.redirect !== 'undefined') {
                            window.location.href = code.redirect;
                        }
                    } else {
                        alert(code.message);
                    }
                    if (typeof code.id !== 'undefined') {
                        WPHB_Cart.update_add_item_mini_cart(code);
                    }
                    _button.removeClass('hb_loading');
                    _button.after('<a href="' + wphb_js.cart_url + '" class="hb_button hb_view_cart">' + wphb_js.view_cart + '</a>');
                },
                error: function () {
                    // searchResult.hb_overlay_ajax_stop();
                    _button.removeClass('hb_loading');
                    alert(wphb_js.warning.try_again);
                }
            });
            return false;
        },
        remove_room_cart: function (e) {
            e.preventDefault();

            var _self = $(this),
                _row = _self.parents('tr'),
                _cart_id = _self.data('cart-id');

            $.ajax({
                url: hotel_settings.ajax,
                type: 'POST',
                data: {
                    cart_id: _cart_id,
                    action: 'wphb_remove_cart_item',
                    nonce: hotel_settings.nonce
                },
                dataType: 'html',
                beforeSend: function () {
                    _row.hb_overlay_ajax_start();
                }
            }).done(function (res) {
                res = parseJSON(res);
                if (typeof res.status === 'undefined' || res.status !== 'success') {
                    console.log(res.message);
                    if (res.message) {
                        alert(res.message);
                    } else {
                        alert(wphb_js.warning.try_again);
                    }
                }

                // update woo cart when remove room from cart
                $('body').trigger('hb_removed_item_to_cart');

                if (typeof res.sub_total !== 'undefined')
                    $('span.hb_sub_total_value').html(res.sub_total);

                if (typeof res.grand_total !== 'undefined')
                    $('span.hb_grand_total_value').html(res.grand_total);

                if (typeof res.advance_payment !== 'undefined')
                    $('span.hb_advance_payment_value').html(res.advance_payment);
                _row.hb_overlay_ajax_stop();
                _row.remove();
                WPHB_Cart.update_remove_item_mini_cart(_cart_id, res);
            });
        },
        remove_extra_cart: function (e) {
            e.preventDefault();
            var _self = $(this),
                _cart_id = _self.attr('data-cart-id'),
                _parents = _self.parents('.hb_mini_cart_item:first'),
                _overlay = _self.parents('.hb_mini_cart_item:first, tr');

            if (typeof _parents === 'undefined' || _parents.length === 0) {
                _parents = _self.parents('.hb_checkout_item.package:first');
            }

            $.ajax({
                url: hotel_settings.ajax,
                method: 'POST',
                data: {
                    action: 'wphb_remove_extra_cart',
                    cart_id: _cart_id
                },
                dataType: 'html',
                beforeSend: function () {
                    // ajax start effect
                    _overlay.hb_overlay_ajax_start();
                }
            }).done(function (res) {
                res = parseJSON(res);
                if (typeof res.status !== 'undefined' && res.status === 'success') {
                    WPHB_Cart.update_add_item_mini_cart(res, function () {
                        var cart_table = $('#hotel-booking-payment, #hotel-booking-cart');

                        for (var i = 0; i < cart_table.length; i++) {
                            var _table = $(cart_table[i]);
                            var tr = _table.find('table').find('.hb_checkout_item.package');
                            for (var y = 0; y < tr.length; y++) {
                                var _tr = $(tr[y]),
                                    _cart_id = _tr.attr('data-cart-id'),
                                    _cart_parent_id = _tr.attr('data-parent-id');
                                if (_cart_id === res.package_id && _cart_parent_id === res.cart_id) {
                                    var _packages = $('tr.hb_checkout_item.package[data-cart-id="' + _cart_id + '"][data-parent-id="' + _cart_parent_id + '"]'),
                                        _addition_package = $('tr.hb_addition_services_title[data-cart-id="' + _cart_parent_id + '"]'),
                                        _tr_room = $('.hb_checkout_item:not(.package)[data-cart-id="' + _cart_parent_id + '"]'),
                                        _packages_length = $('tr.hb_checkout_item.package[data-parent-id="' + _cart_parent_id + '"]').length;

                                    if (_packages_length === 1) {
                                        _tr.remove();
                                        _addition_package.remove();
                                        _tr_room.find('td:first').removeAttr('rowspan');
                                    }
                                    else {
                                        var _rowspan = _tr_room.find('td:first').attr('rowspan');
                                        _tr.remove();
                                        _tr_room.find('td:first').attr('rowspan', _rowspan - 1);
                                    }

                                    break;
                                }
                            }

                            if (typeof res.sub_total !== 'undefined')
                                _table.find('span.hb_sub_total_value').html(res.sub_total);

                            if (typeof res.grand_total !== 'undefined')
                                _table.find('span.hb_grand_total_value').html(res.grand_total);

                            if (typeof res.advance_payment !== 'undefined')
                                _table.find('span.hb_advance_payment_value').html(res.advance_payment);

                        }
                    });
                }
                // ajax stop effect
                _overlay.hb_overlay_ajax_stop();
            });
        },
        update_add_item_mini_cart: function (data, callback) {

            var _mini_cart = $('.hotel_booking_mini_cart'),
                _number_items = _mini_cart.length,
                _template = wp.template('hb-minicart-item');

            var template = _template(data);

            if (_number_items > 0) {
                for (var i = 0; i < _number_items; i++) {
                    var cart = $(_mini_cart[i]),
                        cart_item = $(_mini_cart[i]).find('.hb_mini_cart_item'),
                        insert = false,
                        empty = cart.find('.hb_mini_cart_empty'),
                        footer_ele = cart.find('.hb_mini_cart_footer'),
                        items_length = cart_item.length;

                    if (items_length === 0) {
                        var footer = wp.template('hb-minicart-footer');
                        if (empty.length === 1) {
                            empty.after(footer({}));
                            empty.before(template);
                        } else {
                            footer_ele.before(template);
                        }
                        insert = true;
                        break;
                    } else {
                        for (var y = 0; y < items_length; y++) {
                            var item = $(cart_item[y]),
                                cart_id = item.attr('data-cart-id');

                            if (data.cart_id === cart_id) {
                                item.replaceWith(template);
                                insert = true;
                                break;
                            }
                        }

                        if (insert === false) {
                            footer_ele.before(template);
                        }
                    }
                }
            }

            $('.hb_mini_cart_empty').remove();
            var timeout = setTimeout(function () {
                $('.hb_mini_cart_item').removeClass('active');
                clearTimeout(timeout);
            }, 3500);

            if (typeof callback !== 'undefined') {
                callback();
            }
        },
        update_remove_item_mini_cart: function (cart_id, res) {
            var mini_cart = $('.hotel_booking_mini_cart');
            for (var i = 0; i < mini_cart.length; i++) {
                var cart = $(mini_cart[i]);
                var items = cart.find('.hb_mini_cart_item');

                for (var y = 0; y < items.length; y++) {
                    var _item = $(items[y]),
                        _cart_item_id = _item.attr('data-cart-id');
                    if (cart_id === _cart_item_id) {
                        _item.remove();
                        break;
                    }
                }

                // append message empty cart
                items = cart.find('.hb_mini_cart_item');
                if (items.length === 0) {
                    var empty = wp.template('hb-minicart-empty');
                    cart.find('.hb_mini_cart_footer').remove();
                    cart.append(empty({}));
                    break;
                }
            }

            var cart_table = $('#hotel-booking-payment, #hotel-booking-cart');

            for (var a = 0; a < cart_table.length; a++) {
                var _table = $(cart_table[i]);
                var tr = _table.find('table').find('.hb_checkout_item, .hb_addition_services_title');
                for (var b = 0; b < tr.length; b++) {
                    var _tr = $(tr[y]),
                        cart_item_id = _tr.attr('data-cart-id'),
                        parent_item_id = _tr.attr('data-parent-id');
                    if (cart_id === cart_item_id || cart_id === parent_item_id) {
                        _tr.remove();
                    }
                }

                if (typeof res.sub_total !== 'undefined')
                    _table.find('span.hb_sub_total_value').html(res.sub_total);

                if (typeof res.grand_total !== 'undefined')
                    _table.find('span.hb_grand_total_value').html(res.grand_total);

                if (typeof res.advance_payment !== 'undefined')
                    _table.find('span.hb_advance_payment_value').html(res.advance_payment);

            }
        },
        remove_mini_cart_item: function (e) {
            e.preventDefault();

            var _self = $(this),
                _item = _self.parents('.hb_mini_cart_item'),
                _cart_id = _item.attr('data-cart-id');

            $.ajax({
                url: hotel_settings.ajax,
                type: 'POST',
                data: {
                    cart_id: _cart_id,
                    nonce: hotel_settings.nonce,
                    action: 'wphb_remove_cart_item'
                },
                dataType: 'html',
                beforeSend: function () {
                    _item.addClass('before_remove');
                    _item.hb_overlay_ajax_start();
                }
            }).done(function (res) {
                res = parseJSON(res);
                if (typeof res.status === 'undefined' || res.status !== 'success') {
                    alert(wphb_js.waring.try_again);
                    return;
                }

                WPHB_Cart.update_remove_item_mini_cart(_cart_id, res);
                _item.hb_overlay_ajax_stop();
            });
        }

    };

    var WPHB_Datepicker = {
        init: function () {

            var _self = this,
                _today = new Date(),
                _tomorrow = new Date();

            var _min = hotel_settings.min_booking_date;
            if (!isInteger(_min)) {
                _min = 1;
            }

            _tomorrow.setDate(_today.getDate() + _min);

            _self.check_in_date(_today, _min);
            _self.check_out_date(_tomorrow, _min);
        },
        check_in_date: function (_today, _min) {
            $('input[id^="check_in_date"]').datepicker({
                firstDay: wphb_js.date_start,
                minDate: _today,
                onSelect: function () {
                    var unique = $(this).attr('id');
                    unique = unique.replace('check_in_date_', '');
                    var selected = $(this).datepicker('getDate'),
                        checkout = $('#check_out_date_' + unique);

                    selected.setDate(selected.getDate() + _min);
                    checkout.datepicker('option', 'minDate', selected);
                }
            }).on('click', function () {
                $(this).datepicker('show');
            });
        },
        check_out_date: function (_tomorrow, _min) {
            $('input[id^="check_out_date"]').datepicker({
                minDate: _tomorrow,
                onSelect: function () {
                    var unique = $(this).attr('id');
                    unique = unique.replace('check_out_date_', '');
                    var selected = $(this).datepicker('getDate'),
                        check_in = $('#check_in_date_' + unique);

                    selected.setDate(selected.getDate() - _min);
                    check_in.datepicker('option', 'maxDate', selected);
                }
            }).on('click', function () {
                $(this).datepicker('show');
            })
        }
    };

    var WPHB_Search_Room = {
        init: function () {
            var _doc = $(document),
                _self = this;

            // search room
            _doc.on('submit', 'form[class^="hb-search-form"]', _self.search_room_availability)
            // toggle extra when change room number in search room page
                .on('change', '.number_room_select', _self.select_room_toggle_extra)
                // toggle extra when click toggle button
                .on('click', '.hb_package_toggle', _self.toggle_extra);
        },
        search_room_availability: function (e) {
            e.preventDefault();
            var _self = $(this),
                _unique = _self.attr('class').replace('hb-search-form-', ''),
                _button = _self.find('button[type="submit"]');

            _self.find('input, select').removeClass('error');

            var _check_in = $('#check_in_date_' + _unique),
                _check_out = $('#check_out_date_' + _unique);

            WPHB_Search_Room.validate_date(_check_in, _check_out);

            var _action = _self.attr('action') || window.location.href,
                _data = _self.serializeArray();

            for (var i = 0; i < _data.length; i++) {
                if ('check_in_date' === _data[i].name || 'check_out_date' === _data[i].name) {
                    var _date = _self.find('input[name="' + _data[i].name + '"]').datepicker('getDate'),
                        _time = new Date(_date);
                    _data.push({
                        name: 'hb_' + _data[i].name,
                        value: _time.getTime() / 1000 - (_time.getTimezoneOffset() * 60)
                    })
                }
            }

            $.ajax({
                url: hotel_settings.ajax,
                type: 'POST',
                dataType: 'html',
                data: _data,
                beforeSend: function () {
                    _button.addClass('hb_loading');
                },
                success: function (response) {
                    response = parseJSON(response);
                    if (typeof response.success === 'undefined' || !response.success) {
                        return;
                    }
                    // redirect if url is ! undefined
                    if (typeof response.url !== 'undefined') {
                        window.location.href = response.url;
                    } else if (response.sig) {
                        if (_action.indexOf('?') === -1) {
                            _action += '?hotel-booking-params=' + response.sig;
                        } else {
                            _action += '&hotel-booking-params=' + response.sig;
                        }
                        window.location.href = _action;
                    }
                }
            });
        },
        select_room_toggle_extra: function (e) {
            e.preventDefault();

            var _self = $(this),
                _form = _self.parents('.hb-search-room-results'),
                _extra_area = _form.find('.hb_addition_package_extra'),
                _toggle = _extra_area.find('.hb_addition_packages'),
                _val = _self.val();

            if (_val !== '') {
                _form.parent().siblings().find('.hb_addition_packages').removeClass('active').slideUp();
                _toggle.removeAttr('style').addClass('active');
                _extra_area.removeAttr('style').slideDown();
            }
            else {
                _extra_area.slideUp();
                _val = 1;
            }

            _form.find('.hb_optional_quantity').val(_val);
        },
        toggle_extra: function (e) {
            e.preventDefault();

            var _self = $(this),
                _parent = _self.parents('.hb_addition_package_extra'),
                _toggle = _parent.find('.hb_addition_packages');

            _self.toggleClass('active');
            _toggle.toggleClass('active');

            if (_toggle.hasClass('active')) {
                _toggle.slideDown();
            } else {
                _toggle.slideUp();
            }
        },
        validate_date: function (_check_in, _check_out) {

            if ('' === _check_in.val() || !isDate(_check_in.datepicker('getDate')) || null === _check_in.datepicker('getDate')) {
                _check_in.addClass('error');
                return false;
            }

            if ('' === _check_out.val() || !isDate(_check_out.datepicker('getDate')) || null === _check_out.datepicker('getDate')) {
                _check_out.addClass('error');
                return false;
            }

            var _check_in_date = new Date(_check_in.datepicker('getDate')),
                _check_out_date = new Date(_check_out.datepicker('getDate'));

            if (_check_in_date.compareWith(_check_out_date) >= 0) {
                _check_in.addClass('error');
                return false;
            }
        }
    };

    var WPHB_Checkout = {
        init: function () {
            var _doc = $(document),
                _self = this;

            // fetch customer exist
            _doc.on('click', '#fetch-customer-info', _self.fetch_customer_info)
            // toggle payment gateway description
                .on('click', 'input[name="hb-payment-method"]', _self.toggle_payment_description)
                // validate booking fields and process booking
                .on('submit', '#hb-payment-form', _self.checkout_booking);
        },
        fetch_customer_info: function () {
            var _button = $(this),
                _email = $('input[name="existing-customer-email"]'),
                _error_pos = $('.hb-order-existing-customer .hb-form-table');
            if (!isEmail(_email.val())) {
                _email.addClass('error').focus();
                return false;
            }

            _button.attr('disable', true);
            _email.attr('disable', true);

            var _table_info = $('.hb-col-padding.hb-col-border');
            $.ajax({
                url: hotel_settings.ajax,
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'wphb_fetch_customer_info',
                    email: _email.val()
                },
                beforeSend: function () {
                    _button.addClass('hb_loading');
                },
                success: function (response) {
                    _button.removeClass('hb_loading');
                    response = parseJSON(response);
                    if (response && response.ID) {
                        var $container = $('#hb-order-new-customer');
                        for (var key in response.data) {
                            var inputName = key.replace(/^_hb_customer_/, '');
                            var $field = $container.find('input[name="' + inputName + '"], select[name="' + inputName + '"], textarea[name="' + inputName + '"]');
                            $field.val(response.data[key]);
                        }
                        $container.find('input[name="existing-customer-id"]').val(response.ID);
                        $('.hb-order-existing-customer').fadeOut();
                    } else {
                        WPHB_Checkout.fetch_info_error([wphb_js.no_customer_exist], _error_pos);
                    }
                    _button.removeAttr('disabled');
                    _email.removeAttr('disabled');

                },
                error: function () {
                    _table_info.hb_overlay_ajax_stop();
                    WPHB_Checkout.fetch_info_error([wphb_js.ajax_error], _error_pos);
                    _button.removeAttr('disabled');
                    _email.removeAttr('disabled');
                }
            });
        },
        toggle_payment_description: function () {
            var _self = this;

            if (_self.checked) {
                $('.hb-payment-method-form:not(.' + this.value + ')').slideUp();
                $('.hb-payment-method-form.' + this.value + '').slideDown();
            }
        },
        checkout_booking: function (e) {
            e.preventDefault();
            var _self = $(this),
                _action = window.location.href.replace(/\?.*/, '');

            _self.find('.hotel_checkout_errors').slideUp().remove();
            _self.find('input, select').parents('div:first-child').removeClass('error');

            try {
                if (_self.triggerHandler('hb_order_submit') === false || !WPHB_Checkout.validate_booking(_self)) {
                    return false;
                }

                _self.attr('action', _action);

                WPHB_Checkout.submit_booking(_self);

            } catch (e) {
                alert(e);
            }
        },
        validate_booking: function (_form) {

            var _email = _form.find('input[name="email"]'),
                _tos = _form.find('input[name="tos"]'),
                _msgs = [];

            if (_tos.length && !_tos.is(':checked')) {
                _msgs.push(wphb_js.confirm_tos);
                _tos.addClass('error');
            }
            if ($('input[name="existing-customer-id"]').val()) {
                if (_email.val() !== $('input[name="existing-customer-email"]', _form).val()) {
                    _msgs.push(wphb_js.customer_email_not_match);
                }
                _email.parents('div:first').addClass('error');
                _form.find('input[name="existing-customer-id"]').parents('div:first').addClass('error');
            }

            if (_msgs.length > 0) {
                WPHB_Checkout.fetch_info_error(_msgs);
                return false;
            }
            return true;
        },
        submit_booking: function (_form) {
            var _action = window.location.href.replace(/\?.*/, ''),
                _button = _form.find('button[type="submit"]');

            _form.attr('action', _action);

            $.ajax({
                type: 'POST',
                url: hotel_settings.ajax,
                data: _form.serialize(),
                dataType: 'text',
                beforeSend: function () {
                    _button.addClass('hb_loading');
                },
                success: function (code) {
                    _button.removeClass('hb_loading');
                    try {
                        var response = parseJSON(code);
                        if (response.result === 'success') {
                            if (response.redirect !== undefined) {
                                window.location.href = response.redirect;
                            }
                        } else if (typeof response.message !== 'undefined') {
                            alert(response.message);
                        }
                    } catch (e) {
                        alert(e)
                    }
                },
                error: function () {
                    _button.removeClass('hb_loading');
                    WPHB_Checkout.fetch_info_error([wphb_js.warning.try_again]);
                }

            });

            return false;
        },
        fetch_info_error: function (msgs, pos) {
            if (msgs.length === 0) {
                return;
            }
            $('.hotel_checkout_errors').slideUp().remove();
            var html = [];

            html.push('<div class="hotel_checkout_errors">');
            for (var i = 0; i < msgs.length; i++) {
                html.push('<p>' + msgs[i] + '</p>');
            }
            html.push('</div>');

            pos.after(html.join(''));
        }

    };

    var WPHB_Room = {
        init: function () {
            var _doc = $(document),
                _self = this;

            // toggle room price breakdown
            _doc.on('click', '.hb-view-booking-room-details, .hb_search_room_item_detail_price_close', _self.toggle_price_breakdown)
            // comment for single room
                .on('submit', '#commentform', _self.comment_room);

            // gallery images in single room page
            _self.single_room_gallery();
            // tabs in single room
            _self.room_tabs();
            // rating for single room
            _self.rating_room();


        },
        toggle_price_breakdown: function (e) {
            e.preventDefault();
            var _self = $(this),
                _details = _self.parents('.hb-room-content').find('.hb-booking-room-details');

            _details.toggleClass('active');
        },
        room_tabs: function () {
            var _single = $('.hb_single_room_details'),
                _tabs = _single.find('.hb_single_room_tabs'),
                _contents = _single.find('.hb_single_room_tabs_content'),
                _tab_details = $('.hb_single_room_tab_details'),
                _current_uri = window.location.href,
                _commentID = _current_uri.match(/\#comment-[0-9]+/gi);

            if (_commentID && typeof _commentID[0] !== 'undefined') {
                _tabs.find('a').removeClass('active');
                _tabs.find('a[href="#hb_room_reviews"]').addClass('active');
            } else {
                _tabs.find('a:first').addClass('active');
                $('.hb_single_room_tabs_content .hb_single_room_tab_details:not(:first)').hide();
            }

            _tab_details.hide();
            var _active = _tabs.find('a.active').attr('href');
            _contents.find(_active).fadeIn();

            _tabs.find('a').on('click', function (event) {
                event.preventDefault();
                _tabs.find('a').removeClass('active');
                $(this).addClass('active');
                var tab_id = $(this).attr('href');
                _tab_details.hide();
                _contents.find(tab_id).fadeIn();
                return false;
            });
        },
        comment_room: function () {
            var _self = this,
                _rate = $('#rating'),
                _val = _rate.val();
            if (_rate.length === 1 && typeof _val !== 'undefined' && _val === '') {
                window.alert(wphb_js.review_rating_required);
                return false;
            }
            _self.submit();
        },
        single_room_gallery: function () {
            $('.hb_room_gallery').camera({height: '470px', loader: 'none', pagination: false, thumbnails: true});
        },
        rating_room: function () {
            $('.hb-rating-input').rating();
        }
    };

    function _ready() {
        WPHB_Site.init();

        WPHB_Cart.init();

        WPHB_Datepicker.init();

        WPHB_Search_Room.init();

        WPHB_Checkout.init();

        WPHB_Room.init();
    }

    $doc.ready(_ready);

})(jQuery);