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

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Booking Statistic', 'wp-hotel-booking' ); ?></h1>
</div>

<div id="wphb-booking-statistic">

    <div id="wp-hotel-booking-report-main">
        <canvas id="booking_statistic_chart"></canvas>
    </div>

</div>