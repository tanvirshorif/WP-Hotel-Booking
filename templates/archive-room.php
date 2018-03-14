<?php
/**
 * The Template for displaying archive room page.
 *
 * Override this template by copying it to yourtheme/wp-hotel-booking/archive-room.php
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

<?php get_header(); ?>
    <div id="primary" class="content-area">
		<?php
		/**
		 * hotel_booking_before_main_content hook
		 *
		 * @hooked hotel_booking_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked hotel_booking_breadcrumb - 20
		 */
		do_action( 'hotel_booking_before_main_content' );
		?>

		<?php
		/**
		 * hotel_booking_archive_description hook
		 */
		do_action( 'hotel_booking_archive_description' );
		?>

		<?php if ( have_posts() ) : ?>

			<?php
			/**
			 * hotel_booking_before_room_loop hook
			 *
			 * @hooked hotel_booking_result_count - 20
			 * @hooked hotel_booking_catalog_ordering - 30
			 */
			do_action( 'hotel_booking_before_room_loop' );
			?>

			<?php hotel_booking_room_loop_start(); ?>


            <!-- filter room by price-->
			<?php
			$price = hb_get_min_max_rooms_price();
			$min   = hb_get_request( 'min_price', $price['min'] );
			$max   = hb_get_request( 'max_price', $price['max'] );

			if ( $min && $max ) {
				$filter = array( 'min' => $min, 'max' => $max );
			} ?>

			<?php while ( have_posts() ) : the_post();

				$prices = hb_get_rooms_price();
				if ( $prices[ get_the_ID() ]['max'] <= $max && $prices[ get_the_ID() ]['min'] >= $min ) {
					hb_get_template_part( 'content', 'room' );
				}

			endwhile; // end of the loop. ?>

			<?php hotel_booking_room_loop_end(); ?>

			<?php
			/**
			 * hotel_booking_after_room_loop hook
			 *
			 * @hooked hotel_booking_pagination - 10
			 */
			do_action( 'hotel_booking_after_room_loop' );
			?>

		<?php endif; ?>

		<?php
		/**
		 * hotel_booking_after_main_content hook
		 *
		 * @hooked hotel_booking_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'hotel_booking_after_main_content' );
		?>

		<?php
		/**
		 * hotel_booking_sidebar hook
		 *
		 * @hooked hotel_booking_get_sidebar - 10
		 */
		do_action( 'hotel_booking_sidebar' );
		?>
    </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>