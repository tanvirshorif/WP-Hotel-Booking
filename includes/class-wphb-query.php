<?php

/**
 * WP Hotel Booking Query class.
 *
 * @class
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Query' ) ) {

	/**
	 * Class WPHB_Query.
	 *
	 * @since 2.0
	 */
	class WPHB_Query {

		/**
		 * @var WPHB_Query object instance
		 *
		 * @access protected
		 */
		static protected $_instance = null;

		/**
		 * WPHB_Query constructor.
		 *
		 * @since 2.0
		 *
		 * @param null $params
		 */
		public function __construct( $params = null ) {
			add_filter( 'hb_search_booking_except_conditions', array( $this, 'booking_except_conditions' ), 10, 3 );
		}

		/**
		 * Where conditions when except booking search room.
		 *
		 * @since 2.0
		 *
		 * @param $default
		 * @param $check_in
		 * @param $check_out
		 *
		 * @return string
		 */
		public function booking_except_conditions( $default, $check_in, $check_out ) {

			$conditions = "( check_in.meta_value >= $check_in AND check_in.meta_value <= $check_out )
						OR 	( check_out.meta_value >= $check_in AND check_out.meta_value <= $check_out )
						OR 	( check_in.meta_value <= $check_in AND check_out.meta_value > $check_out )";

			return $conditions;

		}

		/**
		 * Search rooms query.
		 *
		 * @since 2.0
		 *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public function search_rooms( $args = array() ) {
			global $wpdb;

			$adults                 = $args['adults'];
			$max_child              = $args['max_child'];
			$date_in                = strtotime( $args['check_in_date'] );
			$time_in                = $args['check_in_time'];
			$date_out               = strtotime( $args['check_out_date'] );
			$time_out               = $args['check_out_time'];
			$check_in_date_to_time  = mktime( 0, 0, 0, date( 'm', $date_in ), date( 'd', $date_in ), date( 'Y', $date_in ) );
			$check_out_date_to_time = mktime( 0, 0, 0, date( 'm', $date_out ), date( 'd', $date_out ), date( 'Y', $date_out ) );

			$extend_join       = apply_filters( 'hb_search_room_extend_join', '' );
			$extend_conditions = apply_filters( 'hb_search_room_extend_conditions', '' );

			$booking_except_join       = apply_filters( 'hb_search_booking_except_join', '' );
			$booking_except_conditions = apply_filters( 'hb_search_booking_except_conditions', '', $check_in_date_to_time, $check_out_date_to_time );

			$results = array();

			$except = $wpdb->prepare( "
				( SELECT COALESCE( SUM( meta.meta_value ), 0 ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
					LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS item_meta ON order_item.order_item_id = item_meta.hotel_booking_order_item_id AND item_meta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_in ON order_item.order_item_id = check_in.hotel_booking_order_item_id AND check_in.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_out ON order_item.order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
					{$booking_except_join}
				WHERE
					item_meta.meta_value = rooms.ID
					AND ({$booking_except_conditions})
					AND booking.post_type = %s
					AND booking.post_status IN ( %s, %s, %s )
				)",
				'qty', 'product_id', 'check_in_date', 'check_out_date', 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
			);

			$query = $wpdb->prepare( "
				SELECT rooms.*, ( number.meta_value - {$except} ) AS available_rooms FROM $wpdb->posts AS rooms
				    LEFT JOIN {$wpdb->postmeta} AS number ON rooms.ID = number.post_id AND number.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = rooms.ID AND pm1.meta_key = %s
					LEFT JOIN {$wpdb->termmeta} AS term_cap ON term_cap.term_id = pm1.meta_value AND term_cap.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
					{$extend_join}
				WHERE
					rooms.post_type = %s
					AND rooms.post_status = %s
					AND term_cap.meta_value >= %d
					AND pm2.meta_value >= %d
					{$extend_conditions}
				GROUP BY rooms.post_name
				HAVING available_rooms > 0
				ORDER BY term_cap.meta_value ASC
		", '_hb_num_of_rooms', '_hb_room_capacity', 'hb_max_number_of_adults', '_hb_max_child_per_room', 'hb_room', 'publish', $adults, $max_child );

			$query = apply_filters( 'hb_search_query', $query, array(
				'check_in'  => $check_in_date_to_time,
				'check_out' => $check_out_date_to_time,
				'adults'    => $adults,
				'child'     => $max_child
			) );

			if ( $search = $wpdb->get_results( $query ) ) {
				foreach ( $search as $k => $p ) {
					$room = WPHB_Room::instance( $p, array(
						'check_in_date'  => date( 'm/d/Y', $check_in_date_to_time ),
						'check_out_date' => date( 'm/d/Y', $check_out_date_to_time ),
						'quantity'       => 1
					) );

					$room->post->available_rooms = (int) $p->available_rooms;

					$room = apply_filters( 'hotel_booking_query_search_parser', $room );
					if ( $room && $room->post->available_rooms > 0 ) {
						$results[ $k ] = $room;
					}
				}
			}

			$cart = WPHB_Cart::instance();
			if ( $cart->get_cart_contents() && $search ) {
				$selected_id = array();
				foreach ( $cart->get_cart_contents() as $k => $_cart ) {
					$selected_id[ $_cart->product_id ] = $_cart->quantity;
				}

				foreach ( $results as $k => $room ) {
					if ( array_key_exists( $room->post->ID, $selected_id ) ) {
						$in  = $room->get_data( 'check_in_date' );
						$out = $room->get_data( 'check_out_date' );
						if ( ( $in < $check_in_date_to_time && $check_out_date_to_time < $out ) || ( $in < $check_in_date_to_time && $check_out_date_to_time < $out ) ) {
							$total                                = $search[ $k ]->available_rooms;
							$results[ $k ]->post->available_rooms = (int) $total - (int) $selected_id[ $room->post->ID ];
						}
					}
				}
			}

			$results = apply_filters( 'hb_search_available_rooms', $results, array(
				'check_in'  => $check_in_date_to_time,
				'check_out' => $check_out_date_to_time,
				'adults'    => $adults,
				'child'     => $max_child
			) );

			$total          = count( $results );
			$posts_per_page = (int) apply_filters( 'hb_number_search_rooms_per_page', hb_get_option( 'posts_per_page', 8 ) );
			$page           = isset( $_GET['hb_page'] ) ? absint( $_GET['hb_page'] ) : 1;
			$offset         = ( $page * $posts_per_page ) - $posts_per_page;
			$max_num_pages  = ceil( $total / $posts_per_page );

			$GLOBALS['hb_search_rooms'] = array(
				'max_num_pages'  => $max_num_pages,
				'data'           => $max_num_pages > 1 ? array_slice( $results, $offset, $posts_per_page ) : $results,
				'total'          => $total,
				'posts_per_page' => $posts_per_page,
				'offset'         => $offset,
				'page'           => $page,
			);

			return apply_filters( 'hb_search_results', $GLOBALS['hb_search_rooms'], $args );
		}

		/**
		 * Get unique instance for this object.
		 *
		 * @since 2.0
		 *
		 * @param null $params
		 *
		 * @return WPHB_Query
		 */
		public static function instance( $params = null ) {
			if ( empty( self::$_instance ) ) {
				self::$_instance = new self( $params );
			}

			return self::$_instance;
		}
	}
}

new WPHB_Query();