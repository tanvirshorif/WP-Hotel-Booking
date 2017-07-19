<?php

/**
 * The template for displaying mini cart item.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/mini-cart-item.php.
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

<div class="hb_mini_cart_item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">

	<?php $cart = WPHB_Cart::instance(); ?>
	<?php $cart_item = $cart->get_cart_item( $cart_id ) ?>

	<?php do_action( 'hotel_booking_before_mini_cart_loop', $room ); ?>

    <div class="hb_mini_cart_top">
        <h4 class="hb_title">
            <a href="<?php echo get_permalink( $room->ID ); ?>"><?php printf( '%s %s', $room->name, $room->capacity_title ? '(' . $room->capacity_title . ')' : '' ) ?></a>
        </h4>
        <span class="hb_mini_cart_remove"><i class="fa fa-times"></i></span>
    </div>

    <div class="hb_mini_cart_number">
        <label><?php _e( 'Quantity: ', 'wp-hotel-booking' ); ?></label>
        <span><?php printf( '%s', $cart_item->quantity ); ?></span>
    </div>

    <!--add extra items in mini cart-->
	<?php do_action( 'hotel_booking_mini_cart_item', $room, $cart_id ); ?>

    <div class="hb_mini_cart_price">
        <label><?php _e( 'Price: ', 'wp-hotel-booking' ); ?></label>
        <span><?php printf( '%s', hb_format_price( $cart_item->amount ) ) ?></span>
    </div>

	<?php do_action( 'hotel_booking_after_mini_cart_loop', $room, $cart_id ); ?>

</div>
