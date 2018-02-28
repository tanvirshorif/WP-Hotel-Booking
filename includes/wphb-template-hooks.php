<?php
/**
 * WP Hotel Booking Template Hooks
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Hooks
 * @category    Hooks
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
// show message on shortcodes
add_action( 'hotel_booking_wrapper_shortcode_start', 'hb_display_message' );

// enqueue lightbox in search room page
add_action( 'hb_before_search_result', 'hb_enqueue_lightbox_assets' );

// setup global room data variable
add_action( 'the_post', 'hb_setup_room_data' );

// add page body class
add_filter( 'body_class', 'hb_body_class' );

// room per page
add_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 );

// hide pricing plan
add_action( 'hotel_booking_single_room_before_tabs_content_hb_room_pricing_plans', 'hotel_show_pricing' );

// remove old action
remove_action( 'hotel_booking_single_room_information_tabs', array( 'WPHB_Comments', 'addTabReviews' ) );

// print mini cart JS template
add_action( 'wp_footer', 'hb_print_mini_cart_template' );

// parse params from request has encoded in search room page
add_action( 'init', 'hb_parse_request' );

// maybe modify page content
add_filter( 'the_content', 'hb_maybe_modify_page_content' );

// after main content
add_action( 'hotel_booking_after_main_content', 'hotel_booking_after_main_content' );
//thumbnail
add_action( 'hotel_booking_loop_room_thumbnail', 'hotel_booking_loop_room_thumbnail' );
// title
add_action( 'hotel_booking_loop_room_title', 'hotel_booking_room_title' );
add_action( 'hotel_booking_single_room_title', 'hotel_booking_room_title' );
// price display
add_action( 'hotel_booking_loop_room_price', 'hotel_booking_loop_room_price' );
// pagination
add_action( 'hotel_booking_after_room_loop', 'hotel_booking_after_room_loop' );
// gallery
add_action( 'hotel_booking_single_room_gallery', 'hotel_booking_single_room_gallery' );
// room details
add_action( 'hotel_booking_single_room_information', 'hotel_booking_single_room_information' );
// room related
add_action( 'hotel_booking_after_single_product', 'hotel_booking_single_room_related' );
// room rating
add_action( 'hotel_booking_loop_room_rating', 'hotel_booking_loop_room_rating' );


