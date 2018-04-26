<?php

/**
 * Admin View: Admin setting field - textarea.
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
if ( $name = substr( $field['id'], strpos( $field['id'], '[' ) + 1, ( strpos( $field['id'], ']' ) - strpos( $field['id'], '[' ) - 1 ) ) ) {
	$option = substr( $field['id'], 0, strpos( $field['id'], '[' ) );
	$value  = get_option( $option );
	if ( $value ) {
		$content = $value[ $name ];
	} else {
		$content = isset( $field['default'] ) ? $field['default'] : '';
	}
} else {
	$content = ( get_option( $field['id'] ) !== false ) ? get_option( $field['id'] ) : ( isset( $field['default'] ) ? $field['default'] : '' );
} ?>

<tr valign="top" <?php echo $field['class'] ? 'class="' . $field['class'] . '"' : ''; ?>>
    <th scope="row">
		<?php if ( isset( $field['title'] ) ) { ?>
            <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
				<?php echo esc_html( $field['title'] ) ?>
            </label>
		<?php } ?>
    </th>
    <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
		<?php if ( isset( $field['id'] ) ) { ?>
			<?php wp_editor( $content, $field['id'], isset( $field['options'] ) ? $field['options'] : array() ); ?>
		<?php } ?>
		<?php if ( isset( $field['desc'] ) ) { ?>
            <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
		<?php } ?>
    </td>
</tr>
