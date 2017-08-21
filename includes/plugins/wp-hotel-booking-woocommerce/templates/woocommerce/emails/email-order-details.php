<?php
/**
 * The email template for Woocommerce order details.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-woocommerce/emails/email-order-details.php.
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
// get booking items
$booking_id = hb_get_post_id_meta( '_hb_woo_order_id', $order->get_order_number() );
$room_items = hb_get_order_items( $booking_id );
?>

<!--check rtl-->
<?php $text_align = is_rtl() ? 'right' : 'left'; ?>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php if ( ! $sent_to_admin ) { ?>
    <h2><?php printf( __( 'Order #%s', 'wphb-woocommerce' ), $order->get_order_number() ); ?></h2>
<?php } else { ?>
    <h2><a class="link"
           href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ); ?>">
			<?php printf( __( 'Order #%s', 'wphb-woocommerce' ), $order->get_order_number() ); ?></a>
        (<?php printf( '<time datetime="%s">%s</time>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ); ?>
        )</h2>
<?php } ?>

<!--email table-->
<table class="td" cellspacing="0" cellpadding="6"
       style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
    <thead>
    <tr>
        <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;">
			<?php _e( 'Product', 'wphb-woocommerce' ); ?>
        </th>
		<?php if ( $room_items ) { ?>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;">
				<?php _e( 'Check in', 'wphb-woocommerce' ) ?>
            </th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;">
				<?php _e( 'Check out', 'wphb-woocommerce' ) ?>
            </th>
		<?php } ?>
        <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;">
			<?php _e( 'Quantity', 'wphb-woocommerce' ); ?>
        </th>
        <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;">
			<?php _e( 'Price', 'wphb-woocommerce' ); ?>
        </th>
    </tr>
    </thead>
    <tbody>
	<?php echo wc_get_email_order_items( $order, array(
		'show_sku'      => $sent_to_admin,
		'show_image'    => false,
		'image_size'    => array( 32, 32 ),
		'plain_text'    => $plain_text,
		'sent_to_admin' => $sent_to_admin,
	) ); ?>
    </tbody>
    <tfoot>
	<?php if ( $totals = $order->get_order_item_totals() ) { ?>
		<?php $i = 0; ?>
		<?php foreach ( $totals as $total ) { ?>
			<?php $i ++; ?>
            <tr>
                <th class="td" scope="row" colspan="4"
                    style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['label']; ?>
                </th>
                <td class="td"
                    style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['value']; ?>
                </td>
            </tr>
		<?php } ?>
	<?php } ?>
    </tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
