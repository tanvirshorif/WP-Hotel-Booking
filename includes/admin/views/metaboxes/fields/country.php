<?php

/**
 * Admin View: Admin meta box field - country.
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

<?php

$field['selected'] = $field['std'];

hb_dropdown_countries( $field );

?>
