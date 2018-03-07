<?php

/**
 * WP Hotel Booking Room CURD class.
 *
 * @class       WPHB_Room_CURD
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Room_CURD' ) ) {
	/**
	 * Class WPHB_Room_CURD.
	 *
	 * @since 2.0
	 */
	class WPHB_Room_CURD extends WPHB_Abstract_CURD implements WPHB_Interface_CURD {

		/**
		 * @var array
		 */
		protected $_args = array();

		/**
		 * WPHB_Room_CURD constructor.
		 */
		public function __construct() {
		}

		public function create( &$object ) {
			// TODO: Implement create() method.
		}

		public function load( &$object ) {
			// TODO: Implement load() method.
		}

		/**
		 * Delete room pricing plans in plans table.
		 *
		 * @param object $room_id
		 *
		 * @return bool
		 */
		public function delete( &$room_id ) {
			if ( ! $room_id || get_post_type( $room_id ) != WPHB_Room_CPT ) {
				return false;
			}

			global $wpdb;
			// delete room pricing plans by room id
			$wpdb->delete( $wpdb->hotel_booking_plans, array( 'room_id' => $room_id ), array( '%d' ) );

			do_action( 'wphb_before_delete_room', $room_id );

			return true;
		}

		public function update( &$object ) {
			// TODO: Implement update() method.
		}

		/**
		 * Get all rooms.
		 *
		 * @return array|null|object
		 */
		public static function get_rooms() {
			global $wpdb;
			$query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE `post_type` = %s AND `post_status` =  %s ORDER BY %s ASC", WPHB_Room_CPT, 'publish', 'id' );

			return $wpdb->get_results( $query, OBJECT );
		}

		/**
		 * Get extra of room.
		 */
		public static function get_room_extra( $room_id ) {
			$extra_product = WPHB_Extra_Product::instance( $room_id );
			$room_extra    = $extra_product->get_extra();

			return $room_extra;
		}

		/**
		 * Get quantity room available.
		 *
		 * @param $room_id
		 * @param $args
		 *
		 * @return mixed|WP_Error
		 */
		public static function get_room_available( $room_id, $args ) {
			$valid  = true;
			$errors = new WP_Error;
			if ( ! $room_id ) {
				$valid = false;
				$errors->add( 'room_id_invalid', __( 'Room not found', 'wp-hotel-booking' ) );
			}

			$args = wp_parse_args( $args, array(
				'check_in_date'  => '',
				'check_out_date' => '',
				'excerpt'        => array( 0 )
			) );

			if ( ! $args['check_in_date'] ) {
				$valid = false;
				$errors->add( 'check_in_date_not_available', __( 'Check in date is not valid', 'wp-hotel-booking' ) );
			} else {
				if ( ! is_numeric( $args['check_in_date'] ) ) {
					$args['check_in_date'] = strtotime( $args['check_in_date'] );
				}
			}

			if ( ! $args['check_out_date'] ) {
				$valid = false;
				$errors->add( 'check_out_date_not_available', __( 'Check out date is not valid', 'wp-hotel-booking' ) );
			} else {
				if ( ! is_numeric( $args['check_out_date'] ) ) {
					$args['check_out_date'] = strtotime( $args['check_out_date'] );
				}
			}

			// $valid is false
			if ( $valid === false ) {
				return $errors;
			} else {
				global $wpdb;

				$not = $wpdb->prepare( "
					SELECT SUM( meta.meta_value ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
						LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS room ON order_item.order_item_id = room.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
					WHERE
						meta.meta_key = %s
						AND room.meta_value = %d
						AND room.meta_key = %s
						AND checkin.meta_key = %s
						AND checkout.meta_key = %s
						AND (
								( checkin.meta_value >= %d AND checkin.meta_value < %d )
							OR 	( checkout.meta_value > %d AND checkout.meta_value <= %d )
							OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
						)
						AND booking.post_type = %s
						AND booking.post_status IN ( %s, %s, %s )
						AND order_item.order_id NOT IN( %s )
				", 'qty', $room_id, 'product_id', 'check_in_date', 'check_out_date', $args['check_in_date'], $args['check_out_date'], $args['check_in_date'], $args['check_out_date'], $args['check_in_date'], $args['check_out_date'], 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending', implode( ',', $args['excerpt'] )
				);

				$sql = $wpdb->prepare( "
					SELECT number.meta_value AS qty FROM $wpdb->postmeta AS number
						INNER JOIN $wpdb->posts AS hb_room ON hb_room.ID = number.post_id
					WHERE
						number.meta_key = %s
						AND hb_room.ID = %d
						AND hb_room.post_type = %s
				", '_hb_num_of_rooms', $room_id, 'hb_room' );

				$qty = absint( $wpdb->get_var( $sql ) ) - absint( $wpdb->get_var( $not ) );

				if ( $qty === 0 ) {
					$errors->add( 'zero', __( 'This room is not available.', 'wp-hotel-booking' ) );

					return $errors;
				}

				return apply_filters( 'hotel_booking_get_room_available', $qty, $room_id, $args );
			}
		}
	}
}

new WPHB_Room_CURD();