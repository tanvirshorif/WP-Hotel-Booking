<?php
/**
 * WP Hotel Booking Core Hooks
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
//  show notice required remove tp hotel booking plugin and add-ons
if ( is_multisite() ) {
	if ( file_exists( ABSPATH . 'wp-content/plugins/tp-hotel-booking/tp-hotel-booking.php' ) && ! get_site_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'network_admin_notices', 'hb_notice_remove_hotel_booking' );
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
} else {
	if ( file_exists( ABSPATH . 'wp-content/plugins/tp-hotel-booking/tp-hotel-booking.php' ) && ! get_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
}

// register widget
add_action( 'widgets_init', 'hotel_booking_widget_init' );

// set table name
add_action( 'init', 'hotel_booking_set_table_name', 0 );
add_action( 'switch_blog', 'hotel_booking_set_table_name', 0 );

// set up page content
add_filter( 'the_content', 'hb_setup_page_content' );

// footer advertisement
add_action( 'admin_footer', 'hb_footer_advertisement', - 10 );

// add room meta box
add_action( 'admin_init', 'hb_admin_room_meta_boxes', 50 );
// init other meta boxes
add_action( 'admin_init', 'hb_admin_init_metaboxes', 50 );

// query request for booking
add_filter( 'request', 'hb_request_query' );

// change booking title in admin archive booking page
add_action( 'admin_head-edit.php', 'hb_edit_post_change_title_in_list' );

// admin script
add_action( 'admin_print_scripts', 'hb_admin_js_template' );

// remove revolution slider meta boxes
add_action( 'do_meta_boxes', 'hb_remove_revolution_slider_meta_boxes' );

// update booking
add_action( 'hb_booking_detail_update_meta_box', 'hb_booking_detail_update_meta_box', 10, 3 );

// update room gallery meta box
add_action( 'hb_update_meta_box_gallery_settings', 'hb_update_meta_box_gallery' );








