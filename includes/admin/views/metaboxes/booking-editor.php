<?php
/**
 * Admin View: Booking editor page.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

hb_admin_view( 'booking/overview' );
hb_admin_view( 'booking/customer' );
hb_admin_view( 'booking/actions' );
?>

<script type="text/x-template" id="tmpl-admin-booking-editor">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="postbox-container-1" class="postbox-container">
            <wphb-booking-actions :customer="customer" :users="users"></wphb-booking-actions>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <wphb-booking-overview :customer="customer" :users="users"></wphb-booking-overview>
            <wphb-booking-customer></wphb-booking-customer>
        </div>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-editor', {
            template: '#tmpl-admin-booking-editor',
            computed: {
                customer: function () {
                    return $store.getters['customer'];
                },
                users: function () {
                    return $store.getters['users'];
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>