<?php

/**
 * Admin View: Booking items.
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

hb_admin_view( 'booking/loop/room' );
hb_admin_view( 'booking/loop/extra' );
?>

<script type="text/x-template" id="tmpl-admin-booking-items">

    <div id="booking-items" class="inside">
        <h3><?php _e( 'Booking Items', 'wp-hotel-booking' ); ?></h3>
        <table cellpadding="0" cellspacing="0" class="booking_item_table">

            <thead>
            <tr>
                <th class="item"><?php _e( 'Item' ); ?></th>
                <th class="checkin"><?php _e( 'Check in' ); ?></th>
                <th class="checkout"><?php _e( 'Checkout' ); ?></th>
                <th class="night"><?php _e( 'Night' ); ?></th>
                <th class="qty"><?php _e( 'Quantity' ); ?></th>
                <th class="price"><?php _e( 'Price' ); ?></th>
                <th class="actions"></th>
            </tr>
            </thead>
            <tbody>
            <!-- init component -->
            <wphb-booking-room></wphb-booking-room>
            <wphb-booking-extra></wphb-booking-extra>

            <!-- loop -->
            <template v-for="(room, r_index) in rooms">
                <wphb-booking-room :booking="booking" :room="room" :index="r_index"
                                   @openModalUpdate="openModalUpdate"></wphb-booking-room>
                <template v-for="(extra, e_index) in room.extra">
                    <wphb-booking-extra :booking="booking" :r_index="r_index" :extra="extra"
                                        :index="e_index"></wphb-booking-extra>
                </template>
            </template>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="5"><?php echo __( 'Sub Total' ); ?></td>
                <td>{{booking.sub_total}}{{booking.currency}}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5"><?php echo __( 'Tax' ); ?></td>
                <td>{{booking.tax}}{{booking.currency}}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5"><?php echo __( 'Grand Total', 'wp-hotel-booking' ); ?></td>
                <td>{{booking.total}}{{booking.currency}}</td>
                <td></td>
            </tr>
            </tfoot>
        </table>

        <div class="booking-actions">
            <div class="actions">
                <span class="button" id="add_room_item"
                      @click="openModalAdd"><?php _e( 'Add New Item', 'wp-hotel-booking' ); ?> </span>
            </div>
        </div>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-items', {
            template: '#tmpl-admin-booking-items',
            computed: {
                rooms: function () {
                    return $store.getters['rooms'];
                },
                booking: function () {
                    return $store.getters['booking'];
                }
            },
            methods: {
                openModalUpdate: function (room) {
                    this.$emit('openModal', room);
                },
                openModalAdd: function () {
                    this.$emit('openModal', false);
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>