<?php

/**
 * Admin View: Statistic admin view page.
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
$tab        = hb_get_request( 'tab', 'price' );
$range      = hb_get_request( 'range', '7day' );
$date_start = hb_get_request( 'report_in', '' );
$date_end   = hb_get_request( 'report_out', '' );
$room_id    = hb_get_request( 'room_id', '' );
?>


<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Booking Statistic', 'wp-hotel-booking' ); ?></h1>
</div>


<div id="wphb-booking-statistic">
	<?php
	/**
	 * Show sections.
	 */
	do_action( 'wphb_statistic_admin_settings_sections' );
	?>

    <div id="wp-hotel-booking-report" class="postbox">

        <div id="poststuff">
            <h3>

				<?php
				/**
				 * Show range filter.
				 */
				do_action( 'wphb_statistic_admin_range_filter' );

				/**
				 * Show date filter.
				 */
				do_action( 'wphb_statistic_date_filter', $tab, $date_start, $date_end, $room_id );

				/**
				 * Show export form.
				 */
				do_action( 'wphb_statistic_actions', $tab, $range, $date_start, $date_end );
				?>
            </h3>

        </div>

        <div id="wp-hotel-booking-report-main">
			<?php
			/**
			 * Show statistic charts.
			 */
			do_action( 'wphb_statistic_charts', $tab ); ?>
        </div>

    </div>
</div>