<?php

/**
 * Admin View: Modal search booking item.
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

global $post;
$booking = WPHB_Booking::instance( $post->ID );
?>

<script type="text/x-template" id="tmpl-admin-booking-modal-search">

    <div id="booking-modal-search">
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-modal-search', {
            template: '#tmpl-admin-booking-modal-search'
        });

    })(Vue, WPHB_Booking_Store);

</script>
