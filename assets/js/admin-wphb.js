(function ($, Vue, wp, wphb_extra) {

    $(document).ready(function () {

        var WPHB_Extra = new Vue({
            el: '#list-extra-services',
            data: {
                extras: wphb_extra,
                isEdit: false
            },
            methods: {
                add_extra: function () {

                },
                edit_extra: function () {
                    this.isEdit = true
                },
                delete_extra: function (id) {
                    this.isEdit = false
                }
            }
        });

    });

})(jQuery, window.Vue, window.wp, window.wphb_extra);