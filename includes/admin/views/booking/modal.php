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
        <form name="booking-room-item" class="booking-room-item">

            <div class="header">
                <h3><?php echo __( 'Add new item', 'wp-hotel-booking' ); ?></h3>
                <span class="close dashicons dashicons-no-alt" @click="closeModal"></span>
            </div>

            <div class="main">
                <div class="room">
                    <select name="product_id" class="booking_search_room_items">
                        <option value="0"><?php _e( 'Select room' ) ?></option>
                    </select>
                </div>
                <div class="date">
                    <input type="text" name="check_in_date" class="check_in_date" value="">
                    <input type="text" name="check_out_date" class="check_out_date" value="">
                </div>
                <div class="qty">
                    <select name="qty" class="number-room">
                        <option value="0"><?php _e( 'Quantity' ) ?></option>
                    </select>
                </div>
            </div>

            <div class="footer">
                <button type="submit" class="button button-primary">
					<?php echo __( 'Add', 'wp-hotel-booking' ); ?>
                </button>
            </div>

        </form>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-modal-search', {
            template: '#tmpl-admin-booking-modal-search',
            methods: {
                closeModal: function () {
                    this.$emit('closeModal');
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>
