<?php

/**
 * The template for displaying room pricing place in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/pricing-plan.php.
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

<?php
$week_names = hb_date_names();
$plans      = hb_room_get_pricing_plans( get_the_ID() );
$date_order = hb_start_of_week_order();
?>

<?php foreach ( $plans as $plan ) { ?>

    <h4 class="hb_room_pricing_plan_data">
		<?php if ( $plan->start && $plan->end ) { ?>
			<?php printf( '%1$s', date_i18n( hb_get_date_format(), strtotime( $plan->start ) ) ) ?>
            <span><?php _e( 'to', 'wp-hotel-booking' ) ?></span>
			<?php printf( '%1$s', date_i18n( hb_get_date_format(), strtotime( $plan->end ) ) ) ?>
		<?php } else { ?>
			<?php _e( 'Regular plan', 'wp-hotel-booking' ) ?>
		<?php } ?>
    </h4>

    <table class="hb_room_pricing_plans">
        <thead>
        <tr>
	        <?php foreach ( $date_order as $i ) {  ?>
                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
	        <?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
			<?php $prices = $plan->prices ?>
			<?php foreach ( $date_order as $i ) {  ?>
                <td>
					<?php $price = isset( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
					<?php printf( '%s', hb_format_price( $price ) ) ?>
                </td>
			<?php } ?>
        </tr>
        </tbody>
    </table>

<?php } ?>