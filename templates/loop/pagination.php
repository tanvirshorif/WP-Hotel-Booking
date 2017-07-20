<?php

/**
 * The template for displaying page pagination in in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/pagination.php.
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

<?php global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

?>
<nav class="rooms-pagination">
	<?php
	echo paginate_links( apply_filters( 'hb_pagination_args', array(
		'base'      => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
		'format'    => '',
		'add_args'  => '',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $wp_query->max_num_pages,
		'prev_text' => __( 'Previous', 'wp-hotel-booking' ),
		'next_text' => __( 'Next', 'wp-hotel-booking' ),
		'type'      => 'list',
		'end_size'  => 3,
		'mid_size'  => 3
	) ) );
	?>
</nav>
