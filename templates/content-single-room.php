<?php
/**
 * The template for displaying single room content in single room page.
 *
 * Override this template by copying it to yourtheme/wp-hotel-booking/content-single-room.php
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
if ( post_password_required() ) {
	echo get_the_password_form();

	return;
}
?>

<div id="room-<?php the_ID(); ?>" <?php post_class( 'hb_single_room' ); ?>>

	<?php
	/**
	 * hotel_booking_before_loop_room_summary hook
	 *
	 * @hooked hotel_booking_show_room_sale_flash - 10
	 * @hooked hotel_booking_show_room_images - 20
	 */
	do_action( 'hotel_booking_before_single_room' );
	?>

    <div class="summary entry-summary">

		<?php
		/**
		 * hotel_booking_single_room_title hook
		 */
		do_action( 'hotel_booking_single_room_title' );

		/**
		 * hotel_booking_loop_room_single_price
		 */
		do_action( 'hotel_booking_loop_room_price' );

		/**
		 * hotel_booking_single_room_gallery hook
		 */
		do_action( 'hotel_booking_single_room_gallery' );


		/**
		 * @deprecated
		 */
		do_action( 'hotel_booking_single_room_infomation' );

		/**
		 * hotel_booking_single_room_information hook
		 */
		do_action( 'hotel_booking_single_room_information' );
		?>

    </div><!-- .summary -->

	<?php
	/**
	 * hotel_booking_after_loop_room hook
	 *
	 * @hooked hotel_booking_output_room_data_tabs - 10
	 * @hooked hotel_booking_upsell_display - 15
	 * @hooked hotel_booking_output_related_products - 20
	 */
	do_action( 'hotel_booking_after_single_room' );
	?>

</div>
