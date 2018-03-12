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

hb_admin_view( 'booking/items' );
hb_admin_view( 'booking/modal' );
$booking = WPHB_Booking::instance( $post->ID );
?>

<script type="text/x-template" id="tmpl-admin-booking-overview">
    <div id="hb-booking-details" class="postbox " @keyup="keyUp">
        <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"></span><span
                    class="toggle-indicator" aria-hidden="true"></span></button>
        <h2 class="hndle"><span><?php _e( 'Booking Details', 'wp-hotel-booking' ); ?></span></h2>
        <div class="inside">
            <div id="booking-details">
                <div class="booking-data">
                    <h3 class="booking-data-number"><?php echo sprintf( esc_attr__( 'Booking %s', 'wp-hotel-booking' ), hb_format_order_number( $post->ID ) ); ?></h3>
                    <div class="booking-date">
						<?php echo sprintf( __( 'Date %s', 'wp-hotel-booking' ), $post->post_date ); ?>
                    </div>
                </div>
                <div class="booking-user-data">
                    <div class="user-avatar">
                        <!--                        <img v-bind:src="users[customer.id].avatar" v-bind:alt="users[customer.id].email"/>-->
                    </div>
                    <div class="order-user-meta">
                        <div class="user-display-name">
                            <!--                            <a v-bind:href="users[customer.id].link"-->
                            <!--                               target="_blank">{{users[customer.id].display_name}}</a>-->
                        </div>
                        <!--                        <div class="user-email">-->
                        <!--                            {{users[customer.id].email}}-->
                        <!--                        </div>-->
                    </div>
                    <div class="user-info">
                        <ul>
                            <li>
                                <label for="_hb_customer_title"><?php echo __( 'Title:', 'wp-hotel-booking' ); ?></label>
								<?php hb_dropdown_titles( array(
									'name'     => '_hb_customer_title',
									'class'    => 'normal',
									'selected' => $booking->customer_title
								) ); ?>
                            </li>
                            <li>
                                <label for="_hb_customer_first_name"><?php echo __( 'First Name:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_first_name" id="_hb_customer_first_name"
                                       value="<?php echo esc_attr( $booking->customer_first_name ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_last_name"><?php echo __( 'Last Name:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_last_name" id="_hb_customer_last_name"
                                       value="<?php echo esc_attr( $booking->customer_last_name ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_address"><?php echo __( 'Address:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_address" id="_hb_customer_address"
                                       value="<?php echo esc_attr( $booking->customer_address ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_city"><?php echo __( 'City:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_city" id="_hb_customer_city"
                                       value="<?php echo esc_attr( $booking->customer_city ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_state"><?php echo __( 'State:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_state" id="_hb_customer_state"
                                       value="<?php echo esc_attr( $booking->customer_state ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_postal_code"><?php echo __( 'Postal Code:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_postal_code" id="_hb_customer_postal_code"
                                       value="<?php echo esc_attr( $booking->customer_postal_code ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_country"><?php echo __( 'Country:', 'wp-hotel-booking' ); ?></label>
								<?php hb_dropdown_countries( array(
									'name'             => '_hb_customer_country',
									'class'            => 'normal',
									'show_option_none' => __( 'Country', 'wp-hotel-booking' ),
									'selected'         => $booking->customer_country
								) ); ?>
                            </li>
                            <li>
                                <label for="_hb_customer_phone"><?php echo __( 'Phone:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_phone" id="_hb_customer_phone"
                                       value="<?php echo esc_attr( $booking->customer_phone ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_email"><?php echo __( 'Email:', 'wp-hotel-booking' ); ?></label>
                                <input type="email" name="_hb_customer_email" id="_hb_customer_email"
                                       value="<?php echo esc_attr( $booking->customer_email ) ?>"/>
                            </li>
                            <li>
                                <label for="_hb_customer_fax"><?php echo __( 'Fax:', 'wp-hotel-booking' ); ?></label>
                                <input type="text" name="_hb_customer_fax" id="_hb_customer_fax"
                                       value="<?php echo esc_attr( $booking->customer_tax ) ?>"/>
                            </li>
                        </ul>
                    </div>
                    <div class="user-note">
                        <label for="_hb_addition_information"><?php _e( 'Addition Information:', 'wp-hotel-booking' ); ?></label>
                        <textarea name="_hb_addition_information" id="_hb_addition_information" cols="30"
                                  rows="10"><?php echo esc_html( $booking->post->post_content ); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <wphb-booking-items @openModal="openModal"></wphb-booking-items>

        <wphb-booking-modal :class="modal.show ? 'show' : ''" :type="modal.type" :newItem="modalItem"
                            :existItem="modal.item"
                            @closeModal="closeModal" @checkAvailable="checkAvailable"
                            @addItem="addItem"></wphb-booking-modal>

        <wphb-booking-modal-update></wphb-booking-modal-update>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-overview', {
            template: '#tmpl-admin-booking-overview',
            props: ['customer', 'users'],
            data: function () {
                return {
                    modal: {
                        show: false,
                        type: 'add'
                    }
                }
            },
            computed: {
                modalItem: function () {
                    return $store.getters['newItem'];
                }
            },
            methods: {
                keyUp: function (e) {
                    var keyCode = e.keyCode;
                    // escape update course item title
                    if (keyCode === 27) {
                        this.modal.show = false;
                    }
                },
                openModal: function (room) {
                    if (room) {
                        this.modal.type = 'update';
                        this.modal.item = room;
                    }
                    this.modal.show = true;
                },
                checkAvailable: function (item) {
                    $store.dispatch('checkAvailable', item);
                },
                addItem: function (item) {
                    $store.dispatch('addItem', item);
                    this.modal.show = false;
                },
                closeModal: function () {
                    this.modal.show = false;
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>
