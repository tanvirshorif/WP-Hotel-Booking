<?php

/**
 * The template for button check availability in archive room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/button.php.
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

<a href="#" data-id="<?php echo esc_attr( get_the_ID() ) ?>" data-name="<?php echo esc_attr( get_the_title() ) ?>"
   class="hb_button hb_primary booking-now check_availability_room" title="<?php _e( 'Book Now', 'wp-hotel-booking-room' ); ?>"><i class="dashicons dashicons-calendar-alt"></i></a>