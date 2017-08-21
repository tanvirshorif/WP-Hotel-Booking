<?php
/**
 * The Woocommerce order details item template.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-woocommerce/order/order-details-item.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Woocommerce/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>

<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
    <td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible );
		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item->get_quantity() ) . '</strong>', $item );

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

		wc_display_item_meta( $item );
		wc_display_item_downloads( $item );

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
		?>
    </td>
	<?php foreach ( $room_items as $room ) { ?>
		<?php if ( get_post_type( hb_get_order_item_meta( $room->order_item_id, 'product_id', true ) ) == 'hb_room' ) { ?>
            <td class="td"
                style="vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ) ) ?>
            </td>
            <td class="td"
                style="vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ) ) ) ?></td>
		<?php } ?>
		<?php
		break;
	} ?>
    <td class="woocommerce-table__product-total product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
    </td>
</tr>

<?php if ( $show_purchase_note && $purchase_note ) { ?>
    <tr class="woocommerce-table__product-purchase-note product-purchase-note">
        <td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
    </tr>
<?php } ?>
