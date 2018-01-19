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

global $post;
?>

<script type="text/x-template" id="tmpl-admin-booking-items">

    <div id="booking-items">
        <h3><?php _e( 'Booking Items', 'wp-hotel-booking' ); ?></h3>
        <table cellpadding="0" cellspacing="0" class="booking_item_table">
            <thead>
            <tr>
                <th class="item"><?php _e( 'Item' ); ?></th>
                <th class="checkin"><?php _e( 'Check in' ); ?></th>
                <th class="checkout"><?php _e( 'Checkout' ); ?></th>
                <th class="night"><?php _e( 'Night' ); ?></th>
                <th class="qty"><?php _e( 'Quantity' ); ?></th>
                <th class="actions"></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(room, index) in rooms">
                <tr class="room-item">
                    <td>
                        <a target="_blank" v-bind:href="room.edit_link">{{room.order_item_name}}</a>
                    </td>
                    <td>{{room.check_in_date}}</td>
                    <td>{{room.check_out_date}}</td>
                    <td>{{room.night}}</td>
                    <td>{{room.qty}}</td>
                    <td class="actions">
                        <a href="#" data-booking-id="<?php echo $post->ID; ?>"
                           :data-booking-item-id="room.order_item_id"
                           data-booking-item-type="line_item" class="dashicons dashicons-edit edit"
                           title="<?php esc_attr_e( 'Edit item', 'wp-hotel-booking' ); ?>">
                        </a>
                        <a href="#" data-booking-id="<?php echo $post->ID; ?>"
                           :data-booking-item-id="room.order_item_id"
                           data-booking-item-type="line_item" class="dashicons dashicons-no-alt remove"
                           title="<?php esc_attr_e( 'Delete item', 'wp-hotel-booking' ); ?>">
                        </a>
                    </td>
                </tr>


				<?php //$extra = hotel_booking_get_product_class( hb_get_order_item_meta( $package->order_item_id, 'product_id', true ) ); ?>
                <tr :data-order-parent="room.order_item_id" v-for="(extra, index) in room.extra" class="extra-item">
                    <td></td>
                    <td colspan="3">{{extra.order_item_name}}</td>
                    <td>1

						<?php ////echo esc_html( hb_get_order_item_meta( $package->order_item_id, 'qty', true ) ); ?>
                    </td>
                    <td class="actions">
                        <a href="#" data-booking-id="<?php echo $post->ID; ?>"
                           :data-booking-item-id="extra.order_item_id"
                           data-booking-item-type="sub_item" class="dashicons dashicons-edit edit"
                           :data-booking-item-parent="extra.order_item_parent"
                           title="<?php esc_attr_e( 'Edit item', 'wp-hotel-booking' ); ?>">
                        </a>
                        <a href="#" data-booking-id="<?php echo $post->ID; ?>"
                           :data-booking-item-id="extra.order_item_id"
                           data-booking-item-type="sub_item" class="dashicons dashicons-no-alt remove"
                           :data-booking-item-parent="extra.order_item_parent"
                           title="<?php esc_attr_e( 'Delete item', 'wp-hotel-booking' ); ?>">
                        </a>
                    </td>
                </tr>
            </template>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5"><?php echo __( 'Sub Total' ); ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5"><?php echo __( 'Tax' ); ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5"><?php echo __( 'Grand Total', 'wp-hotel-booking' ); ?></td>
                <td></td>
            </tr>
            </tfoot>
        </table>

        <div class="booking-actions">
            <div class="actions">
                <a href="#" class="button"
                   id="add_room_item"><?php _e( 'Add Room Item', 'wp-hotel-booking' ); ?> </a>
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
                    console.log($store.getters['rooms']);
                    return $store.getters['rooms'];
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>