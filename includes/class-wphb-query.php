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
			if ( $params['location'] ) {
				add_filter( 'hb_search_room_extent_join', array( $this, 'location_join' ) );
				add_filter( 'hb_search_room_extent_conditions', array( $this, 'location_conditions' ), 10, 2 );
			}
		}

		/**
		 * Left join to query room by location.
		 *
		 * @since 2.0
		 *
		 * @return string
		 */
		public function location_join() {
			global $wpdb;
			$join = "LEFT JOIN {$wpdb->term_relationships} AS term_relationships ON rooms.ID = term_relationships.object_id";

			return $join;
		}

		/**
		 * Where conditions when search room by location.
		 *
		 * @since 2.0
		 *
		 * @param $default
		 * @param $location
		 *
		 * @return string
		 */
		public function location_conditions( $default, $location ) {
			$conditions = "AND term_relationships.term_taxonomy_id = $location ";

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
			$location               = $args['location'];
			$check_in_time          = strtotime( $args['check_in_date'] );
			$check_out_time         = strtotime( $args['check_out_date'] );
			$check_in_date_to_time  = mktime( 0, 0, 0, date( 'm', $check_in_time ), date( 'd', $check_in_time ), date( 'Y', $check_in_time ) );
			$check_out_date_to_time = mktime( 0, 0, 0, date( 'm', $check_out_time ), date( 'd', $check_out_time ), date( 'Y', $check_out_time ) );

			$extend_join       = apply_filters( 'hb_search_room_extent_join', '' );
			$extend_conditions = apply_filters( 'hb_search_room_extent_conditions', '', $location );

			$results = array();

			$not = $wpdb->prepare( "
			(
				SELECT COALESCE( SUM( meta.meta_value ), 0 ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
					LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS itemmeta ON order_item.order_item_id = itemmeta.hotel_booking_order_item_id AND itemmeta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id AND checkin.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id AND checkout.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
				WHERE
						itemmeta.meta_value = rooms.ID
					AND (
							( checkin.meta_value >= %d AND checkin.meta_value <= %d )
						OR 	( checkout.meta_value >= %d AND checkout.meta_value <= %d )
						OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
					)
					AND booking.post_type = %s
					AND booking.post_status IN ( %s, %s, %s )
			)
		", 'qty', 'product_id', 'check_in_date', 'check_out_date', $check_in_date_to_time, $check_out_date_to_time, $check_in_date_to_time, $check_out_date_to_time, $check_in_date_to_time, $check_out_date_to_time, 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
			);

			$query = $wpdb->prepare( "
				SELECT rooms.*, ( number.meta_value - {$not} ) AS available_rooms FROM $wpdb->posts AS rooms
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
				'child'     => $max_child,
				'location'  => $location
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
			if ( $cart->cart_contents && $search ) {
				$selected_id = array();
				foreach ( $cart->cart_contents as $k => $_cart ) {
					$selected_id[ $_cart->product_id ] = $_cart->quantity;
				}

				foreach ( $results as $k => $room ) {
					if ( array_key_exists( $room->post->ID, $selected_id ) ) {
						$in  = $room->get_data( 'check_in_date' );
						$out = $room->get_data( 'check_out_date' );
						if (
							( $in < $check_in_date_to_time && $check_out_date_to_time < $out ) || ( $in < $check_in_date_to_time && $check_out_date_to_time < $out )
						) {
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
			global $hb_settings;
			$total          = count( $results );
			$posts_per_page = (int) apply_filters( 'hb_number_search_rooms_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
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