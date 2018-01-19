<?php

/**
 * Admin View: Booking actions.
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
$users   = get_users( array( 'fields' => array( 'ID' ) ) );
?>

<script type="text/x-template" id="tmpl-admin-booking-actions">
    <div id="side-sortables" class="meta-box-sortables">
        <div id="hb-booking-actions" class="postbox ">
            <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"></span><span
                        class="toggle-indicator" aria-hidden="true"></span></button>
            <h2 class="hndle"><span><?php _e( 'Booking Actions', 'wp-hotel-booking' ); ?></span></h2>
            <div class="inside">

                <div id="booking-actions">
					<?php wp_nonce_field( 'hotel-booking-metabox-booking-actions', 'hotel_booking_metabox_booking_actions_nonce' ); ?>
                    <ul>
                        <li>
                            <label for="_hb_booking_status"><?php _e( 'Booking Status:', 'wp-hotel-booking' ); ?></label>
                            <select name="_hb_booking_status" id="_hb_booking_status">
								<?php $status = hb_get_booking_statuses(); ?>
								<?php foreach ( $status as $key => $value ) { ?>
                                    <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $post->post_status, $key ); ?>><?php printf( '%s', $value ) ?></option>
								<?php } ?>
                            </select>
                        </li>
                        <li>
                            <label><?php _e( 'Customer:', 'wp-hotel-booking' ); ?></label>
                            <div class="customer_details">
                                <label for="_hb_user_id"></label>
                                <select name="_hb_user_id" id="_hb_user_id" v-model="customer.id">
                                    <option value="-1"><?php echo __( '[Guest]', 'wp-hotel-booking' ); ?></option>
									<?php foreach ( $users as $_users ) {
										$_id   = $_users->ID;
										$_user = get_userdata( $_users->ID );
										?>
                                        <option value="<?php echo esc_attr( $_id ) ?>"><?php printf( '%s', $_user->user_login ) ?></option>
									<?php } ?>
                                </select>
                            </div>
                        </li>
                        <li>
                            <label for="_hb_method"><?php _e( 'Payment Method:', 'wp-hotel-booking' ); ?></label>
							<?php $methods = hb_get_payment_gateways(); ?>
                            <select name="_hb_method" id="_hb_method">
								<?php if ( $booking->method && ! array_key_exists( $booking->method, $methods ) ) { ?>
                                    <option value="<?php echo esc_attr( $booking->method ) ?>"
                                            selected><?php printf( __( '%s is not available', 'wp-hotel-booking' ), $booking->method_title ) ?></option>
								<?php } ?>
								<?php foreach ( $methods as $id => $method ) { ?>
                                    <option value="<?php echo esc_attr( $id ) ?>" <?php selected( $booking->method, $id ); ?>><?php printf( '%s', $method->title ) ?></option>
								<?php } ?>
                            </select>
                        </li>
                    </ul>
                    <div class="major-publishing-actions">
                        <div id="delete-action">
							<?php if ( current_user_can( 'delete_post', $post->ID ) ) { ?>
                                <a class="submitdelete deletion"
                                   href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ) ?>"><?php _e( 'Move to Trash', 'wp-hotel-booking' ); ?></a>
							<?php } ?>
                        </div>
                        <div id="publishing-action">
                            <button name="save" type="submit" class="button button-primary" id="publish">
								<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'wp-hotel-booking' ) : __( 'Save', 'wp-hotel-booking' ) ) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</script>


<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-actions', {
            template: '#tmpl-admin-booking-actions',
            props: ['customer', 'users']
        });

    })(Vue, WPHB_Booking_Store);

</script>
