<?php

/**
 * Admin View: Booking details.
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
hb_admin_view( 'booking/items' );
?>

<script type="text/x-template" id="tmpl-admin-booking-overview">
    <div id="hb-booking-details" class="postbox ">
        <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"></span><span
                    class="toggle-indicator" aria-hidden="true"></span></button>
        <h2 class="hndle"><span><?php _e( 'Booking Details', 'wp-hotel-booking' ); ?></span></h2>
        <div class="inside">
            <div id="booking-details">
                <div class="booking-user-data">
                    <div class="user-avatar">
                        <img v-bind:src="users[customer.id].avatar" v-bind:alt="users[customer.id].email"/>
                    </div>
                    <div class="order-user-meta">
                        <div class="user-display-name">
                            <a v-bind:href="users[customer.id].link"
                               target="_blank">{{users[customer.id].display_name}}</a>
                        </div>
                        <div class="user-email">
                            {{users[customer.id].email}}
                        </div>
                    </div>
                </div>
                <div class="booking-data">
                    <h3 class="booking-data-number"><?php echo sprintf( esc_attr__( 'Booking %s', 'wp-hotel-booking' ), hb_format_order_number( $post->ID ) ); ?></h3>
                    <div class="booking-date">
						<?php echo sprintf( __( 'Date %s', 'wp-hotel-booking' ), $post->post_date ); ?>
                    </div>
                </div>
            </div>
            <wphb-booking-items></wphb-booking-items>
        </div>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-overview', {
            template: '#tmpl-admin-booking-overview',
            props: ['customer', 'users']
        });

    })(Vue, WPHB_Booking_Store);

</script>
