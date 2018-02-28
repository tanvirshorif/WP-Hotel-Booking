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
