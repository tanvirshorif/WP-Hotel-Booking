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
?>

<div id="booking-actions">
	<?php wp_nonce_field( 'hotel-booking-metabox-booking-actions', 'hotel_booking_metabox_booking_actions_nonce' ); ?>
    <ul>
        <li>
            <label for="_hb_booking_status"><?php _e( 'Booking Status:', 'wp-hotel-booking' ); ?></label>
            <select name="_hb_booking_status" id="_hb_booking_status">
				<?php $status = hb_get_booking_statuses(); ?>
				<?php foreach ( $status as $key => $value ) : ?>
                    <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $post->post_status, $key ); ?>><?php printf( '%s', $value ) ?></option>
				<?php endforeach; ?>
            </select>
        </li>
        <li>
            <label><?php _e( 'Customer:', 'wp-hotel-booking' ); ?></label>
            <div class="customer_details">
                <label for="_hb_user_id"></label>
                <select name="_hb_user_id" id="_hb_user_id">
					<?php if ( $booking->user_id ) { ?>
						<?php $user = get_userdata( $booking->user_id ); ?>
                        <option value="<?php echo esc_attr( $booking->user_id ) ?>"
                                selected><?php printf( '%s(#%s %s)', $user->user_login, $booking->user_id, $user->user_email ) ?></option>
					<?php } else {
						// default customer booking for current user id
						$id   = get_current_user_id();
						$user = get_userdata( $id ); ?>
                        <option value="<?php echo esc_attr( $id ) ?>"
                                selected><?php printf( '%s(#%s %s)', $user->user_login, $booking->user_id, $user->user_email ) ?></option>
					<?php } ?>
                </select>
            </div>
        </li>
        <li>
            <label for="_hb_method"><?php _e( 'Payment Method:', 'wp-hotel-booking' ); ?></label>
			<?php $methods = hb_get_payment_gateways(); ?>
            <select name="_hb_method" id="_hb_method">
				<?php if ( $booking->method && ! array_key_exists( $booking->method, $methods ) ) : ?>
                    <option value="<?php echo esc_attr( $booking->method ) ?>"
                            selected><?php printf( __( '%s is not available', 'wp-hotel-booking' ), $booking->method_title ) ?></option>
				<?php endif; ?>
				<?php foreach ( $methods as $id => $method ) { ?>
                    <option value="<?php echo esc_attr( $id ) ?>" <?php selected( $booking->method, $id ); ?>><?php printf( '%s(%s)', $method->title, $method->description ) ?></option>
				<?php } ?>
            </select>
        </li>
    </ul>
    <div class="major-publishing-actions">
        <div id="delete-action">
			<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
                <a class="submitdelete deletion"
                   href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ) ?>"><?php _e( 'Move to Trash', 'wp-hotel-booking' ); ?></a>
			<?php endif; ?>
        </div>
        <div id="publishing-action">
            <button name="save" type="submit" class="button button-primary" id="publish">
				<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'wp-hotel-booking' ) : __( 'Save Book', 'wp-hotel-booking' ) ) ?>
            </button>
        </div>
        <div class="clear"></div>
    </div>
</div>
