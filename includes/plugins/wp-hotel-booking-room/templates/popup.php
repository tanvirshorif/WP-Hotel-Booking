<?php

/**
 * The template for search available room from.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/popup.php.
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

<?php global $post; ?>

<div id="book_room_now_popup"></div>
<!--Single search form-->
<script type="text/html" id="tmpl-hb-room-load-form">
    <form action="POST" name="hb-search-single-room"
          class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action hb-room-meta">
        <div class="hb-booking-room-form-header">
            <h2>{{data.name}}</h2>
            <p class="description"><?php _e( 'Please set arrival date and departure date before check available.', 'wp-hotel-booking-room' ); ?></p>
        </div>

        <div class="hb-search-results-form-container">
            <div class="hb-booking-room-form-group">
                <div class="hb-booking-room-form-field hb-form-field-input">
                    <label for="check_in_date"><?php _e( 'Check in date', 'wp-hotel-booking-room' ); ?></label>
                    <input type="text" name="check_in_date" value="{{ data.check_in_date }}"
                           placeholder="<?php _e( 'Arrival Date', 'wp-hotel-booking-room' ); ?>"/>
                </div>
            </div>
            <div class="hb-booking-room-form-group">
                <div class="hb-booking-room-form-field hb-form-field-input">
                    <label for="check_out_date"><?php _e( 'Check out date', 'wp-hotel-booking-room' ); ?></label>
                    <input type="text" name="check_out_date" value="{{ data.check_out_date }}"
                           placeholder="<?php _e( 'Departure Date', 'wp-hotel-booking-room' ); ?>"/>
                </div>
            </div>
        </div>

        <div class="hb-booking-room-form-footer">
            <input type="hidden" name="room-name" value="{{data.name}}"/>
            <input type="hidden" name="room-id" value="{{data.id}}"/>
			<?php wp_nonce_field( 'hb_booking_single_room_check_nonce_action', 'hb-booking-single-room-check-nonce-action' ); ?>
            <input type="hidden" name="check_in_date_text" value=""/>
            <input type="hidden" name="check_out_date_text" value=""/>
            <input type="hidden" name="room-id" value="{{data.id}}"/>
            <input type="hidden" name="action" value="wphb_add_to_cart"/>
            <input type="hidden" name="is_single" value="1"/>
			<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
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
            <label for="hb-num-of-rooms"><?php _e( 'Quantity', 'wp-hotel-booking-room' ); ?></label>
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
