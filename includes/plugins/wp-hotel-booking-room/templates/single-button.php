<?php

/**
 * The template for button check availability in single room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/single-button.php.
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

<?php global $hb_room; ?>

<a href="#" data-id="<?php echo esc_attr( $hb_room->ID ) ?>" data-name="<?php echo esc_attr( $hb_room->name ) ?>"
   class="hb_button hb_primary check_availability_room"><?php _e( 'Book Now', 'wp-hotel-booking-room' ); ?></a>