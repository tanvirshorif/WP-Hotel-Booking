<?php

/**
 * WP Hotel Booking Calendar class.
 *
 * @class       WPHB_Calendar
 * @version     2.0
 * @package     WP_Hotel_Booking_Block_Room/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Calendar' ) ) {


	/**
	 * Class WPHB_Calendar.
	 *
	 * @since 2.0
	 */
	class WPHB_Calendar {

		/**
		 * @var null
		 */
		public static $instance = null;

		/**
		 * WPHB_Calendar constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			// admin menu
			add_filter( 'hotel_booking_menu_items', array( $this, 'admin_sub_menu' ) );
			// add calendar tab in single room page
			add_filter( 'hotel_booking_single_room_information_tabs', array( $this, 'calendar_single_room_tabs' ) );

			// enqueue script
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Add Calendar sub menu in WP Hotel Booking plugin menu.
		 *
		 * @since 2.0
		 *
		 * @param  $menus array
		 *
		 * @return array
		 */
		public function admin_sub_menu( $menus ) {
			$menus['calendar'] = array(
				'tp_hotel_booking',
				__( 'Calendar', 'wphb-calendar' ),
				__( 'Calendar', 'wphb-calendar' ),
				'manage_hb_booking',
				'wphb-calendar',
				array( $this, 'booking_calendar' )
			);

			return $menus;
		}

		/**
		 * Add calendar tab in single room page.
		 *
		 * @since 2.0
		 *
		 * @param $tabs
		 *
		 * @return array
		 */
		public function calendar_single_room_tabs( $tabs ) {
			$tabs[] = array(
				'id'      => 'hb_room_calendar',
				'title'   => __( 'Calendar', 'wp-calendar' ),
				'content' => ''
			);

			return $tabs;
		}

		/**
		 * Plugin enqueues scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			$dependencies = array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'wp-util' );

			if ( is_admin() ) {

			} else {
				if ( is_singular( 'hb_room' ) ) {
					wp_enqueue_script( 'wphb-library-moment' );
					wp_enqueue_style( 'wphb-library-fullcalendar' );
					wp_enqueue_script( 'wphb-library-fullcalendar' );
					wp_enqueue_script( 'wphb-calendar-site', WPHB_CALENDAR_URL . 'assets/js/site.js', $dependencies );
					wp_localize_script( 'wphb-calendar-site', 'wphb_calendar_booking', array( 'booking' => $this->get_booking( get_the_ID() ) ) );
				}
			}
		}


		public function get_booking( $id ) {

			global $wpdb;

			$query = $wpdb->prepare( "
			SELECT check_in.meta_value AS check_in, check_out.meta_value AS check_out FROM {$wpdb->hotel_booking_order_itemmeta} AS product_id
				LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_in ON product_id.hotel_booking_order_item_id = check_in.hotel_booking_order_item_id AND check_in.meta_key = %s
				LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS check_out ON product_id.hotel_booking_order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
				LEFT JOIN {$wpdb->hotel_booking_order_items} AS booking_items ON product_id.hotel_booking_order_item_id = booking_items.order_item_id
				LEFT JOIN {$wpdb->posts} AS booking on booking_items.order_id = booking.ID
			WHERE
				booking.post_type = %s 
		  		AND booking.post_status IN ( %s, %s, %s)
		  		AND product_id.meta_key = %s
		  		AND product_id.meta_value = %d
			", 'check_in_date', 'check_out_date', 'hb_booking', 'hb-completed', 'hb-pending', 'hb-processing', 'product_id', $id );

			$query = apply_filters( 'hb_calendar_booking_query', $query );

			$data = array();

			if ( $bookings = $wpdb->get_results( $query, ARRAY_A ) ) {
				foreach ( $bookings as $key => $booking ) {
					$data[] = array(
						'start'     => date( hb_get_date_format(), $booking['check_in'] ),
						'end'       => date( hb_get_date_format(), $booking['check_out'] )
					);
				}
			}

			return $data;
		}

		/**
		 * Booking calendar admin view.
		 *
		 * @since 2.0
		 */
		public function booking_calendar() {
			require_once WPHB_CALENDAR_ABSPATH . '/includes/admin/views/calendar.php';
		}

		/**
		 * WPHB_Calendar instance.
		 *
		 * @since 2.0
		 *
		 * @return null|WPHB_Calendar
		 */
		public static function instance() {
			if ( self::$instance ) {
				return self::$instance;
			}

			return self::$instance = new self();
		}

	}

}

WPHB_Calendar::instance();