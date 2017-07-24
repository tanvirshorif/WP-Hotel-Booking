<?php

/**
 * Admin View: Booking actions.
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

global $post;
$booking = WPHB_Booking::instance( $post->ID );
?>

<div class="submitbox">
    <ul>
        <li>
            <label><?php _e( 'Booking Status:', 'wp-hotel-booking' ); ?></label>
            <select name="_hb_booking_status" v-model="status" @change="update_desc()">
				<?php $hb_status = hb_get_booking_statuses(); ?>
				<?php foreach ( $hb_status as $key => $status ) :
					echo '<option data-desc="' . esc_attr( wphb_booking_status_description( $key ) ) . '" value="' . esc_attr( $key ) . '" ' . selected( $key, $post->post_status, false ) . '>' . esc_html( $status ) . '</option>';
					?>
				<?php endforeach; ?>
            </select>
            {{message}}
        </li>
        <li>
            <label><?php _e( 'Customer:', 'wp-hotel-booking' ); ?></label>
            <div class="customer_details">
                <select name="_hb_user_id" id="_hb_user_id">
					<?php if ( $booking->user_id ) { ?>
						<?php $user = get_userdata( $booking->user_id ); ?>
                        <option value="<?php echo esc_attr( $booking->user_id ) ?>"
                                selected><?php printf( '%s(#%s %s)', $user->user_login, $booking->user_id, $user->user_email ) ?></option>
					<?php } ?>
                </select>
            </div>
        </li>
    </ul>
    <div id="delete-action">
		<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
            <a class="submitdelete deletion"
               href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ) ?>"><?php _e( 'Move to Trash', 'wp-hotel-booking' ); ?></a>
		<?php endif; ?>
    </div>
    <div id="publishing-action">
        <button name="save" type="submit" class="button button-primary" id="publish">
			<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'wp-hotel-booking' ) : __( 'Save Book', 'wp-hotel-booking' ) ) ?>
        </button>
    </div>
</div>
