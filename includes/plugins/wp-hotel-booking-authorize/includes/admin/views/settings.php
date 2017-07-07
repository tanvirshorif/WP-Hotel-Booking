<?php
/**
 * Admin View: Authorize payment gateway setting page.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Authorize/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$settings  = WPHB_Settings::instance();
$authorize = $settings->get( 'authorize' );
$authorize = wp_parse_args(
	$authorize, array(
		'enable'          => 'on',
		'sandbox'         => 'off',
		'api_login_id'    => '',
		'transaction_key' => ''
	)
);

$field_name = $settings->get_field_name( 'authorize' );
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable', 'wphb-authorize-payment' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[enable]" value="off"/>
            <input type="checkbox"
                   name="<?php echo esc_attr( $field_name ); ?>[enable]" <?php checked( $authorize['enable'] == 'on' ? 1 : 0, 1 ); ?>
                   value="on"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Sandbox Mode', 'wphb-authorize-payment' ); ?></th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[sandbox]" value="off"/>
            <input type="checkbox"
                   name="<?php echo esc_attr( $field_name ); ?>[sandbox]" <?php checked( $authorize['sandbox'] == 'on' ? 1 : 0, 1 ); ?>
                   value="on"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'API Login ID', 'wphb-authorize-payment' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[api_login_id]"
                   value="<?php echo esc_attr( $authorize['api_login_id'] ); ?>"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Transaction Key', 'wphb-authorize-payment' ); ?></th>
        <td>
            <input type="text" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[transaction_key]"
                   value="<?php echo esc_attr( $authorize['transaction_key'] ); ?>"/>
        </td>
    </tr>
</table>
