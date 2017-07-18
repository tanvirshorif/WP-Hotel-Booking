<?php

/**
 * Admin View: Admin setting field - select and multiple select.
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

<?php $selected = hb_settings()->get( $field['id'], isset( $field['default'] ) ? $field['default'] : array() ); ?>

<tr valign="top" <?php echo $field['class'] ? 'class="' . $field['class'] . '"' : ''; ?>>
    <th scope="row">
		<?php if ( isset( $field['title'] ) ) { ?>
            <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
				<?php echo esc_html( $field['title'] ) ?>
            </label>
		<?php } ?>
    </th>
    <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
		<?php if ( isset( $field['options'] ) ) { ?>
			<?php
			$name     = isset( $field['id'] ) ? esc_attr( $field['id'] ) : '';
			$type     = $field['type'] === 'multiselect' ? '[]' : '';
			$id       = isset( $field['id'] ) ? esc_attr( $field['id'] ) : '';
			$multiple = $field['type'] === 'multiple' ? ' multiple="multiple"' : '';
			?>
            <label for="<?php esc_attr_e( $id ); ?>"></label>
            <select name="<?php esc_attr_e( $name . $type ) ?>"
                    id="<?php esc_attr_e( $id ); ?>" <?php esc_attr_e( $multiple ); ?>>
				<?php foreach ( $field['options'] as $val => $text ) { ?>
                    <option value="<?php echo esc_attr( $val ) ?>"
						<?php echo ( is_array( $selected ) && in_array( $val, $selected ) ) || $selected === $val ? ' selected' : '' ?> >
						<?php echo esc_html( $text ) ?>
                    </option>
				<?php } ?>
            </select>
		<?php } ?>
		<?php if ( isset( $field['desc'] ) ) { ?>
            <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
		<?php } ?>
    </td>
</tr>
