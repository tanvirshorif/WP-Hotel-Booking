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

<?php $selected = hb_settings()->get( $field['id'], 0 ); ?>

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
			<?php
			hb_dropdown_pages(
				array(
					'show_option_none'  => __( '---Select page---', 'wp-hotel-booking' ),
					'option_none_value' => 0,
					'name'              => $field['id'],
					'selected'          => $selected
				)
			);
			?>
		<?php } ?>
	    <?php if ( isset( $field['desc'] ) ) { ?>
            <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
	    <?php } ?>
    </td>
</tr>
