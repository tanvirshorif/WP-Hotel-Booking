<?php

/**
 * Admin View: Admin statistic sidebar room.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Statistic/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
$range      = hb_get_request( 'range', '7day' );
$date_start = hb_get_request( 'report_in', '' );
$date_end   = hb_get_request( 'report_out', '' );
$room_id    = array( hb_get_request( 'room_id', '' ) );
?>

<?php
/**
 * Show room availability.
 */
do_action( 'wphb_statistic_room_availability', $range, $date_start, $date_end, $room_id );
?>

<h3 class="chart_title">
	<?php _e( 'Report Chart Room Unavailable', 'wphb-statistic' ) ?>
</h3>

<canvas id="hotel_canvas_report_room"></canvas>