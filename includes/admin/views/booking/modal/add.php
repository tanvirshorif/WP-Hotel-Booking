<?php

/**
 * Admin View: Modal add booking item.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit; ?>

<script type="text/x-template" id="tmpl-admin-booking-modal-add">

    <form name="booking-room-item" class="booking-room-item" @submit.prevent="">

        <div class="header">
            <h3><?php _e( 'Add new item', 'wp-hotel-booking' ); ?></h3>
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
                        <select name="product_id" class="select-item" v-model="item.id">
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
                        <input type="text" name="check_in_date" class="check_in_date" v-model="item.check_in_date">
                        <template>
							<?php if ( hb_get_option( 'booking_time' ) ) { ?>
                                <template>
                                    <a href="#"
                                       @click="addCheckInTime"><?php _e( 'Add Time', 'wp-hotel-booking' ); ?></a>
                                    <a href="#" :class="showCheckInTime ? 'show': 'hide'"
                                       @click="removeCheckInTime"><?php _e( 'Remove Time', 'wp-hotel-booking' ); ?></a>
                                </template>
							<?php } ?>
                            <input :class="showCheckInTime ? 'show': 'hide'" type="text" name="check_in_time"
                                   class="check_in_time"
                                   v-model="item.check_in_time">
                        </template>
                    </div>
                    <div class="checkout">
                        <input type="text" name="check_out_date" class="check_out_date" v-model="item.check_out_date">
                        <template>
							<?php if ( hb_get_option( 'booking_time' ) ) { ?>
                                <template>
                                    <a href="#"
                                       @click="addCheckOutTime"><?php _e( 'Add Time', 'wp-hotel-booking' ); ?></a>
                                    <a href="#" :class="showCheckOutTime ? 'show': 'hide'"
                                       @click="removeCheckOutTime"><?php _e( 'Remove Time', 'wp-hotel-booking' ); ?></a>
                                </template>
							<?php } ?>
                            <input :class="showCheckOutTime ? 'show': 'hide'" type="text" name="check_out_time"
                                   class="check_out_time"
                                   v-model="item.check_out_time">
                        </template>
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
                    <div class="qty">
                        <input type="number" min="1" v-model="extra.qty" v-bind:readonly="extra.respondent == 'trip'"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <button type="submit" class="button"
                    @click.prevent.submit="checkAvailable"><?php echo __( 'Check Available', 'wp-hotel-booking' ); ?>
            </button>
            <button type="submit" class="button button-primary" :disabled="!addable"
                    @click.prevent.submit="addItem">
				<?php echo __( 'Add', 'wp-hotel-booking' ); ?>
            </button>
        </div>
    </form>

</script>

<script type="text/javascript">

    (function (Vue, $store, $) {

        Vue.component('wphb-booking-modal-add', {
            template: '#tmpl-admin-booking-modal-add',
            props: ['item'],
            data: function () {
                return {
                    showCheckInTime: false,
                    showCheckOutTime: false
                }
            },
            computed: {
                // item valid to add to booking
                addable: function () {
                    return this.item.id && this.item.check_in_date && this.item.check_out_date && this.item.qty;
                }
            },
            methods: {
                addCheckInTime: function () {
                    this.showCheckInTime = true;
                },
                removeCheckInTime: function () {
                    $('.checkin input.check_in_time').val(0);
                    this.item.check_in_time = '';
                    this.showCheckInTime = false;
                },
                addCheckOutTime: function () {
                    this.showCheckOutTime = true;
                },
                removeCheckOutTime: function () {
                    $('.checkout input.check_out_time').val(0);
                    this.item.check_out_time = '';
                    this.showCheckOutTime = false;
                },
                checkAvailable: function () {
                    this.item.available = 0;
                    var checkInDate = $('.checkin input.check_in_date').val(),
                        checkOutDate = $('.checkout input.check_out_date').val(),
                        checkInTime = $('.checkin input.check_in_time').val(),
                        checkOutTime = $('.checkout input.check_out_time').val();

                    if (this.item.id && checkInDate && checkOutDate) {
                        this.item.check_in_date = checkInDate;
                        this.item.check_out_date = checkOutDate;
                        this.item.check_in_time = checkInTime;
                        this.item.check_out_time = checkOutTime;
                        this.$emit('checkAvailable', this.item);
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

    })(Vue, WPHB_Booking_Store, jQuery);

</script>