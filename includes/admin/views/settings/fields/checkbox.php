<?php

/**
 * Admin View: Admin setting field - checkbox.
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

<?php $val = hb_settings()->get( $field['id'] ); ?>

<tr valign="top" <?php echo $field['class'] ? 'class="' . $field['class'] . '"' : ''; ?>>
    <th scope="row">
		<?php if ( isset( $field['title'] ) ) { ?>
            <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
				<?php echo esc_html( $field['title'] ) ?>
            </label>
		<?php } ?>
    </th>
    <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
        <input type="hidden" name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>" value="0"/>
        <input type="checkbox" name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"
               value="1" <?php echo $custom_attr ?><?php checked( $val, $field['default'] ); ?>/>
	    <?php if ( isset( $field['desc'] ) ) { ?>
            <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
	    <?php } ?>
    </td>
</tr>
