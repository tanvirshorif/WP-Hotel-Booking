<?php

/**
 * Admin View: Admin setting field - radio.
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

<?php $selected = hb_settings()->get( $field['id'], isset( $field['default'] ) ? $field['default'] : '' ); ?>

<tr valign="top">
    <th scope="row">
		<?php if ( isset( $field['title'] ) ) { ?>
            <label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
				<?php echo esc_html( $field['title'] ) ?>
            </label>
		<?php } ?>
    </th>
    <td class="hb-form-field hb-form-field-<?php echo esc_attr( $field['type'] ) ?>">
		<?php if ( isset( $field['options'] ) ) { ?>
			<?php foreach ( $field['options'] as $val => $text ) { ?>
                <label>
                    <input type="radio"
                           name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"<?php selected( $selected, $val ); ?>/><?php echo esc_html( $text ) ?>
                </label>

			<?php } ?>
		<?php } ?>
	    <?php if ( isset( $field['desc'] ) ) { ?>
            <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
	    <?php } ?>
    </td>
</tr>


