<?php

/**
 * The template for displaying mini cart extra item.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/mini-cart-extra-item.php.
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

<?php if ( $extras ) { ?>

    <div class="hb_mini_cart_price_packages">
        <label><?php _e( 'Addition Services:', 'wp-hotel-booking' ) ?></label>
        <ul>
			<?php foreach ( $extras as $extra ) : ?>
                <li>
                    <div class="hb_package_title">
                        <a href="#"><?php printf( '%s (%s)', $extra->product_data->title, hb_format_price( $extra->amount_singular ) ) ?></a>
                        <span>
							(<?php printf( 'x%s', $extra->quantity ) ?>)
							<a href="#" class="hb_package_remove"
                               data-cart-id="<?php echo esc_attr( $extra->cart_id ) ?>"><i class="fa fa-times"></i></a>
						</span>
                    </div>
                </li>
			<?php endforeach; ?>
        </ul>
    </div>

<?php } ?>
