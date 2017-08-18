<?php
/**
 * Admin View: Offline payment payment gateway setting page.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Stripe/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$settings = WPHB_Settings::instance();
$payment  = $settings->get( 'offline-payment' );
$payment  = wp_parse_args( $payment, array( 'enable' => 'on' ) );

$field_name = $settings->get_field_name( 'offline-payment' );
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'wp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[enable]" value="off"/>
            <input type="checkbox"
                   name="<?php echo esc_attr( $field_name ); ?>[enable]" <?php checked( $payment['enable'] == 'on' ? 1 : 0, 1 ); ?>
                   value="on"/>
            <p class="description"><?php echo __( 'Enable Offline payment gateway', 'wp-hotel-booking' ); ?></p>
        </td>
    </tr>
</table>