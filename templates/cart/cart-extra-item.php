<?php

/**
 * The template for displaying extra item in cart page.
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

<tr class="hb_checkout_item package" data-cart-id="<?php echo esc_attr( $cart_id ) ?>"
    data-parent-id="<?php echo esc_attr( $extra->parent_id ) ?>">

    <td colspan="<?php echo is_hb_cart() ? 1 : 0 ?>">
		<?php if ( is_hb_cart() ) { ?>
            <a href="#" class="hb_package_remove" data-cart-id="<?php echo esc_attr( $cart_id ) ?>"
               data-parent-id="<?php echo esc_attr( $extra->parent_id ) ?>"><i class="fa fa-times"></i></a>
		<?php } ?>
    </td>

    <td><?php echo esc_html( $extra->quantity ); ?></td>

    <td colspan="<?php echo is_hb_cart() ? 3 : 2 ?>"><?php printf( '%s', $extra->product_data->title ) ?></td>

    <td class="hb_gross_total"><?php echo hb_format_price( $extra->amount_exclude_tax ) ?></td>
</tr>
