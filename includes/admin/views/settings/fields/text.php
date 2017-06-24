<?php

/**
 * Admin View: Admin setting field - text.
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

<?php $value = hb_settings()->get( $field['id'] ); ?>

<tr valign="top">
    <th scope="row">
		<?php if ( isset( $field['title'] ) ) { ?>
            <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
				<?php echo esc_html( $field['title'] ) ?>
            </label>
			<?php if ( isset( $field['desc'] ) ) { ?>
                <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
			<?php } ?>
		<?php } ?>
    </th>
    <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
        <input
                type="<?php echo esc_attr( $field['type'] ) ?>"
                name="<?php echo esc_attr( $field['id'] ) ?>"
                value="<?php echo esc_attr( $value ) ?>"
                class="regular-text"
                placeholder="<?php echo esc_attr( $field['placeholder'] ) ?>"
			<?php if ( $field['type'] === 'number' ) { ?>

				<?php echo isset( $field['min'] ) && is_numeric( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '' ?>
				<?php echo isset( $field['max'] ) && is_numeric( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : '' ?>
				<?php echo isset( $field['step'] ) ? ' step="' . esc_attr( $field['step'] ) . '"' : '' ?>

			<?php } ?>
        />
    </td>
</tr>
