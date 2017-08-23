<?php

/**
 * The template for search available room from.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/search-available.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Room/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */


/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
global $post;
if ( ! $post || ! is_single( $post->ID ) || get_post_type( $post->ID ) !== 'hb_room' ) {
	return;
}
?>

<div id="single_booking_room_lightbox"></div>
<!--Single search form-->
<script type="text/html" id="tmpl-hb-room-load-form">

    <form action="POST" name="hb-search-single-room"
          class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action hb-room-meta">

        <div class="hb-booking-room-form-header">
            <h2><?php printf( '%s', $post->post_title ) ?></h2>
            <p class="description"><?php _e( 'Please set arrival date and departure date before check available.', 'wp-hotel-booking-room' ); ?></p>
        </div>

        <div class="hb-search-results-form-container">
            <div class="hb-booking-room-form-group">
                <div class="hb-booking-room-form-field hb-form-field-input">
                    <input type="text" name="check_in_date" value="{{ data.check_in_date }}"
                           placeholder="<?php _e( 'Arrival Date', 'wp-hotel-booking-room' ); ?>"/>
                </div>
            </div>
            <div class="hb-booking-room-form-group">
                <div class="hb-booking-room-form-field hb-form-field-input">
                    <input type="text" name="check_out_date" value="{{ data.check_out_date }}"
                           placeholder="<?php _e( 'Departure Date', 'wp-hotel-booking-room' ); ?>"/>
                </div>
            </div>
        </div>

        <div class="hb-booking-room-form-footer">
            <input type="hidden" name="room-name" value="<?php printf( '%s', $post->post_title ) ?>"/>
            <input type="hidden" name="room-id" value="<?php printf( '%s', $post->ID ) ?>"/>
			<?php wp_nonce_field( 'hb_booking_single_room_check_nonce_action', 'hb-booking-single-room-check-nonce-action' ); ?>
            <input type="hidden" name="check_in_date_text" value=""/>
            <input type="hidden" name="check_out_date_text" value=""/>
            <input type="hidden" name="room-id" value="<?php printf( '%s', $post->ID ) ?>"/>
            <input type="hidden" name="action" value="wphb_add_to_cart"/>
            <input type="hidden" name="is_single" value="1"/>
			<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
			<?php do_action( 'hotel_booking_loop_after_item', $post->ID ); ?>
            <button type="submit"
                    class="hb_button check_available"><?php _e( 'Check Available', 'wp-hotel-booking-room' ); ?></button>
            <button type="submit"
                    class="hb_add_to_cart hb_button"
                    disabled="disabled"><?php _e( 'Add To Cart', 'wp-hotel-booking-room' ); ?></button>
        </div>

    </form>

</script>

<script type="text/html" id="tmpl-hb-room-load-form-cart">

	<?php do_action( 'hotel_booking_room_before_quantity', $post ); ?>
    <# if ( typeof data.qty !== 'undefined' ) { #>
        <div class="hb-booking-room-form-group">
            <div class="hb-booking-room-form-field hb-form-field-input">
                <select name="hb-num-of-rooms" class="number_room_select">
                    <option value="0"><?php _e( 'Select Quantity', 'wphb-booking-room' ); ?></option>
                    <# for( var i = 1; i<= data.qty; i++ ) { #>
                    <option value="{{ i }}">{{ i }}</option>
                    <# } #>
                </select>
            </div>
        </div>
        <# } #>
			<?php do_action( 'hotel_booking_room_after_quantity', $post ); ?>

</script>
