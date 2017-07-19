<?php

/**
 * The template for displaying user account page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/account//account.php.
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
        </tr>
        </thead>

        <tbody>
		<?php foreach ( $bookings as $booking ) : ?>

            <tr>
                <td><?php printf( '%s', hb_format_order_number( $booking->id ) ) ?></td>
                <td><?php printf( '%s', date_i18n( hb_get_date_format(), strtotime( $booking->post_date ) ) ) ?></td>
                <td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
                <td><?php printf( '%s', hb_get_booking_status_label( $booking->id ) ) ?></td>
            </tr>

		<?php endforeach; ?>
        </tbody>

    </table>

</div>
