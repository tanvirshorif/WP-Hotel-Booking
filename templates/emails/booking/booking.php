<?php
/**
 * The email template for displaying customer booking detail.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/booking/booking.php.
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
?>

<h2 class="section-title">
	<?php echo __( 'Booking ', 'wp-hotel-booking' ) . hb_format_order_number( $booking->id ); ?>
</h2>

<table class="width-100 booking_details" cellspacing="0" cellpadding="0">
    <tr>
        <th><?php _e( 'Room', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Check in', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Check out', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( '#', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Price', 'wp-hotel-booking' ) ?></th>
    </tr>

	<?php $items = hb_get_booking_items( $booking->id ); ?>

	<?php foreach ( $items as $k => $item ) { ?>
        <tr>
            <td><?php printf( '%s', $item->order_item_name ) ?></td>
            <td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_booking_item_meta( $item->order_item_id, 'check_in_date', true ) ) ) ?></td>
            <td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_booking_item_meta( $item->order_item_id, 'check_out_date', true ) ) ) ?></td>
            <td><?php printf( '%s', hb_get_booking_item_meta( $item->order_item_id, 'qty', true ) ) ?></td>
            <td><?php printf( '%s', hb_format_price( hb_get_booking_item_meta( $item->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
        </tr>

		<?php $packages = hb_get_booking_items( $booking->id, 'sub_item', $item->order_item_id );
		if ( ! $packages ) {
			$html = array();
			foreach ( $packages as $i => $package ) {
				$html[] = '<tr>';
				$html[] = '<td>' . sprintf( '%s', $package->order_item_name ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_booking_item_meta( $package->order_item_id, 'check_in_date', true ) ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_booking_item_meta( $package->order_item_id, 'check_out_date', true ) ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', hb_get_booking_item_meta( $package->order_item_id, 'qty', true ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', hb_format_price( hb_get_booking_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ) . '</td>';
				$html[] = '</tr>';
			}

			printf( '%s', implode( '', $html ) );
		} ?>

	<?php } ?>
    <tr>
        <td colspan="4"><b><?php _e( 'Subtotal', 'wp-hotel-booking' ) ?></b></td>
        <td><?php printf( '%s', hb_format_price( $booking->sub_total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
    </tr>
    <tr>
        <td colspan="4"><b><?php _e( 'Payment method', 'wp-hotel-booking' ) ?></b></td>
        <td><?php echo esc_html( $booking->method_title ) ?></td>
    </tr>
    <tr>
        <td colspan="4"><b><?php _e( 'Total', 'wp-hotel-booking' ) ?></b></td>
        <td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
    </tr>
</table>

<?php if ( $booking->content ) { ?>
    <h2><?php _e( 'Addition Information', 'wp-hotel-booking' ); ?></h2>
    <p><?php printf( '%s', $booking->content ) ?></p>
<?php } ?>
