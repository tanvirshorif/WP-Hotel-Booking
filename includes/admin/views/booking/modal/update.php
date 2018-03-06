<?php

/**
 * Admin View: Modal update booking item.
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

<script type="text/x-template" id="tmpl-admin-booking-modal-update">

    <form name="booking-room-item" class="booking-room-item" @submit.prevent="" v-if="item">

        <div class="header">
            <h3><?php _e( 'Update item', 'wp-hotel-booking' ); ?></h3>
            <span class="close dashicons dashicons-no-alt" @click="closeModal"></span>
        </div>

        <div class="main">
            <div class="room-item">
                <div class="heading">
                    <div class="room"><?php _e( 'Room Name', 'wp-hotel-booking' ); ?></div>
                    <div class="checkin"><?php _e( 'Check in', 'wp-hotel-booking' ); ?></div>
                    <div class="checkout"><?php _e( 'Check out', 'wp-hotel-booking' ); ?></div>
                    <div class="qty"><?php _e( 'Quantity', 'wp-hotel-booking' ); ?></div>
                </div>
                <div class="content">
                    <div class="room">
                        <select name="product_id" class="select-item" v-model="item.id" @change="checkAvailable"
                                disabled="disabled">
                            <option v-bind:value="item.id">{{item.order_item_name}}</option>
                        </select>
                    </div>
                    <div class="checkin">
                        <input type="text" name="check_in_date" class="check_in_date" v-model="item.check_in_date"
                               @change="checkAvailable">
                    </div>
                    <div class="checkout">
                        <input type="text" name="check_out_date" class="check_out_date" v-model="item.check_out_date"
                               @change="checkAvailable">
                    </div>
                    <div class="qty">
                        <select name="qty" class="number-room" v-model="item.qty">
                            <option v-bind:value="item.qty">{{item.qty}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="extra-item" v-if="item.all_extra">
                <div class="heading">
                    <div class="extra"><?php _e( 'Extra Packages', 'wp-hotel-booking' ); ?></div>
                    <div class="type"><?php _e( 'Type', 'wp-hotel-booking' ); ?></div>
                    <div class="qty"><?php _e( 'Quantity', 'wp-hotel-booking' ); ?></div>
                </div>
                <div class="content" v-for="(extra, index) in item.all_extra">
                    <div class="extra">
                        <input type="checkbox" checked/>{{extra.title}}
                    </div>
                    <div class="type">{{extra.respondent}}</div>
                    <div class="qty"><input type="number" v-model="extra.qty"/></div>
                </div>
            </div>
        </div>

        <div class="footer">
            <button type="submit" class="button button-primary" :disabled="!addable"
                    @click.prevent.submit="updateItem">
				<?php echo __( 'Update', 'wp-hotel-booking' ); ?>
            </button>
        </div>
    </form>

</script>

<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-modal-update', {
            template: '#tmpl-admin-booking-modal-update',
            props: ['item'],
            computed: {
                // item valid to add to booking
                addable: function () {
                    return this.item.id && this.item.check_in && this.item.check_out && this.item.qty;
                },
                listExtra: function () {
                    var extra = this.item.extra,
                        list = [];

                    extra.each(function (_extra) {
                        list[] = [_extra['id']];
                    });

                    return list;
                }
            },
            methods: {
                updateItem: function () {

                },
                closeModal: function () {
                    this.$emit('closeModal');
                },
                checkAvailable: function () {
                    if (this.item.id && this.item.check_in && this.item.check_out) {
                        $store.dispatch('checkAvailable', this.item);
                    }
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>