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

<div id="hotel_booking_room_filter-<?php echo uniqid(); ?>" class="wphb_room_filter">
	<?php if ( isset( $atts['title'] ) && $atts['title'] ) { ?>
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
	<?php } ?>

    <div class="price_slider_wrapper">
        <div class="price_slider" style="display:none;"></div>
        <div class="price_slider_amount">
            <input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( 0 ); ?>"
                   placeholder="<?php esc_attr_e( 'Min price', 'wp-hotel-booking' ); ?>"/>
            <input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( 1 ); ?>"
                   placeholder="<?php esc_attr_e( 'Max price', 'wp-hotel-booking' ); ?>"/>
            <button type="submit" class="button"><?php _e( 'Filter', 'wp-hotel-booking' ); ?></button>
            <div class="price_label" style="display:none;">
				<?php _e( 'Price:', 'wp-hotel-booking' ); ?> <span class="from"></span> &mdash; <span class="to"></span>
            </div>
        </div>
    </div>
</div>