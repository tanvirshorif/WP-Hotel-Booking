<?php

/**
 * WP Hotel Booking Calendar functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking_Calendar/Functions
 * @category    Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php

if ( ! function_exists( 'wphb_calendar_template_path' ) ) {

	/**
	 * Template path.
	 *
	 * @return string
	 */
	function wphb_calendar_template_path() {
		return apply_filters( 'hb_calendar_addon_template_path', 'wp-hotel-booking-calendar' );
	}

}

if ( ! function_exists( 'wphb_calendar_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *        yourtheme        /    $template_path    /    $template_name
	 *        yourtheme        /    $template_name
	 *        $default_path    /    $template_name
	 *
	 * @access public
	 *
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 *
	 * @return string
	 */
	function wphb_calendar_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = wphb_calendar_template_path();
		}

		if ( ! $default_path ) {
			$default_path = WPHB_CALENDAR_ABSPATH . '/templates/';
		}

		$template = null;
		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'hb_calendar_locate_template', $template, $template_name, $template_path );
	}

}

if ( ! function_exists( 'wphb_calendar_get_template_part' ) ) {

	/**
	 * Get template part.
	 *
	 * @param $slug
	 * @param string $name
	 *
	 * @return mixed|string
	 */
	function wphb_calendar_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/wp-hotel-booking-calendar/slug-name.php
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				wphb_calendar_template_path() . "/{$slug}-{$name}.php"
			) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( WPHB_CALENDAR_ABSPATH . "/templates/{$slug}-{$name}.php" ) ) {
			$template = WPHB_CALENDAR_ABSPATH . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wp-hotel-booking-calendar/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", $this->template_path() . "{$slug}.php" ) );
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'hb_calendar_addon_get_template_part', $template, $slug, $name );
		}
		if ( $template && file_exists( $template ) ) {
			load_template( $template, false );
		}

		return $template;
	}
}


if ( ! function_exists( 'wphb_calendar_get_template' ) ) {

	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name
	 * @param array $args (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 *
	 * @return void
	 */
	function wphb_calendar_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = wphb_calendar_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );

			return;
		}
		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'hb_calendar_addon_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'hb_calendar_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'hb_calendar_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'wphb_calendar_locate_template' ) ) {
	/**
	 * Locate template.
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return mixed
	 */
	function wphb_calendar_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = wphb_calendar_template_path();
		}

		if ( ! $default_path ) {
			$default_path = WPHB_CALENDAR_ABSPATH . '/templates/';
		}

		$template = null;
		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'wphb_calendar_locate_template', $template, $template_name, $template_path );
	}
}
add_action( 'hotel_booking_single_room_before_tabs_content_hb_room_calendar', 'wphb_calendar_get_single_room_calendar' );

if ( ! function_exists( 'wphb_calendar_get_single_room_calendar' ) ) {
	/**
	 * Get bookings calendar in single room page.
	 */
	function wphb_calendar_get_single_room_calendar() {
		global $post;
		wphb_calendar_get_template( 'loop/calendar.php', array( 'post_id' => $post->ID ) );
	}
}

if ( ! function_exists( 'wphb_calendar_get_room_bookings' ) ) {

	/**
	 * Get all bookings by room id.
	 *
	 * @param $room_id
	 *
	 * @return array
	 */
	function wphb_calendar_get_room_bookings( $room_id ) {
		global $wpdb;

		$query = $wpdb->prepare( "
			SELECT check_in.meta_value AS check_in, check_out.meta_value AS check_out, booking.ID AS id, qty.meta_value AS qty FROM {$wpdb->hotel_booking_order_itemmeta} AS product_id
				LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_in ON product_id.hotel_booking_order_item_id = check_in.hotel_booking_order_item_id AND check_in.meta_key = %s
				LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_out ON product_id.hotel_booking_order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
				LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS qty ON product_id.hotel_booking_order_item_id = qty.hotel_booking_order_item_id AND qty.meta_key = %s
				LEFT JOIN {$wpdb->hotel_booking_order_items} AS booking_items ON product_id.hotel_booking_order_item_id = booking_items.order_item_id
				LEFT JOIN {$wpdb->posts} AS booking on booking_items.order_id = booking.ID
			WHERE
				booking.post_type = %s 
		  		AND booking.post_status IN ( %s, %s, %s)
		  		AND product_id.meta_key = %s
		  		AND product_id.meta_value = %d
			", 'check_in_date', 'check_out_date', 'qty', 'hb_booking', 'hb-completed', 'hb-pending', 'hb-processing', 'product_id', $room_id );

		$query = apply_filters( 'hb_calendar_booking_query', $query );

		$calendar_data = array();

		if ( $bookings = $wpdb->get_results( $query, ARRAY_A ) ) {
			foreach ( $bookings as $key => $booking ) {
				if ( ! $booking_id = $booking['id'] ) {
					return $calendar_data;
				}

				for ( $i = 0; $i < $booking['qty']; $i ++ ) {
					$calendar_data[] = array(
						'title' => get_post_meta( $booking_id, '_hb_customer_first_name', true ) . ' ' . get_post_meta( $booking_id, '_hb_customer_last_name', true ) . ' #' . $booking_id . ' #' . ( $i + 1 ),
						'start' => date( hb_get_date_format(), $booking['check_in'] ),
						'end'   => date( hb_get_date_format(), $booking['check_out'] ),
						'color' => hb_random_color()
					);
				}
			}
		}

		return $calendar_data;
	}
}