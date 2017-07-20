<?php

/**
 * The template display shortcode for room latest reviews.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/shortcodes/best-reviews.php.
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

<div id="hotel_booking_lastest_reviews-<?php echo uniqid(); ?>" class="hotel_booking_lastest_reviews tp-hotel-booking">

    <?php if ( isset( $atts['title'] ) && $atts['title'] ): ?>
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
	<?php endif; ?>

	<?php hotel_booking_room_loop_start(); ?>

	<?php while ( $query->have_posts() ) : $query->the_post(); ?>

		<?php hb_get_template_part( 'content', 'room' ); ?>

	<?php endwhile; ?>

	<?php hotel_booking_room_loop_end(); ?>
</div>