<?php

/**
 * WP Hotel Booking Block class.
 *
 * @class       WPHB_Block
 * @version     2.0
 * @package     WP_Hotel_Booking_Block_Room/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Block' ) ) {

	/**
	 * Class WPHB_Block.
	 *
	 * @since 2.0
	 */
	class WPHB_Block {

		/**
		 * @var null
		 */
		public static $instance = null;

		/**
		 * WPHB_Block constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			// admin menu
			add_filter( 'hotel_booking_menu_items', array( $this, 'admin_sub_menu' ) );
			add_action( 'init', array( $this, 'register_post_type' ) );

			// enqueue script
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// js template
			add_action( 'wp_ajax_hotel_block_update', array( $this, 'update_block_dates' ) );
			add_action( 'wp_ajax_nopriv_hotel_block_update', array( $this, 'notLogin' ) );

			// remove calendar
			add_action( 'wp_ajax_wphb_delete_block', array( $this, 'delete_block_post' ) );
			add_action( 'wp_ajax_nopriv_wphb_delete_block', array( $this, 'notLogin' ) );

			add_filter( 'hb_search_query', array( $this, 'search' ), 10, 2 );
			add_filter( 'hotel_booking_get_room_available', array( $this, 'single_room_search' ), 10, 3 );
		}

		/**
		 * Add Block sub menu in WP Hotel Booking plugin menu.
		 *
		 * @since 2.0
		 *
		 * @param  $menus array
		 *
		 * @return array
		 */
		public function admin_sub_menu( $menus ) {
			$menus['block'] = array(
				'tp_hotel_booking',
				__( 'Block Special Date', 'wphb-block' ),
				__( 'Block Special Date', 'wphb-block' ),
				'manage_hb_booking',
				'wphb-block-dates',
				array( $this, 'block_special_dates' )
			);

			return $menus;
		}

		/**
		 * Register block custom post type.
		 *
		 * @since 2.0
		 */
		public function register_post_type() {

			$args = array(
				'labels'             => array(
					'name'               => _x( 'Blocked', 'post type general name', 'wphb-block' ),
					'singular_name'      => _x( 'Blocked', 'post type singular name', 'wphb-block' ),
					'menu_name'          => _x( 'Blocked', 'admin menu', 'wphb-block' ),
					'name_admin_bar'     => _x( 'Blocked', 'add new on admin bar', 'wphb-block' ),
					'add_new'            => _x( 'Add New', 'block', 'wphb-block' ),
					'add_new_item'       => __( 'Add New Blocked', 'wphb-block' ),
					'new_item'           => __( 'New Blocked', 'wphb-block' ),
					'edit_item'          => __( 'Edit Blocked', 'wphb-block' ),
					'view_item'          => __( 'View Blocked', 'wphb-block' ),
					'all_items'          => __( 'All Blocked', 'wphb-block' ),
					'search_items'       => __( 'Search Blocked', 'wphb-block' ),
					'parent_item_colon'  => __( 'Parent Blocked:', 'wphb-block' ),
					'not_found'          => __( 'No blocked found.', 'wphb-block' ),
					'not_found_in_trash' => __( 'No blocked found in Trash.', 'wphb-block' )
				),
				'description'        => __( 'Blocked days.', 'wphb-block' ),
				'public'             => false,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'block' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' )
			);
			$args = apply_filters( 'hotel_booking_register_post_type_block_arg', $args );

			register_post_type( 'hb_blocked', $args );
		}

		/**
		 * Block date admin view.
		 *
		 * @since 2.0
		 */
		public function block_special_dates() {
			require_once WPHB_BLOCK_ABSPATH . '/includes/admin/views/block.php';
		}

		/**
		 * Plugin enqueues scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wphb_block_angular', WPHB_BLOCK_URI . 'assets/js/angular.min.js', array(), WPHB_BLOCK_VER );
			wp_enqueue_script( 'wphb_block_moment', WPHB_BLOCK_URI . 'assets/js/moment.min.js', array(), WPHB_BLOCK_VER );
			wp_enqueue_script( 'wphb_block_lib_datepicker', WPHB_BLOCK_URI . 'assets/js/multipleDatePicker.min.js', array(), WPHB_BLOCK_VER );
			wp_enqueue_style( 'wphb_block_lib_datepicker', WPHB_BLOCK_URI . 'assets/css/multiple-date-picker.min.css' );

			wp_enqueue_script( 'wphb_block', WPHB_BLOCK_URI . 'assets/js/admin.js', array(), WPHB_BLOCK_VER );
			wp_enqueue_style( 'wphb_block', WPHB_BLOCK_URI . 'assets/css/admin.css' );

			wp_localize_script( 'wphb_booking_block', 'WPHB_Block',
				apply_filters( 'wphb_block_l18n',
					array(
						'ajaxurl'    => admin_url( 'admin-ajax.php?schema=hotel-block' ),
						'error_ajax' => __( 'Request has error. Please try again.', 'wphb-block' )
					)
				)
			);

			wp_enqueue_script( 'wphb_block' );
		}

		/**
		 * Convert current time with timezone.
		 *
		 * @since 2.0
		 *
		 * @param null $time
		 * @param int $gmt
		 *
		 * @return int|null
		 */
		public function convert_current_time( $time = null, $gmt = 0 ) {
			if ( ! $time ) {
				$time = time();
			}

			if ( ! $gmt ) {
				return $time + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			} else {
				return $time - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) - 12 * HOUR_IN_SECONDS;
			}
		}

		/**
		 * Update block dates ajax action.
		 *
		 * @since 2.0
		 */
		public function update_block_dates() {
			if ( ! isset( $_REQUEST['schema'] ) || sanitize_text_field( $_REQUEST['schema'] ) !== 'hotel-block' ) {
				wp_send_json( array(
					'status'  => 'failed',
					'message' => __( 'Something went wrong.', 'wphb-block' )
				) );
			}

			$calendars = json_decode( file_get_contents( 'php://input' ) );
			$calendars = json_decode( $calendars->data );

			global $wpdb;
			foreach ( $calendars as $k => $calendar ) {
				if ( ! isset( $calendar->post_id ) || empty( $calendar->post_id ) ) {
					continue;
				}

				if ( ! isset( $calendar->selected ) || empty( $calendar->selected ) ) {
					continue;
				}

				$calendar_id = $calendar->id;
				if ( ! get_post( $calendar_id ) ) {
					$calendar_id = wp_insert_post( array(
						'post_type'    => 'hb_blocked',
						'post_status'  => 'publish',
						'post_title'   => __( 'Block item', 'wphb-block' ),
						'post_content' => __( 'Block item', 'wphb-block' )
					) );
				}

				delete_post_meta( $calendar_id, 'hb_blocked_time' );

				// delete all blocked time
				$times = get_post_meta( $calendar_id, 'hb_blocked_time' );

				// add post meta for post type hb_blocked
				foreach ( $calendar->selected as $key => $timestamp ) {
					// $timestamp is millisecond in UTC +0
					$time = $timestamp / 1000 + HOUR_IN_SECONDS * 12;
					$time = $this->convert_current_time( $time );

					if ( ! in_array( $time, $times ) ) {
						add_post_meta( $calendar_id, 'hb_blocked_time', $time );
					}
				}

				// delete old room selected
				$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => 'hb_blocked_id', 'meta_value' => $calendar_id ) );
				// add post meta blocked id
				foreach ( $calendar->post_id as $key => $post ) {
					if ( ! in_array( $calendar_id, get_post_meta( $post->ID, 'hb_blocked_id' ) ) ) {
						add_post_meta( $post->ID, 'hb_blocked_id', $calendar_id );
					}
				}
			}

			wp_send_json( array(
				'status'  => 'success',
				'data'    => $this->get_blocked(),
				'message' => __( 'Update Completed.', 'wphb-block' )
			) );
		}

		/**
		 * Ajax required login.
		 *
		 * @since 2.0
		 */
		public function notLogin() {
			wp_send_json( array(
				'status'  => 'failed',
				'message' => __( 'You must Login System', 'wphb-block' )
			) );
		}

		/**
		 * Get blocked date to object.
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		public function get_blocked() {
			global $wpdb;

			$title = $wpdb->prepare( "
				SELECT room.post_title
				FROM $wpdb->posts AS room
				WHERE
					room.post_status = %s
					AND room.post_type = %s
					AND room.ID = room_meta.post_id
				GROUP BY room.ID
			", 'publish', 'hb_room' );

			$query = $wpdb->prepare( "
				SELECT calendar.ID as calendarID, blocked.meta_value AS selected, room_meta.post_id AS ID, ( $title ) AS post_title
				FROM $wpdb->posts AS calendar
				INNER JOIN $wpdb->postmeta AS blocked ON calendar.ID = blocked.post_id
				INNER JOIN $wpdb->postmeta AS room_meta ON room_meta.meta_value = calendar.ID
				WHERE
					calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked.meta_key = %s
					AND room_meta.meta_key = %s
				ORDER BY calendarID
			", 'hb_blocked', 'publish', 'hb_blocked_time', 'hb_blocked_id' );

			$results = $wpdb->get_results( $query, OBJECT );

			$calendars = array();
			if ( $results ) {
				foreach ( $results as $key => $post ) {
					if ( ! isset( $calendars[ $post->calendarID ] ) ) {
						$calendars[ $post->calendarID ] = new stdClass();
					}

					if ( ! isset( $calendars[ $post->calendarID ]->id ) ) {
						$calendars[ $post->calendarID ]->id = (int) $post->calendarID;
					}

					if ( ! isset( $calendars[ $post->calendarID ]->post_id ) ) {
						$calendars[ $post->calendarID ]->post_id = array();
					}

					// post_id
					$room             = new stdClass();
					$room->ID         = $post->ID;
					$room->post_title = $post->post_title;

					$calendars[ $post->calendarID ]->post_id[] = $room;

					// selected
					if ( ! isset( $calendars[ $post->calendarID ]->selected ) ) {
						$calendars[ $post->calendarID ]->selected = array();
					}

					if ( $post->selected <= current_time( 'timstamp' ) ) {
						$time = $this->convert_current_time( $post->selected, 1 ) * 1000;
						if ( ! in_array( $time, $calendars[ $post->calendarID ]->selected ) ) {
							$calendars[ $post->calendarID ]->selected[] = $time;
						}
					}
				}
			} else {
				$time             = time();
				$object           = new stdClass();
				$object->id       = $time;
				$object->post_id  = array();
				$object->selected = array();

				$calendars[ $time ] = $object;
			}

			return $calendars;
		}

		/**
		 * Ajax handler delete block post.
		 *
		 * @since 2.0
		 */
		public function delete_block_post() {
			if ( ! isset( $_REQUEST['schema'] ) || sanitize_text_field( $_REQUEST['schema'] ) !== 'hotel-block' ) {
				wp_send_json( array(
					'status'  => 'failed',
					'message' => __( 'Something went wrong. Please try again!', 'wphb-block' )
				) );
			}

			$calendar = json_decode( file_get_contents( 'php://input' ) );
			if ( $calendar_id = $calendar->calendar_id ) {
				if ( get_post( $calendar_id ) && wp_delete_post( $calendar_id ) ) {
					wp_send_json( array(
						'status'  => 'success',
						'data'    => $this->get_blocked(),
						'message' => __( 'Remove completed!', 'wphb-block' )
					) );
				}
			}

			wp_send_json( array(
				'status'  => 'success',
				'data'    => $this->get_blocked(),
				'message' => __( 'Remove completed!', 'wphb-block' )
			) );
		}

		/**
		 * Custom global search query with block dates.
		 *
		 * @since 2.0
		 *
		 * @param $query
		 * @param $param
		 *
		 * @return string
		 */
		public function search( $query, $param ) {
			$check_in  = isset( $param['check_in'] ) ? $param['check_in'] : time();
			$check_out = isset( $param['check_out'] ) ? $param['check_out'] : time();
			$adults    = isset( $param['adults'] ) ? (int) $param['adults'] : hb_get_max_capacity_of_rooms();
			$child     = isset( $param['child'] ) ? (int) $param['child'] : hb_get_max_child_of_rooms();

			global $wpdb;

			$blocked = $wpdb->prepare( "
				SELECT COALESCE( COUNT( blocked_time.meta_value ), 0 )
				FROM $wpdb->postmeta AS blocked_post
				INNER JOIN $wpdb->posts AS calendar ON calendar.ID = blocked_post.meta_value
				INNER JOIN $wpdb->postmeta AS blocked_time ON blocked_time.post_id = calendar.ID
				WHERE
					blocked_post.post_id = rooms.ID
					AND calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked_post.meta_key = %s
					AND blocked_time.meta_key = %s
					AND blocked_time.meta_value >= %d
					AND blocked_time.meta_value <= %d
			", 'hb_blocked', 'publish', 'hb_blocked_id', 'hb_blocked_time', $check_in, $check_out );

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
		", 'qty', 'product_id', 'check_in_date', 'check_out_date', $check_in, $check_out, $check_in, $check_out, $check_in, $check_out, 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
			);

			$query = $wpdb->prepare( "
				SELECT rooms.*, ( number.meta_value - {$not} ) AS available_rooms, ($blocked) AS blocked FROM $wpdb->posts AS rooms
					LEFT JOIN $wpdb->postmeta AS number ON rooms.ID = number.post_id AND number.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = rooms.ID AND pm1.meta_key = %s
					LEFT JOIN {$wpdb->termmeta} term_cap ON term_cap.term_id = pm1.meta_value AND term_cap.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
				WHERE
					rooms.post_type = %s
					AND rooms.post_status = %s
					AND term_cap.meta_value >= %d
					AND pm2.meta_value >= %d
				GROUP BY rooms.post_name
				HAVING ( available_rooms > 0 AND blocked = 0 )
				ORDER BY term_cap.meta_value DESC
			", '_hb_num_of_rooms', '_hb_room_capacity', 'hb_max_number_of_adults', '_hb_max_child_per_room', 'hb_room', 'publish', $adults, $child );

			return $query;
		}

		/**
		 * Custom single room search query with block dates.
		 *
		 * @since 2.0
		 *
		 * @param $qty
		 * @param $room_id
		 * @param $args
		 *
		 * @return int
		 */
		public function single_room_search( $qty, $room_id, $args ) {
			global $wpdb;
			$check_in  = $args['check_in_date'];
			$check_out = $args['check_out_date'];
			$sql       = $wpdb->prepare( "
				SELECT COALESCE( COUNT( blocked_time.meta_value ), 0 )
				FROM $wpdb->postmeta AS blocked_time
				INNER JOIN $wpdb->posts AS calendar ON calendar.ID = blocked_time.meta_value
				INNER JOIN $wpdb->postmeta AS blocked_post ON blocked_post.post_id = calendar.ID
				WHERE
					blocked_time.post_id = %d
					AND calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked_time.meta_key = %s
					AND blocked_post.meta_key = %s
					AND ( blocked_post.meta_value > %d
                                            AND blocked_post.meta_value <= %d )
			", $room_id, 'hb_blocked', 'publish', 'hb_blocked_id', 'hb_blocked_time', $check_in, $check_out, $check_in, $check_out );

			$blocked = $wpdb->get_var( $sql );
			if ( $blocked ) {
				return 0;
			}

			return $qty;
		}

		/**
		 * WPHB_Block instance.
		 *
		 * @since 2.0
		 *
		 * @return null|WPHB_Block
		 */
		public static function instance() {
			if ( self::$instance ) {
				return self::$instance;
			}

			return self::$instance = new self();
		}

	}

}

WPHB_Block::instance();
