<?php
/**
 * Admin View: Paypal payment gateway setting page.
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
$paypal   = $settings->get( 'paypal' );
$paypal   = wp_parse_args(
	$paypal,
	array(
		'enable'        => 'on',
		'email'         => '',
		'sandbox'       => 'off',
		'sandbox_email' => ''
	)
);

$field_name = $settings->get_field_name( 'paypal' );
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'wp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[enable]" value="off"/>
            <input type="checkbox"
                   name="<?php echo esc_attr( $field_name ); ?>[enable]" <?php checked( $paypal['enable'] == 'on' ? 1 : 0, 1 ); ?>
                   value="on"/>
            <p class="description"><?php echo __( 'Enable Paypal gateway', 'wp-hotel-booking' ); ?></p>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Paypal email', 'wp-hotel-booking' ); ?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[email]"
                   value="<?php echo esc_attr( $paypal['email'] ); ?>"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Sandbox Mode', 'wp-hotel-booking' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[sandbox]" value="off"/>
            <input type="checkbox"
                   name="<?php echo esc_attr( $field_name ); ?>[sandbox]" <?php checked( $paypal['sandbox'] == 'on' ? 1 : 0, 1 ); ?>
                   value="on"/>
            <p class="description"><?php echo __( 'Enable Paypal Sandbox gateway', 'wp-hotel-booking' ); ?></p>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Paypal sandbox email', 'wp-hotel-booking' ); ?></th>
        <td>
            <input type="email" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[sandbox_email]"
                   value="<?php echo esc_attr( $paypal['sandbox_email'] ); ?>"/>
        </td>
    </tr>
</table>

