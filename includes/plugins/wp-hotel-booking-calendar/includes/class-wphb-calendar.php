<?php

/**
 * WP Hotel Booking Calendar class.
 *
 * @class       WPHB_Calendar
 * @version     2.0
 * @package     WP_Hotel_Booking_Calendar/Classes
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

			// ajax
			add_action( 'wp_ajax_wphb_load_booking_calendar', array( $this, 'load_booking_calendar' ) );
			add_action( 'wp_ajax_nopriv_wphb_load_booking_calendar', array( $this, 'load_booking_calendar' ) );
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
				__( 'Booking Calendar', 'wphb-calendar' ),
				__( 'Booking Calendar', 'wphb-calendar' ),
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
				'title'   => __( 'Booking Calendar', 'wp-calendar' ),
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

			if ( ! is_admin() ) {
				if ( is_singular( 'hb_room' ) ) {
					wp_enqueue_script( 'wphb-library-moment' );
					wp_enqueue_style( 'wphb-library-fullcalendar' );
					wp_enqueue_script( 'wphb-library-fullcalendar' );
					wp_enqueue_script( 'wphb-calendar-site', WPHB_CALENDAR_URL . 'assets/js/site.js', $dependencies );
					wp_localize_script( 'wphb-calendar-site', 'wphb_calendar_booking', array( 'booking' => wphb_calendar_get_room_bookings( get_the_ID() ) ) );
				}
			}
		}

		/**
		 * Booking calendar admin view.
		 *
		 * @since 2.0
		 */
		public function booking_calendar() {
			require_once WPHB_CALENDAR_ABSPATH . '/includes/admin/views/calendar.php';
		}

		public static function load_booking_calendar() {

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