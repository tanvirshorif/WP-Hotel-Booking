<?php

/**
 * Admin View: Admin meta box field - gallery.
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
$meta_value      = get_post_meta( $post->ID, $field['name'], true );
$upload_dir      = wp_upload_dir();
$upload_base_url = $upload_dir['baseurl'];
?>

<div class="hb-form-field-input room-gallery-input">
    <ul>
		<?php if ( $meta_value ) { ?>
			<?php foreach ( $meta_value as $key => $id ) { ?>
				<?php if ( $id ) { ?>
                    <li class="attachment">
                        <div class="attachment-preview">
                            <div class="thumbnail">
                                <div class="centered">
									<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
                                    <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[]"
                                           value="<?php echo esc_attr( $id ); ?>"/>
                                </div>
                            </div>
                        </div>
                        <a class="dashicons dashicons-trash"
                           title="<?php _e( 'Remove this image', 'wp-hotel-booking' ); ?>"></a>
                    </li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
        <li class="attachment add-new">
            <div class="attachment-preview">
                <div class="thumbnail">
                    <div class="dashicons-plus dashicons">
                        <input type="hidden" name="_hb_gallery[]" value=''>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>