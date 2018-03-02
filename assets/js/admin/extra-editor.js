if (typeof wphb_addition_packages !== 'undefined') {
    /**
     * Addition package store.
     */
    (function (exports, $, Vue, Vuex, helpers, data) {

        var state = helpers.cloneObject(data.wphb_extra);

        state.state = 'success';
        state.countCurrentRequest = 0;

        var getters = {
            extra: function (state) {
                return state.extra;
            },
            unit: function (state) {
                return state.unit;
            },
            types: function (state) {
                return state.types;
            },
            action: function (state) {
                return state.action;
            },
            nonce: function (state) {
                return state.nonce;
            },
            addable: function () {
                return true;
            },
            currentRequest: function (state) {
                return state.countCurrentRequest || 0;
            }
        };

        var mutations = {
            'SET_LIST_EXTRA': function (state, extra) {
                state.extra = extra;
            },
            'ADD_EXTRA': function (state, extra) {
                state.extra.push(extra);
            },
            'DELETE_EXTRA': function (state, index) {
                state.extra.splice(index, 1);
            },
            'SET_ADDABLE_NEW': function (state, addable) {
                state.addable = addable;
            },
            'PROCESSING_EXTRA': function (state, id) {
                $('.extra-item.item-id-' + id).css('opacity', '0.5');
            },
            'COMPLETED_EXTRA': function (state, id) {
                $('.extra-item.item-id-' + id).css('opacity', '1');
            },
            'PROCESSING_LIST_EXTRA': function (state) {
                $('.extra-item').css('opacity', '0.5');
            },
            'COMPLETED_LIST_EXTRA': function (state) {
                $('.extra-item').css('opacity', '1');
            },
            'UPDATE_ITEM_STATUS': function (item_id, status) {

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
            newExtra: function (context, extra) {
                // disable add new
                context.commit('SET_ADDABLE_NEW', false);

                Vue.http.WPHB_Request({
                    type: 'new-extra',
                    extra: JSON.stringify(extra)
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        extra['id'] = data;
                        context.commit('ADD_EXTRA', extra);
                        context.commit('SET_ADDABLE_NEW', true);
                    }
                });
            },

            updateExtra: function (context, extra) {
                context.commit('PROCESSING_EXTRA', extra.id);
                Vue.http.WPHB_Request({
                    type: 'update-extra',
                    extra: JSON.stringify(extra)
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('COMPLETED_EXTRA', extra.id);
                    }
                });
            },

            deleteExtra: function (context, payload) {
                var _id = payload[0]['extra_id'];
                context.commit('PROCESSING_EXTRA', _id);

                Vue.http.WPHB_Request({
                    type: 'delete-extra',
                    extra_id: _id
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('DELETE_EXTRA', payload[0]['index']);
                    }
                });
            },

            updateListExtra: function (context, listExtra) {
                context.commit('PROCESSING_LIST_EXTRA');

                Vue.http.WPHB_Request({
                    type: 'update-list-extra',
                    listExtra: JSON.stringify(listExtra)
                }).then(function (response) {
                    var result = response.body,
                        data = result.data;
                    if (data) {
                        context.commit('SET_LIST_EXTRA', listExtra);
                        context.commit('COMPLETED_LIST_EXTRA');
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

        exports.WPHB_Extra_Store = new Vuex.Store({
            state: state,
            getters: getters,
            mutations: mutations,
            actions: actions
        });

    })(window, jQuery, Vue, Vuex, WPHB_Helpers, wphb_addition_packages);
}

if (typeof WPHB_Extra_Store !== 'undefined') {
    /**
     * HTTP
     */
    (function (exports, Vue, $store) {

        Vue.http.WPHB_Request = function (payload) {
            payload['nonce'] = $store.getters['nonce'];
            payload['action'] = $store.getters['action'];

            return Vue.http.post(ajaxurl, payload, {
                    emulateJSON: true,
                    params: {
                        namespace: 'WPHB_Extra_Panel'
                    }
                }
            );
        };

        Vue.http.interceptors.push(function (request, next) {
            if (request.params['namespace'] !== 'WPHB_Extra_Panel') {
                next();
                return;
            }

            $store.dispatch('newRequest');

            next(function (response) {
                var body = response.body,
                    result = body.success || false;

                if (result) {
                    $store.dispatch('requestCompleted', 'success');
                    if (body.data.message) {
                        alert(body.data.message);
                    }
                } else {
                    $store.dispatch('requestCompleted', 'fail');
                }
            });
        });

    })(window, Vue, WPHB_Extra_Store);


    /**
     * Init.
     */
    (function ($, Vue, $store) {

        $(document).ready(function () {
            window.WPHB_Extra_Panel = new Vue({
                el: '#wphb-admin-extra-panel',
                template: '<wphb-extra-panel></wphb-extra-panel>'
            });
        })

    })(jQuery, Vue, WPHB_Extra_Store);
}