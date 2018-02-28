<?php
/**
 * The Woocommerce order details template.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-woocommerce/order/order-details.php.
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
if ( ! $order = wc_get_order( $order_id ) ) {
	return;
}
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array(
	'completed',
	'processing'
) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();

// get booking items
$booking_id = hb_get_post_id_meta( '_hb_woo_order_id', $order->get_order_number() );
$room_items = hb_get_booking_items( $booking_id );
?>

<section class="woocommerce-order-details">

    <h2 class="woocommerce-order-details__title"><?php _e( 'Order details', 'woocommerce' ); ?></h2>

    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

        <thead>
        <tr>
            <th class="woocommerce-table__product-name product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<?php if ( $room_items ) { ?>
                <th class="woocommerce-table__product-name product-name"><?php _e( 'Check in', 'woocommerce' ); ?></th>
                <th class="woocommerce-table__product-name product-name"><?php _e( 'Check out', 'woocommerce' ); ?></th>
			<?php } ?>
            <th class="woocommerce-table__product-table product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
        </tr>
        </thead>

        <tbody>
		<?php
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
			wc_get_template( 'order/order-details-item.php', array(
				'order'              => $order,
				'item_id'            => $item_id,
				'item'               => $item,
				'show_purchase_note' => $show_purchase_note,
				'purchase_note'      => $product ? $product->get_purchase_note() : '',
				'product'            => $product,
				'room_items'         => $room_items
			) );
		} ?>

		<?php do_action( 'woocommerce_order_items_table', $order ); ?>

        </tbody>

        <tfoot>
		<?php foreach ( $order->get_order_item_totals() as $key => $total ) { ?>
            <tr>
                <th scope="row" <?php echo $room_items ? 'colspan="3"' : ''; ?>><?php echo $total['label']; ?></th>
                <td><?php echo $total['value']; ?></td>
            </tr>
		<?php } ?>
        </tfoot>

    </table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

	<?php if ( $show_customer_details ) { ?>
		<?php wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) ); ?>
	<?php } ?>

</section>
