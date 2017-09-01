<?php

/**
 * WP Hotel Booking core booking functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'hb_create_booking' ) ) {
	/**
	 * Create new booking.
	 *
	 * @param array $booking_info
	 * @param array $order_items
	 *
	 * @return int|WP_Error
	 */
	function hb_create_booking( $booking_info = array(), $order_items = array() ) {
		$cart = WPHB_Cart::instance();
		if ( $cart->cart_items_count === 0 ) {
			return new WP_Error( 'hotel_booking_cart_empty', __( 'Your cart is empty.', 'wp-hotel-booking' ) );
		}

		$args = array(
			'status'        => '',
			'user_id'       => get_current_user_id(),
			'customer_note' => null,
			'booking_id'    => 0,
			'parent'        => 0
		);

		// instance empty pending booking
		$booking = WPHB_Booking::instance( $args['booking_id'] );

		$booking->post->post_title   = sprintf( __( 'Booking ', 'wp-hotel-booking' ) );
		$booking->post->post_content = hb_get_request( 'addition_information' ) ? hb_get_request( 'addition_information' ) : __( 'Empty Booking Notes', 'wp-hotel-booking' );
		$booking->post->post_status  = 'hb-' . apply_filters( 'hb_default_order_status', 'pending' );

		if ( $args['status'] ) {
			if ( ! in_array( 'hb-' . $args['status'], array_keys( hb_get_booking_statuses() ) ) ) {
				return new WP_Error( 'hb_invalid_booking_status', __( 'Invalid booking status', 'wp-hotel-booking' ) );
			}
			$booking->post->post_status = 'hb-' . $args['status'];
		}

		$booking_info['_hb_booking_key'] = apply_filters( 'hb_generate_booking_key', uniqid() );

		// update booking info
		$booking->set_booking_info( $booking_info );

		$booking_id = $booking->update( $order_items );

		// set session booking id
		$cart->set_booking( 'booking_id', $booking_id );

		// do action
		do_action( 'hotel_booking_create_booking', $booking_id, $booking_info, $order_items );

		return $booking_id;
	}
}

/**
 * Gets all statuses that room supported
 *
 * @return array
 */
if ( ! function_exists( 'hb_get_booking_statuses' ) ) {

	function hb_get_booking_statuses() {
		$booking_statuses = array(
			'hb-pending'    => _x( 'Pending', 'Booking status', 'wp-hotel-booking' ),
			'hb-cancelled'  => _x( 'Cancelled', 'Booking status', 'wp-hotel-booking' ),
			'hb-processing' => _x( 'Processing', 'Booking status', 'wp-hotel-booking' ),
			'hb-completed'  => _x( 'Completed', 'Booking status', 'wp-hotel-booking' ),
		);

		return apply_filters( 'hb_booking_statuses', $booking_statuses );
	}
}

if ( ! function_exists( 'hb_get_order_items' ) ) {
	function hb_get_order_items( $order_id = null, $item_type = 'line_item', $parent = null ) {
		global $wpdb;

		if ( ! $parent ) {
			$query = $wpdb->prepare( "
                    SELECT booking.* FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                    WHERE post.ID = %d
                        AND booking.order_item_type = %s
                ", $order_id, $item_type );
		} else {
			$query = $wpdb->prepare( "
                    SELECT booking.* FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                    WHERE post.ID = %d
                        AND booking.order_item_type = %s
                        AND booking.order_item_parent = %d
                ", $order_id, $item_type, $parent );
		}

		return $wpdb->get_results( $query );
	}
}

// insert order item
if ( ! function_exists( 'hb_add_order_item' ) ) {
	function hb_add_order_item( $booking_id = null, $param = array() ) {
		global $wpdb;

		$booking_id = absint( $booking_id );

		if ( ! $booking_id ) {
			return false;
		}

		$defaults = array(
			'order_item_name' => '',
			'order_item_type' => 'line_item',
		);

		$param = wp_parse_args( $param, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'hotel_booking_order_items',
			array(
				'order_item_name'   => $param['order_item_name'],
				'order_item_type'   => $param['order_item_type'],
				'order_item_parent' => isset( $param['order_item_parent'] ) ? $param['order_item_parent'] : null,
				'order_id'          => $booking_id
			),
			array(
				'%s',
				'%s',
				'%d',
				'%d'
			)
		);

		$item_id = absint( $wpdb->insert_id );

		do_action( 'hotel_booking_new_order_item', $item_id, $param, $booking_id );

		return $item_id;
	}
}

// update order item
if ( ! function_exists( 'hb_update_order_item' ) ) {
	function hb_update_order_item( $item_id = null, $param = array() ) {
		global $wpdb;

		$update = $wpdb->update( $wpdb->prefix . 'hotel_booking_order_items', $param, array( 'order_item_id' => $item_id ) );

		if ( false === $update ) {
			return false;
		}

		do_action( 'hotel_booking_update_order_item', $item_id, $param );

		return true;
	}
}

if ( ! function_exists( 'hb_remove_order_item' ) ) {
	function hb_remove_order_item( $order_item_id = null ) {
		global $wpdb;

		$wpdb->delete( $wpdb->hotel_booking_order_items, array(
			'order_item_id' => $order_item_id
		), array( '%d' ) );


		$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array(
			'hotel_booking_order_item_id' => $order_item_id
		), array( '%d' ) );

		do_action( 'hotel_booking_remove_order_item', $order_item_id );
	}
}

if ( ! function_exists( 'hb_get_parent_order_item' ) ) {
	function hb_get_parent_order_item( $order_item_id = null ) {
		global $wpdb;
		$query = $wpdb->prepare( "
                SELECT order_item.order_item_parent FROM $wpdb->hotel_booking_order_items AS order_item
                WHERE
                    order_item.order_item_id = %d
                    LIMIT 1
            ", $order_item_id );

		return $wpdb->get_var( $query );
	}
}

if ( ! function_exists( 'hb_get_sub_item_order_item_id' ) ) {
	function hb_get_sub_item_order_item_id( $order_item_id = null ) {
		global $wpdb;
		$query = $wpdb->prepare( "
                SELECT order_item.order_item_id FROM $wpdb->hotel_booking_order_items AS order_item
                WHERE
                    order_item.order_item_parent = %d
            ", $order_item_id );

		return $wpdb->get_col( $query );
	}
}

if ( ! function_exists( 'hb_empty_booking_order_items' ) ) {
	function hb_empty_booking_order_items( $booking_id = null ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
                DELETE hb_order_item, hb_order_itemmeta
                    FROM $wpdb->hotel_booking_order_items as hb_order_item
                    LEFT JOIN $wpdb->hotel_booking_order_itemmeta as hb_order_itemmeta ON hb_order_item.order_item_id = hb_order_itemmeta.hotel_booking_order_item_id
                WHERE
                    hb_order_item.order_id = %d
            ", $booking_id );

		return $wpdb->query( $sql );
	}
}

if ( ! function_exists( 'hb_add_order_item_meta' ) ) {
	/**
	 * Add booking item meta.
	 *
	 * @param null $item_id
	 * @param null $meta_key
	 * @param null $meta_value
	 * @param bool $unique
	 *
	 * @return false|int
	 */
	function hb_add_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $unique = false ) {
		return add_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $unique );
	}
}


if ( ! function_exists( 'hb_update_order_item_meta' ) ) {
	/**
	 * Update booking item meta.
	 *
	 * @param null $item_id
	 * @param null $meta_key
	 * @param null $meta_value
	 * @param bool $prev_value
	 *
	 * @return bool|int
	 */
	function hb_update_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $prev_value = false ) {
		return update_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $prev_value );
	}
}

if ( ! function_exists( 'hb_get_order_item_meta' ) ) {

	/**
	 * Get booking item meta.
	 *
	 * @param null $item_id
	 * @param null $key
	 * @param bool $single
	 *
	 * @return mixed
	 */
	function hb_get_order_item_meta( $item_id = null, $key = null, $single = true ) {
		return get_metadata( 'hotel_booking_order_item', $item_id, $key, $single );
	}
}

// delete order item meta
if ( ! function_exists( 'hb_delete_order_item_meta' ) ) {

	function hb_delete_order_item_meta( $item_id = null, $meta_key = null, $meta_value = '', $delete_all = false ) {
		return delete_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $delete_all );
	}
}

// get sub total booking
if ( ! function_exists( 'hb_booking_subtotal' ) ) {

	function hb_booking_subtotal( $booking_id = null ) {
		if ( ! $booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->sub_total();
	}
}

// get total booking
if ( ! function_exists( 'hb_booking_total' ) ) {

	function hb_booking_total( $booking_id = null ) {
		if ( ! $booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->total();
	}
}
// get total booking
if ( ! function_exists( 'hb_booking_tax_total' ) ) {

	function hb_booking_tax_total( $booking_id = null ) {
		if ( ! $booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->tax_total();
	}
}

/**
 * Checks to see if a user is booked room
 *
 * @param string $customer_email
 * @param int $room_id
 *
 * @return bool
 */
if ( ! function_exists( 'hb_customer_booked_room' ) ) {

	function hb_customer_booked_room( $room_id ) {
		return apply_filters( 'hb_customer_booked_room', true, $room_id );
	}
}

if ( ! function_exists( 'hb_get_booking_id_by_key' ) ) {

	function hb_get_booking_id_by_key( $booking_key ) {
		global $wpdb;

		$booking_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_hb_booking_key' AND meta_value = %s", $booking_key ) );

		return $booking_id;
	}
}

if ( ! function_exists( 'hb_get_booking_status_label' ) ) {

	function hb_get_booking_status_label( $booking_id ) {
		$statuses = hb_get_booking_statuses();
		if ( is_numeric( $booking_id ) ) {
			$status = get_post_status( $booking_id );
		} else {
			$status = $booking_id;
		}

		return ! empty( $statuses[ $status ] ) ? $statuses[ $status ] : __( 'Cancelled', 'wp-hotel-booking' );
	}
}

if ( ! function_exists( 'hb_booking_get_check_in_date' ) ) {
	// get min check in date of booking order
	function hb_booking_get_check_in_date( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );
		$data        = array();
		foreach ( $order_items as $item ) {
			$data[] = hb_get_order_item_meta( $item->order_item_id, 'check_in_date', true );
		}
		sort( $data );

		return array_shift( $data );

	}
}

if ( ! function_exists( 'hb_booking_get_check_out_date' ) ) {
	// get min check in date of booking order
	function hb_booking_get_check_out_date( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );
		$data        = array();
		foreach ( $order_items as $item ) {
			$data[] = hb_get_order_item_meta( $item->order_item_id, 'check_out_date', true );
		}
		sort( $data );

		return array_pop( $data );

	}
}

//========================================== Email Booking functions start ===========================================//

if ( ! function_exists( 'hb_send_booking_mail' ) ) {
	/**
	 * Send email for booking processes.
	 *
	 * @param null $booking
	 * @param null $to
	 * @param string $subject
	 * @param string $heading
	 * @param string $desc
	 *
	 * @return bool
	 */
	function hb_send_booking_mail( $booking = null, $to = null, $subject = '', $heading = '', $desc = '' ) {
		if ( ! ( $booking || $to ) ) {
			return false;
		}

		$settings = hb_settings();

		$format  = $settings->get( 'email_general_format', 'html' );
		$headers = "Content-Type: " . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";

		add_filter( 'wp_mail_from', 'hb_set_mail_from' );
		add_filter( 'wp_mail_from_name', 'hb_set_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$email = hb_get_template_content( 'emails/booking/booking-email.php', array(
			'booking'     => $booking,
			'heading'     => $heading,
			'description' => $desc
		) );

		if ( ! $email ) {
			return false;
		}

		$send = wp_mail( $to, $subject, $email, $headers );

		remove_filter( 'wp_mail_from', 'hb_set_mail_from' );
		remove_filter( 'wp_mail_from_name', 'hb_set_mail_from_name' );
		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		return $send;
	}
}

if ( ! function_exists( 'hb_send_admin_booking_email' ) ) {
	/**
	 * Send email for admin when booking completed.
	 *
	 * @param null $booking
	 * @param $status
	 *
	 * @return bool
	 */
	function hb_send_admin_booking_email( $booking = null, $status ) {

		$settings = hb_settings();

		if ( 'booking_completed' == $status ) {
			$to      = $settings->get( 'email_booking_completed_recipients', get_option( 'admin_email' ) );
			$subject = $settings->get( 'email_booking_completed_subject', '[{site_title}] Reservation completed ({booking_number}) - {booking_date}' );

			$heading = $settings->get( 'email_booking_completed_heading', __( 'Booking completed', 'wp-hotel-booking' ) );
			$desc    = $settings->get( 'email_booking_completed_heading_desc', __( 'The customer had completed the transaction', 'wp-hotel-booking' ) );
		} else {
			$to      = $settings->get( 'email_new_booking_recipients', get_option( 'admin_email' ) );
			$subject = $settings->get( 'email_new_booking_subject', '[{site_title}] Reservation completed ({booking_number}) - {booking_date}' );

			$heading = $settings->get( 'email_new_booking_heading', __( 'New Booking Payment', 'wp-hotel-booking' ) );
			$desc    = $settings->get( 'email_new_booking_heading_desc', __( 'The customer had placed booking', 'wp-hotel-booking' ) );
		}

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}'
		);

		$replace = array(
			'booking-date'   => date_i18n( 'd.m.Y', strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => hb_format_order_number( $booking->id ),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
		);

		$subject = str_replace( $find, $replace, $subject );

		return hb_send_booking_mail( $booking, $to, $subject, $heading, $desc );
	}
}

if ( ! function_exists( 'hb_send_customer_booking_email' ) ) {
	/**
	 * Send email for customer when booking completed.
	 *
	 * @param null $booking
	 * @param $status
	 *
	 * @return bool
	 */
	function hb_send_customer_booking_email( $booking = null, $status ) {

		$settings = hb_settings();

		if ( 'booking_completed' == $status ) {
			$subject = $settings->get( 'email_general_subject', __( 'Reservation', 'wp-hotel-booking' ) );
			$heading = __( 'Thanks for your booking', 'wp-hotel-booking' );
			$desc    = __( 'Thank you for making reservation at our hotel. We will try our best to bring the best service. Good luck and see you soon!', 'wp-hotel-booking' );
		} else {
			$subject = __( 'Booking pending', 'wp-hotel-booking' );
			$heading = __( 'Your booking is pending', 'wp-hotel-booking' );
			$desc    = __( 'Your booking is pending until the payment is completed', 'wp-hotel-booking' );
		}

		return hb_send_booking_mail( $booking, $booking->customer_email, $subject, $heading, $desc );
	}
}

if ( ! function_exists( 'hb_set_mail_from' ) ) {
	/**
	 * Filter email from.
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	function hb_set_mail_from( $email ) {
		$settings = hb_settings();
		if ( $email = $settings->get( 'email_general_from_email', get_option( 'admin_email' ) ) ) {
			if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return $email;
			}
		}

		return $email;
	}
}

if ( ! function_exists( 'hb_set_mail_from_name' ) ) {
	/**
	 * Filter email from name.
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	function hb_set_mail_from_name( $name ) {
		$settings = hb_settings();
		if ( $name = $settings->get( 'email_general_from_name' ) ) {
			return $name;
		}

		return $name;
	}
}

if ( ! function_exists( 'hb_set_html_content_type' ) ) {
	/**
	 * Filter content type to text/html for email.
	 *
	 * @return string
	 */
	function hb_set_html_content_type() {
		$settings = hb_settings();
		$format   = $settings->get( 'email_general_format', 'html' );
		if ( 'html' == $format ) {
			return 'text/html';
		} else {
			return 'text/plain';
		}
	}
}

//============================================ Email Booking functions end ===========================================//
