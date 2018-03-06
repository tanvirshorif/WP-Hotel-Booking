<?php

/**
 * Admin View: Booking room item.
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
?>

<script type="text/x-template" id="tmpl-admin-booking-room">
    <tr class="room-item" v-if="room">
        <td>
            <a target="_blank" v-bind:href="room.edit_link">{{room.order_item_name}}</a>
        </td>
        <td>{{room.check_in_date}}</td>
        <td>{{room.check_out_date}}</td>
        <td>{{room.night}}</td>
        <td>{{room.qty}}</td>
        <td class="price">{{room.price}}{{booking.currency}}</td>
        <td class="actions">
            <span class="dashicons dashicons-edit edit"
                  title="<?php esc_attr_e( 'Edit item', 'wp-hotel-booking' ); ?>" @click="openModalUpdate"></span>
            <span class="dashicons dashicons-no-alt remove"
                  title="<?php esc_attr_e( 'Delete item', 'wp-hotel-booking' ); ?>" @click="removeRoom">
            </span>
        </td>
    </tr>
</script>

<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-room', {
            template: '#tmpl-admin-booking-room',
            props: ['booking', 'room', 'index'],
            computed: {},
            methods: {
                openModalUpdate: function () {
                    this.$emit('openModalUpdate', this.room);
                },
                removeRoom: function () {
                    $store.dispatch('removeRoom', {index: this.index, booking_item_id: this.room.order_item_id});
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>