<?php

/**
 * Admin View: Room pricing table.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
global $post;
$week_names   = hb_date_names();
$regular_plan = hb_room_get_regular_plan( $post->ID );
$plan_id      = isset( $regular_plan->ID ) ? $regular_plan->ID : 0;
$date_order   = hb_start_of_week_order();
?>

<div class="hb-pricing-list">
    <input type="hidden" name="_hbpricing[plan_id][]" value="<?php echo esc_attr( $plan_id ) ?>"/>
    <table>
        <thead>
        <tr>
			<?php foreach ( $date_order as $i ) { ?>
                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
			<?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
			<?php $prices = isset( $regular_plan->prices ) ? $regular_plan->prices : array(); ?>
			<?php foreach ( $date_order as $i ) { ?>
                <td>
					<?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                    <input class="hb-pricing-price" type="number" min="0" step="any"
                           name="_hbpricing[prices][<?php echo sprintf( '%s', $plan_id ); ?>][<?php echo esc_attr( $i ); ?>]"
                           value="<?php echo $price ? esc_attr( $price ) : 0; ?>" size="10"/>
                </td>
			<?php } ?>
        </tr>
        </tbody>
    </table>
</div>

<?php wp_nonce_field( 'hotel_booking_room_pricing_nonce', 'hotel-booking-room-pricing-nonce' ); ?>
