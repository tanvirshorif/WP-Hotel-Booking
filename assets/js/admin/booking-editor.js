if (typeof wphb_admin_booking !== 'undefined') {
    /**
     * Addition package store.
     */
    (function (exports, $, Vue, Vuex, helpers, data) {

        var state = helpers.cloneObject(data.booking.item);

        state.state = 'success';
        state.countCurrentRequest = 0;

        var getters = {
            id: function (state) {
                return state.booking.id;
            },
            customer: function (state) {
                return state.customer;
            },
            users: function (state) {
                return state.users;
            },
            rooms: function (state) {
                return state.rooms || [];
            },
            newItem: function (state) {
                return state.newItem;
            },
            modal: function (state) {
                return state.modal;
            },
            nonce: function (state) {
                return state.nonce;
            },
            action: function (state) {
                return state.action;
            }
        };

        var mutations = {
            'SET_NEW_ITEM': function (state, item) {
                state.newItem = item;
            },
            'SET_BOOKING_ITEMS': function (state, item) {
                state.rooms.push(item);
            },
            'REMOVE_ROOM': function (state, index) {
                state.rooms[index]['extra'].length = 0;
                state.rooms.splice(index, 1);
            },
            'REMOVE_EXTRA': function (state, room_index, extra_index) {
                state.rooms[room_index]['extra'].splice(extra_index, 1);
            },
            'SET_STATUS': function (state, status) {

            },
            'INCREASE_REQUEST': function (state) {
                state.currentRequest++;
            },
            'DECREASE_REQUEST': function (state) {
                state.currentRequest--;
            }
        };

        var actions = {

            checkAvailable: function (context, item) {
                Vue.http.WPHB_Request({
                    type: 'check-room-available',
                    item: JSON.stringify(item)
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('SET_NEW_ITEM', data);
                    }
                });
            },

            addItem: function (context, item) {
                Vue.http.WPHB_Request({
                    type: 'add-item',
                    item: JSON.stringify(item)
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('SET_BOOKING_ITEMS', data);
                    }
                });
            },

            removeRoom: function (context, payload) {
                Vue.http.WPHB_Request({
                    type: 'remove-item',
                    booking_item_id: payload.booking_item_id
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('REMOVE_ROOM', payload.index);
                    }
                });
            },

            removeExtra: function (context, payload) {
                Vue.http.WPHB_Request({
                    type: 'remove-item',
                    booking_item_id: payload.booking_item_id
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('REMOVE_EXTRA', payload.room_index, payload.extra_index);
                    }
                });
            },

            newRequest: function (context) {
                context.commit('INCREASE_REQUEST');
                context.commit('SET_STATUS', 'loading');

                window.onbeforeunload = function () {
                    return '';
                }
            },

            requestCompleted: function (context, status) {
                context.commit('DECREASE_REQUEST');

                if (context.getters.currentRequest === 0) {
                    context.commit('SET_STATUS', status);
                    window.onbeforeunload = null;
                }
            }

        };

        exports.WPHB_Booking_Store = new Vuex.Store({
            state: state,
            getters: getters,
            mutations: mutations,
            actions: actions
        });

    })(window, jQuery, Vue, Vuex, WPHB_Helpers, wphb_admin_booking);
}

if (typeof WPHB_Booking_Store !== 'undefined') {

    /**
     * HTTP
     */
    (function (exports, Vue, $store) {

        Vue.http.WPHB_Request = function (payload) {
            payload['booking_id'] = $store.getters['id'];
            payload['nonce'] = $store.getters['nonce'];
            payload['action'] = $store.getters['action'];

            return Vue.http.post(ajaxurl, payload, {
                    emulateJSON: true,
                    params: {
                        namespace: 'WPHB_Admin_Booking'
                    }
                }
            );
        };

        Vue.http.interceptors.push(function (request, next) {
            if (request.params['namespace'] !== 'WPHB_Admin_Booking') {
                next();
                return;
            }

            $store.dispatch('newRequest');

            next(function (response) {
                var body = response.body,
                    result = body.success || false;

                if (result) {
                    $store.dispatch('requestCompleted', 'success');
                } else {
                    $store.dispatch('requestCompleted', 'fail');
                }
            });
        });

    })(window, Vue, WPHB_Booking_Store);

    /**
     * Init.
     */
    (function ($, Vue, $store) {

        $(document).ready(function () {
            window.WPHB_Booking_Editor = new Vue({
                el: '#post-body',
                template: '<wphb-booking-editor></wphb-booking-editor>'
            });
        });

    })(jQuery, Vue, WPHB_Booking_Store);

}