<?php
/**
 * Admin View: Admin statistic sidebar price.
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

<?php $sidebar = apply_filters( 'hotel_booking_sidebar_price_info', array() ); ?>

<ul class="chart-legend">
	<?php foreach ( $sidebar as $key => $info ) { ?>
        <li style="border-color: <?php echo esc_attr( hb_random_color() ); ?>">
            <span><b><?php echo esc_html( $info['title'] ); ?></b></span>
            <p class="amount"><?php echo sprintf( '%s', $info['amount'] ) ?></p>
        </li>
	<?php } ?>
</ul>

<h3 class="chart_title">
	<?php _e( 'Report Chart Amount Total', 'wphb-statistic' ) ?>
</h3>

<canvas id="statistic_booking_price"></canvas>