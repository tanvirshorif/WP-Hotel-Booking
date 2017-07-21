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

$field = wp_parse_args(
	$field,
	array(
		'id'          => '',
		'name'        => '',
		'std'         => '',
		'placeholder' => '',
		'editor'      => false
	)
);
if ( $field['editor'] ) {
	wp_editor( $field['std'], $field['name'], array( 'textarea_rows' => 10 ) );
} else {
	printf(
		'<textarea name="%s" id="%s" placeholder="%s">%s</textarea>',
		$field['name'],
		$field['id'],
		$field['placeholder'],
		$field['std']
	);
}

?>