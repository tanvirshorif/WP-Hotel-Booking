<?php

/**
 * Admin View: Admin statistic canvas room.
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

global $hb_report;
?>
<h3 class="chart_title"><?php _e( 'Report Chart Room Unavailable', 'wphb-statistic' ) ?></h3>
<canvas id="hotel_canvas_report_room"></canvas>
<script>
    (function ($) {
        var randomScalingFactor = function () {
            return Math.round(Math.random() * 100);
        };

        window.onload = function () {
            var ctx = document.getElementById('hotel_canvas_report_room').getContext('2d');
            window.myBar = new Chart(ctx).Bar( <?php echo json_encode( $hb_report->series() ) ?>, {
                responsive: true,
                scaleGridLineColor: "rgba(0,0,0,.05)"
            });
        }

    })(jQuery);

</script>
