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

$booking = WPHB_Booking::instance( $post->ID );
$rooms   = hb_get_order_items( $post->ID );
?>

<div id="booking-details">
    <div class="booking-user-data">
        <div class="user-avatar">
			<?php echo get_avatar( $booking->user_id, 120 ); ?>
        </div>
        <div class="order-user-meta">
			<?php if ( $user = get_userdata( $booking->user_id ) ) { ?>
                <div class="user-display-name">
					<?php echo sprintf( '<a href="%s">%s</a>', get_edit_user_link( $booking->user_id ), $user->user_login ); ?>
                </div>
                <div class="user-email">
					<?php echo $user->user_email ? $user->user_email : ''; ?>
                </div>
			<?php } else {
				echo __( '[Guest]', 'wp-hotel-booking' );
			} ?>
        </div>
    </div>
    <div class="booking-data">
        <h3 class="booking-data-number"><?php echo sprintf( esc_attr__( 'Booking %s', 'wp-hotel-booking' ), hb_format_order_number( $post->ID ) ); ?></h3>
        <div class="booking-date">
			<?php echo sprintf( __( 'Date %s', 'wp-hotel-booking' ), $post->post_date ); ?>
        </div>
    </div>
</div>

<div id="booking-items">

    <h3><?php echo __( 'Booking Items', 'wp-hotel-booking' ); ?></h3>

    <table cellpadding="0" cellspacing="0" class="booking_item_table">
        <thead>
        <tr>
            <th><?php _e( 'Item', 'wp-hotel-booking' ); ?></th>
            <th><?php _e( 'Check in', 'wp-hotel-booking' ) ?></th>
            <th><?php _e( 'Checkout', 'wp-hotel-booking' ) ?></th>
            <th class="night"><?php _e( 'Night', 'wp-hotel-booking' ); ?></th>
            <th class="qty"><?php _e( 'Qty', 'wp-hotel-booking' ); ?></th>
            <th class="total"><?php _e( 'Total', 'wp-hotel-booking' ); ?></th>
            <th class="actions"><?php _e( 'Actions', 'wp-hotel-booking' ) ?></th>
        </tr>
        </thead>
        <tbody>

		<?php foreach ( $rooms as $k => $room ) { ?>

            <tr>
                <td>
					<?php printf( '<a href="%s">%s</a>', get_edit_post_link( hb_get_order_item_meta( $room->order_item_id, 'product_id', true ) ), $room->order_item_name ) ?>
                </td>
                <td>
					<?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ) ) ?>
                </td>
                <td>
					<?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ) ) ) ?>
                </td>
                <td>
					<?php printf( '%d', hb_count_nights_two_dates( hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ) ) ?>
                </td>
                <td>
					<?php printf( '%s', hb_get_order_item_meta( $room->order_item_id, 'qty', true ) ) ?>
                </td>
                <td>
					<?php printf( '%s', hb_format_price( hb_get_order_item_meta( $room->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ); ?>
                </td>
                <td class="actions">
                    <a href="#" class="edit" data-booking-id="<?php echo esc_attr( $booking->id ); ?>"
                       data-booking-item-id="<?php echo esc_attr( $room->order_item_id ) ?>"
                       data-booking-item-type="line_item">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="#" class="remove" data-booking-id="<?php echo esc_attr( $booking->id ); ?>"
                       data-booking-item-id="<?php echo esc_attr( $room->order_item_id ) ?>"
                       data-booking-item-type="line_item">
                        <i class="fa fa-times-circle"></i>
                    </a>
                </td>
            </tr>

			<?php $packages = hb_get_order_items( $booking->id, 'sub_item', $room->order_item_id ); ?>
			<?php if ( $packages ) { ?>
				<?php foreach ( $packages as $package ) { ?>
					<?php $extra = hotel_booking_get_product_class( hb_get_order_item_meta( $package->order_item_id, 'product_id', true ) ); ?>
                    <tr data-order-parent="<?php echo esc_attr( $room->order_item_id ); ?>">
                        <td><input type="checkbox" name="book_item[]"
                                   value="<?php echo esc_attr( $package->order_item_id ); ?>"/></td>
                        <td colspan="3">
							<?php echo esc_html( $package->order_item_name ); ?>
                        </td>
                        <td>
							<?php echo esc_html( hb_get_order_item_meta( $package->order_item_id, 'qty', true ) ); ?>
                        </td>
                        <td>
	                        <?php echo hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ); ?>
                        </td>
                        <td class="actions">
							<?php if ( $extra->respondent === 'number' ) { ?>
                                <a href="#" class="edit" data-booking-id="<?php echo esc_attr( $booking->id ); ?>"
                                   data-booking-item-id="<?php echo esc_attr( $package->order_item_id ); ?>"
                                   data-booking-item-type="sub_item"
                                   data-booking-item-parent="<?php echo esc_attr( $package->order_item_parent ); ?>">
                                    <i class="fa fa-pencil"></i>
                                </a>
							<?php } ?>
                            <a href="#" class="remove" data-booking-id="<?php echo esc_attr( $booking->id ); ?>"
                               data-booking-item-id="<?php echo esc_attr( $package->order_item_id ); ?>"
                               data-booking-item-type="sub_item"
                               data-booking-item-parent="<?php echo $package->order_item_parent; ?>">
                                <i class="fa fa-times-circle"></i>
                            </a>
                        </td>
                    </tr>
				<?php } ?>
			<?php } ?>
		<?php } ?>

        <tr>
            <td colspan="6"><?php _e( 'Sub Total', 'wp-hotel-booking' ) ?></td>
            <td>
				<?php printf( '%s', hb_format_price( hb_booking_subtotal( $booking->id ), hb_get_currency_symbol( $booking->currency ) ) ); ?>
            </td>
        </tr>
        <tr>
            <td colspan="6"><?php _e( 'Tax', 'wp-hotel-booking' ) ?></td>
            <td>
				<?php printf( '%s', apply_filters( 'hotel_booking_admin_booking_details', hb_format_price( hb_booking_tax_total( $booking->id ), hb_get_currency_symbol( $booking->currency ) ), $booking ) ); ?>
            </td>
        </tr>
        <tr>
            <td colspan="6"><?php _e( 'Grand Total', 'wp-hotel-booking' ) ?></td>
            <td>
				<?php printf( '%s', hb_format_price( hb_booking_total( $booking->id ), hb_get_currency_symbol( $booking->currency ) ) ) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="booking-actions">
        <div class="actions">
			<?php do_action( 'hb_booking_items_actions', $booking ); ?>
            <a href="#" class="button" id="add_room_item"
               data-booking-id="<?php echo esc_attr( $booking->id ) ?>"><?php _e( 'Add Room Item', 'wp-hotel-booking' ); ?></a>
        </div>
    </div>


	<?php if ( $booking->coupon_id ) : ?>
        <div class="coupon">
            <div>
				<?php printf( __( 'Coupon(<a href="%s">%s</a>)', 'wp-hotel-booking' ), get_edit_post_link( $booking->coupon_id ), $booking->coupon_code ) ?>
            </div>
            <div>
				<?php printf( '-%s', hb_format_price( $booking->coupon_value, hb_get_currency_symbol( $booking->currency ) ) ); ?>
            </div>
        </div>
	<?php endif; ?>

</div>
