<?php

/**
 * Admin View: Admin setting field - section end.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'hotel_booking_setting_field_' . $field['id'] . '_end' ); ?>

</table>

<?php do_action( 'hotel_booking_setting_field_' . $field['id'] . '_after' ); ?>
