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
        <form name="booking-room-item" class="booking-room-item" @submit.prevent="">

            <div class="header">
                <h3><?php echo __( 'Add new item', 'wp-hotel-booking' ); ?></h3>
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
                            <select name="product_id" class="select-item" v-model="item.id" @change="checkAvailable">
                                <option value="0"><?php _e( 'Select room' ) ?></option>
								<?php
								$rooms = WPHB_Room_CURD::get_rooms();
								if ( is_array( $rooms ) ) {
									foreach ( $rooms as $room ) { ?>
                                        <option value="<?php echo esc_attr( $room->ID ); ?>"><?php echo esc_html( $room->post_title ); ?></option>
									<?php }
								} ?>
                            </select>
                        </div>
                        <div class="checkin">
                            <input type="text" name="check_in_date" class="check_in_date" v-model="item.check_in"
                                   @change="checkAvailable">
                        </div>
                        <div class="checkout">
                            <input type="text" name="check_out_date" class="check_out_date" v-model="item.check_out"
                                   @change="checkAvailable">
                        </div>
                        <div class="qty">
                            <select :disabled="!item.available" name="qty" class="number-room" v-model="item.qty">
                                <option value="0">0</option>
                                <option v-for="n in item.available" v-bind:value="n">{{n}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="extra-item" v-if="item.qty && item.extra">
                    <div class="heading">
                        <div class="extra"><?php _e( 'Extra Packages', 'wp-hotel-booking' ); ?></div>
                        <div class="type"><?php _e( 'Type', 'wp-hotel-booking' ); ?></div>
                        <div class="qty"><?php _e( 'Quantity', 'wp-hotel-booking' ); ?></div>
                    </div>
                    <div class="content" v-for="(extra, index) in item.extra">
                        <div class="extra">
                            <input type="checkbox" v-model="extra.selected"/>{{extra.title}}
                        </div>
                        <div class="type">{{extra.respondent}}</div>
                        <div class="qty"><input type="number" value="1"/></div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <button type="submit" class="button button-primary" :disabled="!addable"
                        @click.prevent.submit="addItem">
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
            computed: {
                item: function (state) {
                    return $store.getters['newItem'];
                },
                // item valid to add to booking
                addable: function () {
                    return this.item.id && this.item.check_in && this.item.check_out && this.item.qty;
                }
            },
            methods: {
                checkAvailable: function () {
                    if (this.item.id && this.item.check_in && this.item.check_out) {
                        $store.dispatch('checkAvailable', this.item);
                    }
                },
                addItem: function () {
                    this.$emit('addItem', this.item);
                },
                closeModal: function () {
                    this.$emit('closeModal');
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>
