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
?>

<script type="text/x-template" id="tmpl-admin-booking-overview">
    <div id="hb-booking-details" class="postbox " @keyup="keyUp">
        <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"></span><span
                    class="toggle-indicator" aria-hidden="true"></span></button>
        <h2 class="hndle"><span><?php _e( 'Booking Details', 'wp-hotel-booking' ); ?></span></h2>
        <div class="inside">
            <div id="booking-details">
                <div class="booking-user-data">
                    <div class="user-avatar">
                        <!--                        <img v-bind:src="users[customer.id].avatar" v-bind:alt="users[customer.id].email"/>-->
                    </div>
                    <div class="order-user-meta">
                        <div class="user-display-name">
                            <!--                            <a v-bind:href="users[customer.id].link"-->
                            <!--                               target="_blank">{{users[customer.id].display_name}}</a>-->
                        </div>
                        <div class="user-email">
                            <!--                            {{users[customer.id].email}}-->
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
            <wphb-booking-items @openModal="openModal"></wphb-booking-items>
        </div>
        <wphb-booking-modal :class="modal.show ? 'show' : ''" :type="modal.type" :item="modal.item"
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
                        type: 'add',
                        item: $store.getters['newItem']
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
                    console.log(room);
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
