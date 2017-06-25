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

$hb_report   = WPHB_Statistic_Price::instance();
$sidebarInfo = apply_filters( 'hotel_booking_sidebar_price_info', array() );
?>
<ul class="chart-legend">
	<?php foreach ( $sidebarInfo as $key => $mote ): ?>
        <li style="border-color: <?php echo esc_attr( hb_random_color() ); ?>">
            <span><b><?php echo esc_html( $mote['title'] ); ?></b></span>
            <p class="amount"><?php echo sprintf( '%s', $mote['descr'] ) ?></p>
        </li>
	<?php endforeach; ?>
</ul>
