<?php

/**
 * The template for displaying user account page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/account/account.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	printf( wp_kses( __( 'You must <strong><a href="%s">Login<a/></strong>.', 'wp-hotel-booking' ), array( 'strong' => array() ) ), wp_login_url( hb_get_account_url() ) );

	return;
}

$user     = WPHB_User::get_current_user();
$bookings = $user->get_bookings();

if ( ! $bookings ) {
	_e( 'You have no order booking system', 'wp-hotel-booking' );

	return;
}
?>

<div class="hb_booking_wrapper">

    <h2><?php _e( 'Bookings', 'wp-hotel-booking' ) ?></h2>

    <table class="hb_booking_table">

        <thead>
        <tr>
            <th><?php _e( 'ID', 'wp-hotel-booking' ); ?></th>
            <th><?php _e( 'Booking Date', 'wp-hotel-booking' ); ?></th>
            <th><?php _e( 'Total', 'wp-hotel-booking' ); ?></th>
            <th><?php _e( 'Status', 'wp-hotel-booking' ); ?></th>
            <th><?php _e( 'Actions', 'wp-hotel-booking' ); ?></th>
        </tr>
        </thead>

        <tbody>
		<?php foreach ( $bookings as $booking ) { ?>
            <tr>
                <td><?php printf( '%s', hb_format_order_number( $booking->id ) ) ?></td>
                <td><?php printf( '%s', date_i18n( hb_get_date_format(), strtotime( $booking->post_date ) ) ) ?></td>
                <td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
                <td><?php printf( '%s', hb_get_booking_status_label( $booking->id ) ) ?></td>
                <td>
                    <a href="<?php echo esc_attr( hb_get_thank_you_url( $booking->id, $booking->booking_key ) ); ?>"
                       target="_blank" class="view-booking"><?php _e( 'View', 'wp-hotel-booking' ); ?></a>
					<?php $status = get_post_status( $booking->id ); ?>
					<?php if ( get_option( 'tp_hotel_booking_customer_cancel_booking' ) && in_array( $status, array(
							'hb-pending',
							'hb-processing'
						) ) ) { ?>
                        <a href="#" class="customer-cancel-booking"
                           data-booking-id="<?php echo esc_attr( $booking->id ); ?>">
							<?php _e( 'Cancel Booking', 'wp-hotel-booking' ); ?>
                        </a>
					<?php } ?>
                </td>
            </tr>
		<?php } ?>
        </tbody>

    </table>

</div>
