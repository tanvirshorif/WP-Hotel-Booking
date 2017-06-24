<?php

/**
 * Admin View: Admin setting field - image size.
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
$width  = hb_settings()->get( $field['id'] . '_width', isset( $field['default']['width'] ) ? $field['default']['width'] : 270 );
$height = hb_settings()->get( $field['id'] . '_height', isset( $field['default']['height'] ) ? $field['default']['height'] : 270 );
?>

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
			<?php if ( isset( $field['id'] ) && isset( $field['options'] ) ) { ?>

				<?php if ( isset( $field['options']['width'] ) ) { ?>
                    <input type="number" name="<?php echo esc_attr( $field['id'] ) ?>_width"
                           value="<?php echo esc_attr( $width ) ?>"
                    /> x
				<?php } ?>
				<?php if ( isset( $field['options']['height'] ) ) { ?>
                    <input type="number" name="<?php echo esc_attr( $field['id'] ) ?>_height"
                           value="<?php echo esc_attr( $height ) ?>"
                    /> px
				<?php } ?>

			<?php } ?>
        </td>
    </tr>
<?php
