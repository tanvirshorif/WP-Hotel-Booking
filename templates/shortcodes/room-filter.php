<?php

/**
 * The template display shortcode for room filter by price.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/shortcodes/room-filter.php.
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

<div id="hotel-booking-room-filter-<?php echo uniqid(); ?>" class="wphb-room-filter">
	<?php if ( isset( $atts['title'] ) && $atts['title'] ) { ?>
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
	<?php } ?>

	<?php
	$prices = hb_get_min_max_rooms_price();
	$min    = hb_get_request( 'min_price', $prices['min'] );
	$max    = hb_get_request( 'max_price', $prices['max'] );
	?>

    <div class="price-slider-wrapper">
        <form class="price-slider-amount">
            <div class="price-room-range"
                 data-value-max="<?php echo esc_attr( $max ); ?>"
                 data-max-price="<?php echo esc_attr( $prices['max'] ); ?>"
                 data-value-min="<?php echo esc_attr( $min ); ?>"
                 data-min-price="<?php echo esc_attr( $prices['min'] ); ?>">
            </div>
            <span><?php _e( 'Price:', 'wp-hotel-booking' ); ?>
                <input class="range-price" readonly style="border:0; color:#f6931f; font-weight:bold;">
            </span>
            <input type="hidden" id="min_price" name="min_price" value="<?php echo esc_attr( $prices['min'] ); ?>"
                   placeholder="<?php esc_attr_e( 'Min price', 'wp-hotel-booking' ); ?>"/>
            <input type="hidden" id="max_price" name="max_price" value="<?php echo esc_attr( $prices['max'] ); ?>"
                   placeholder="<?php esc_attr_e( 'Max price', 'wp-hotel-booking' ); ?>"/>
            <button type="submit" class="button"><?php _e( 'Filter', 'wp-hotel-booking' ); ?></button>
            <div class="price_label" style="display:none;">
				<?php _e( 'Price:', 'wp-hotel-booking' ); ?> <span class="from"></span> &mdash; <span class="to"></span>
            </div>
        </form>
    </div>
</div>