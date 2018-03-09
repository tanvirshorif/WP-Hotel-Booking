<?php

/**
 * WP Hotel Booking ajax class.
 *
 * @class       WPHB_Ajax
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Ajax' ) ) {

	/**
	 * Class WPHB_Ajax.
	 *
	 * @since 2.0
	 */
	class WPHB_Ajax {

		/**
		 * WPHB_Ajax constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {

			$actions = array(
				'fetch_customer_info',
				'place_booking',
				'parse_search_params',
				'add_to_cart',
				'remove_cart_item',
				'remove_extra_cart',
				'cancel_booking'
			);

			foreach ( $actions as $action ) {
				add_action( "wp_ajax_wphb_{$action}", array( __CLASS__, $action ) );
				add_action( "wp_ajax_nopriv_wphb_{$action}", array( __CLASS__, $action ) );
			}
		}

		/**
		 * Fetch customer information with user email.
		 *
		 * @since 2.0
		 */
		public static function fetch_customer_info() {
			$email = hb_get_request( 'email' );
			$args  = array(
				'post_type'   => 'hb_booking',
				'meta_key'    => '_hb_customer_email',
				'meta_value'  => $email,
				'post_status' => 'any'
			);
			// set_transient( 'hotel_booking_customer_email_' . WPHB_BLOG_ID, $email, DAY_IN_SECONDS );
			$cart = WPHB_Cart::instance();
			$cart->set_customer( 'customer_email', $email );
			if ( $posts = get_posts( $args ) ) {
				$customer       = $posts[0];
				$customer->data = array();
				$data           = get_post_meta( $customer->ID );
				foreach ( $data as $k => $v ) {
					$customer->data[ $k ] = $v[0];
				}
			} else {
				$customer = null;
			}
			hb_send_json( $customer );
			wp_die();
		}

		/**
		 * Process the booking with customer information posted via form.
		 *
		 * @throws Exception
		 */
		public static function place_booking() {
			WPHB_Checkout::instance()->process_checkout();
			exit();
		}

		/**
		 * Catch variables via post method and build a request param.
		 *
		 * @since 2.0
		 */
		public static function parse_search_params() {
			check_ajax_referer( 'hb_search_nonce_action', 'nonce' );

			$params = apply_filters( 'hb_search_room_params', array(
				'hotel-booking'     => hb_get_request( 'hotel-booking' ),
				'check_in_date'     => hb_get_request( 'check_in_date' ),
				'check_in_time'     => hb_get_request( 'check_in_time' ),
				'check_out_date'    => hb_get_request( 'check_out_date' ),
				'check_out_time'    => hb_get_request( 'check_out_time' ),
				'hb_check_in_date'  => hb_get_request( 'hb_check_in_date' ),
				'hb_check_in_time'  => hb_get_request( 'hb_check_in_time' ),
				'hb_check_out_date' => hb_get_request( 'hb_check_out_date' ),
				'hb_check_out_time' => hb_get_request( 'hb_check_out_time' ),
				'adults'            => hb_get_request( 'adults_capacity' ),
				'max_child'         => hb_get_request( 'max_child' ),
				'room_location'     => hb_get_request( 'room_location' )
			) );

			$return = apply_filters( 'hotel_booking_parse_search_param', array(
				'success' => 1,
				'sig'     => base64_encode( serialize( $params ) ),
				'params'  => $params
			) );
			hb_send_json( $return );
		}

		/**
		 * Add to cart action.
		 *
		 * @since 2.0
		 */
		public static function add_to_cart() {
			if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) ) {
				return;
			}

			if ( ! isset( $_POST['room-id'] ) || ! isset( $_POST['hb-num-of-rooms'] ) ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Room ID is not exists.', 'wp-hotel-booking' )
				) );
			}

			if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_out_date'] ) ) {
				return;
			}

			$product_id          = absint( $_POST['room-id'] );
			$param               = array();
			$qty                 = '';
			$param['product_id'] = sanitize_text_field( $product_id );
			if ( ! isset( $_POST['hb-num-of-rooms'] ) || ! absint( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Can not select zero room.', 'wp-hotel-booking' )
				) );
			} else {
				$qty = absint( sanitize_text_field( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) );
			}

			// validate check in, check out date
			if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_in_date'] ) ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Check in date, check out date is invalid.', 'wp-hotel-booking' )
				) );
			} else {
				$param['check_in_date']  = sanitize_text_field( $_POST['check_in_date'] );
				$param['check_in_time']  = sanitize_text_field( $_POST['check_in_time'] );
				$param['check_out_date'] = sanitize_text_field( $_POST['check_out_date'] );
				$param['check_out_time'] = sanitize_text_field( $_POST['check_out_time'] );
			}
			$param = apply_filters( 'hotel_booking_add_cart_params', $param );
			do_action( 'hotel_booking_before_add_to_cart', $_POST );
			// add to cart
			$cart         = WPHB_Cart::instance();
			// add room to cart and after that add extra to cart
			$cart_item_id = $cart->add_to_cart( $product_id, $param, $qty );

			if ( ! is_wp_error( $cart_item_id ) ) {
				$cart_item = $cart->get_cart_item( $cart_item_id );
				$room      = $cart_item->product_data;

				do_action( 'hotel_booking_added_cart_completed', $cart_item_id, $cart_item, $_POST );

				$results = array(
					'status'    => 'success',
					'message'   => sprintf( '<label class="hb_success_message">%1$s</label>', __( 'Added successfully.', 'wp-hotel-booking' ) ),
					'id'        => $product_id,
					'permalink' => get_permalink( $product_id ),
					'name'      => sprintf( '%s', $room->name ) . ( $room->capacity_title ? sprintf( '(%s)', $room->capacity_title ) : '' ),
					'quantity'  => $qty,
					'cart_id'   => $cart_item_id,
					'total'     => hb_format_price( $cart->get_cart_item( $cart_item_id )->amount )
				);

				$results = apply_filters( 'hotel_booking_add_to_cart_results', $results, $room );

				hb_send_json( $results );
			} else {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Room selected. Please View Cart to change order', 'wp-hotel-booking' )
				) );
			}
		}

		/**
		 * Remove cart item action.
		 *
		 * @since 2.0
		 */
		public static function remove_cart_item() {
			if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) ) {
				return;
			}
			$cart = WPHB_Cart::instance();

			if ( ! ( $cart->cart_contents ) || ! isset( $_POST['cart_id'] ) || ! array_key_exists( sanitize_text_field( $_POST['cart_id'] ), $cart->cart_contents ) ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Cart item is not exists.', 'wp-hotel-booking' )
				) );
			}

			if ( $cart->remove_cart_item( sanitize_text_field( $_POST['cart_id'] ) ) ) {
				$return = apply_filters( 'hotel_booking_ajax_remove_cart_item', array(
					'status'          => 'success',
					'sub_total'       => hb_format_price( $cart->sub_total ),
					'grand_total'     => hb_format_price( $cart->total ),
					'advance_payment' => hb_format_price( $cart->advance_payment )
				) );

				hb_send_json( $return );
			}
		}

		/**
		 * Remove extra from cart.
		 *
		 * @since 2.0
		 */
		public static function remove_extra_cart() {
			if ( ! isset( $_POST ) || ! defined( 'WPHB_BLOG_ID' ) ) {
				return;
			}

			if ( ! isset( $_POST['cart_id'] ) || ! $_POST['cart_id'] ) {
				wp_send_json( array(
					'status'  => 'success',
					'message' => __( 'Cart ID is not exists.', 'wp-hotel-booking' )
				) );
			}

			$cart_id = sanitize_text_field( $_POST['cart_id'] );

			// cart item is exists
			$cart = WPHB_Cart::instance();
			if ( $package_item = $cart->get_cart_item( $cart_id ) ) {
				if ( $cart->remove_cart_item( $cart_id ) ) {
					// room cart item id
					$room    = $cart->get_cart_item( $package_item->parent_id );
					$results = array(
						'status'          => 'success',
						'cart_id'         => $package_item->parent_id,
						'permalink'       => get_permalink( $room->product_id ),
						'name'            => sprintf( '%s', $room->product_data->name ) . ( $room->product_data->capacity_title ? sprintf( '(%s)', $room->product_data->capacity_title ) : '' ),
						'quantity'        => $room->quantity,
						'total'           => hb_format_price( $room->amount ),
						// use to cart table
						'package_id'      => $cart_id,
						'item_total'      => hb_format_price( $room->amount_include_tax ),
						'sub_total'       => hb_format_price( $cart->sub_total ),
						'grand_total'     => hb_format_price( $cart->total ),
						'advance_payment' => hb_format_price( $cart->advance_payment )
					);

					$extraRoom      = $cart->get_extra_packages( $package_item->parent_id );
					$extra_packages = array();
					if ( $extraRoom ) {
						foreach ( $extraRoom as $cart_id => $cart_item ) {
							$extra            = WPHB_Extra_Package::instance( $cart_item->product_id );
							$extra_packages[] = array(
								'package_title'      => sprintf( '%s (%s)', $extra->title, hb_format_price( $extra->amount_singular ) ),
								'cart_id'            => $cart_id,
								'package_quantity'   => sprintf( 'x%s', $cart_item->quantity ),
								'package_respondent' => $extra->respondent
							);
						}
					}
					$results['extra_packages'] = $extra_packages;

					$results = apply_filters( 'hb_remove_package_results', $results, $package_item );

					do_action( 'hb_extra_removed_package', $package_item );
					hb_send_json( $results );
				}

			} else {
				wp_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Cart item is not exists.', 'wp-hotel-booking' )
				) );
			}
		}

		/**
		 * Customer request cancel booking.
		 */
		public static function cancel_booking() {
			if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) ) {
				return;
			}

			if ( ! isset( $_POST['booking_id'] ) || get_post_type( $_POST['booking_id'] ) != 'hb_booking' ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'Invalid Booking ID.', 'wp-hotel-booking' )
				) );
			}

			$booking_id = $_POST['booking_id'];
			$booking    = get_post( $booking_id );
			if ( $booking->post_author != get_current_user_id() ) {
				hb_send_json( array(
					'status'  => 'warning',
					'message' => __( 'You are not permission to cancel this booking.', 'wp-hotel-booking' )
				) );
			}

			$booking = WPHB_Booking::instance( $booking_id );
			$booking->update_status( 'cancelled' );

			/**
			 * @hook wphb_send_booking_cancelled_email
			 */
			do_action( 'wphb_customer_cancel_booking', $booking_id );

			$results = array(
				'status'  => 'success',
				'message' => __( 'Cancel booking successfully.', 'wp-hotel-booking' )
			);

			$results = apply_filters( 'wphb_customer_cancel_booking_result', $results, $booking_id );

			hb_send_json( $results );
		}
	}
}

new WPHB_Ajax();
