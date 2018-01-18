if (typeof wphb_admin_booking !== 'undefined') {
    /**
     * Addition package store.
     */
    (function (exports, $, Vue, Vuex, helpers, data) {

        var state = helpers.cloneObject(data.wphb_booking.item);

        state.state = 'success';
        state.countCurrentRequest = 0;

        var getters = {
            customer: function (state) {
                return state.customer;
            },
            users: function (state) {
                return state.users;
            }
        };

        var mutations = {};

        var actions = {};

        exports.WPHB_Booking_Store = new Vuex.Store({
            state: state,
            getters: getters,
            mutations: mutations,
            actions: actions
        });

    })(window, jQuery, Vue, Vuex, WPHB_Helpers, wphb_admin_booking);
}

if (typeof WPHB_Booking_Store !== 'undefined') {

    (function ($, Vue, $store) {

        $(document).ready(function () {
            window.WPHB_Booking_Editor = new Vue({
                el: '#post-body',
                template: '<wphb-booking-editor></wphb-booking-editor>'
            });
        });

    })(jQuery, Vue, WPHB_Booking_Store);

}