<?php

/**
 * The template for displaying search room results.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/results.php.
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

do_action( 'hb_before_search_result' );

global $hb_search_rooms;
?>

<div id="hotel-booking-results">
	<?php if ( $results && ! empty( $hb_search_rooms['data'] ) ): ?>
        <h3><?php _e( 'Search results', 'wp-hotel-booking' ); ?></h3>
        <ul class="hb-search-results">
			<?php hb_get_template( 'search/room.php', array(
				'results' => $hb_search_rooms['data'],
				'atts'    => $atts
			) ); ?>
        </ul>
        <nav class="rooms-pagination">
			<?php
			echo paginate_links( apply_filters( 'hb_pagination_args', array(
				'base'      => add_query_arg( 'hb_page', '%#%' ),
				'format'    => '',
				'prev_text' => __( 'Previous', 'wp-hotel-booking' ),
				'next_text' => __( 'Next', 'wp-hotel-booking' ),
				'total'     => $hb_search_rooms['max_num_pages'],
				'current'   => $hb_search_rooms['page'],
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3
			) ) );
			?>
        </nav>
	<?php else: ?>
        <p><?php _e( 'No room found.', 'wp-hotel-booking' ); ?></p>
        <p>
            <a href="<?php echo hb_get_url(); ?>"><?php _e( 'Search again!', 'wp-hotel-booking' ); ?></a>
        </p>
	<?php endif; ?>
</div>

<?php do_action( 'hb_after_search_result' ); ?>
