<?php

/**
 * Admin View: Admin statistic canvas price.
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
<h3 class="chart_title"><?php _e( 'Report Chart Amount Total', 'wphb-statistic' ) ?></h3>
<canvas id="hotel_canvas_report_price"></canvas>
<script>

    (function ($) {
        window.onload = function () {
            var ctx = document.getElementById('hotel_canvas_report_price').getContext('2d');

            window.myLine = new Chart(ctx).Line( <?php echo json_encode( $hb_report->series() ) ?>, {
                responsive: true
            });
        }


    })(jQuery);

</script>
