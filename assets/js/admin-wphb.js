(function ($, Vue, wp, wphb_admin) {

    // create the media frame
    var select_image_frame = wp.media({
        // set the title of the modal
        title: wphb_admin.choose_images,
        // the the modal show only image files
        library: {
            type: 'application/image',
            multiple: true
        }
    });

    $(document).ready(function () {
        select_image_frame.on('select', function () {
            var attachment = select_image_frame.state().get('selection');
            console.log(attachment);
        });

        var WPHB_Admin = new Vue({
            el: '.attachment.add-new',
            data: {
                message: 'yyy'
            }
        });
    });

})(jQuery, window.Vue, window.wp, window.hotel_booking_i18n);