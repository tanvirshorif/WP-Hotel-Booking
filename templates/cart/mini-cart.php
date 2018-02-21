<?php

/**
 * The template for displaying mini cart.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/mini-cart.php.
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
$cart  = WPHB_Cart::instance();
$rooms = $cart->get_rooms();
?>

<?php if ( $rooms ) { ?>

	<?php foreach ( $rooms as $key => $room ) { ?>
		<?php if ( $cart_item = $cart->get_cart_item( $key ) ) { ?>
			<?php hb_get_template( 'cart/mini-cart-item.php', array( 'cart_id' => $key, 'room' => $room ) ); ?>
		<?php } ?>
	<?php } ?>

    <div class="hb_mini_cart_footer">
        <a href="<?php echo esc_url( hb_get_checkout_url() ); ?>"
           class="hb_button hb_checkout"><?php _e( 'Check Out', 'wp-hotel-booking' ); ?></a>
        <a href="<?php echo esc_url( hb_get_cart_url() ); ?>"
           class="hb_button hb_view_cart"><?php _e( 'View Cart', 'wp-hotel-booking' ); ?></a>
    </div>

<?php } else { ?>
    <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty.', 'wp-hotel-booking' ); ?></p>
<?php } ?>
