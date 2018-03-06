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

<script type="text/x-template" id="tmpl-admin-booking-extra">
    <tr class="extra-item" :data-room-item="this.r_index" v-if="extra">
        <td></td>
        <td colspan="3">{{extra.order_item_name}}</td>
        <td>{{extra.qty}}</td>
        <td class="price">{{extra.price}}{{booking.currency}}</td>
        <td class="actions">
            <span class="dashicons dashicons-no-alt remove"
                  title="<?php esc_attr_e( 'Delete item', 'wp-hotel-booking' ); ?>" @click="removeExtra"></span>
        </td>
    </tr>
</script>

<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-extra', {
            template: '#tmpl-admin-booking-extra',
            props: ['booking', 'extra', 'index', 'r_index'],
            computed: {},
            methods: {
                removeExtra: function () {
                    $store.dispatch('removeExtra', {
                        room_index: this.r_index,
                        extra_index: this.index,
                        booking_item_id: this.extra.order_item_id
                    });
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>