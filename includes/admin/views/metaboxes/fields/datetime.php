<?php

/**
 * Admin View: Admin meta box field - datetime.
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

$field = wp_parse_args(
	$field,
	array(
		'id'          => '',
		'name'        => '',
		'std'         => '',
		'placeholder' => '',
		'attr'        => '',
		'filter'      => ''
	)
);

$field_attr = '';

if ( $field['attr'] ) {
	if ( is_array( $field['attr'] ) ) {
		$field_attr = join( " ", $field['attr'] );
	} else {
		$field_attr = $field['attr'];
	}
}
$value = $field['std'];
if ( is_callable( $field['filter'] ) ) {
	$value = call_user_func_array( $field['filter'], array( $value ) );
}
printf( '<input type="text" class="datetime-picker-metabox" id="%s" name="%s" value="%s" %s />',
	$field['id'],
	$field['name'],
	$value,
	$field_attr
);

?>