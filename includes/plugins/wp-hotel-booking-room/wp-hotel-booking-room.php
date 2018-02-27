<?php

/*
    Plugin Name: WP Hotel Booking Room
    Plugin URI: https://thimpress.com/
    Description: Support book room without search room.
    Author: ThimPress
    Version: 2.0
    Author URI: http://thimpress.com
    Requires at least: 4.2
	Tested up to: 4.9.4
    Tags: wphb, wp-hotel-booking
*/

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WP_Hotel_Booking_Room' ) ) {

	/**
	 * Main WP Hotel Booking Room Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Room {

		/**
		 * WP Hotel Booking Room version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * @var null
		 */
		static $instance = null;

		/**
		 * @var bool
		 */
		public $available = false;

		/**
		 * @var null
		 */
		public $booking = null;

		/**
		 * WP_Hotel_Booking_Room constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Room.
		 *
		 * @since 2.0
		 */
		public function init() {
			if ( self::wphb_is_active() ) {
				if ( WPHB_VERSION && version_compare( WPHB_VERSION, '2.0' ) >= 0 ) {
					$this->define_constants();
					$this->includes();
					$this->init_hooks();
				} else {
					add_action( 'admin_notices', array( $this, 'required_update' ) );
				}
			} else {
				add_action( 'admin_notices', array( $this, 'add_notices' ) );
			}
		}

		/**
		 * Check WP Hotel Booking plugin active.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public static function wphb_is_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			return is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' );
		}

		/**
		 * Define WP Hotel Booking Room constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_ROOM_ABSPATH', plugin_dir_path( __FILE__ ) );
			define( 'WPHB_ROOM_URI', plugins_url( '', __FILE__ ) );
			define( 'WPHB_ROOM_VER', $this->_version );
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			// add admin settings
			add_filter( 'hotel_booking_admin_setting_fields_room', array( $this, 'admin_settings' ) );

			add_action( 'init', array( $this, 'load_text_domain' ) );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_ROOM_ABSPATH . 'includes/class-wphb-booking-room.php';
			require_once WPHB_ROOM_ABSPATH . 'includes/wphb-booking-room-functions.php';
		}

		/**
		 * Admin settings option.
		 *
		 * @since 2.0
		 *
		 * @param $settings
		 *
		 * @return array
		 */
		public function admin_settings( $settings ) {

			$prefix = 'tp_hotel_booking_';

			$booking_room_settings = apply_filters( 'wphb_room_admin_setting_fields', array(
				array(
					'type'  => 'section_start',
					'id'    => 'booking_room_settings',
					'title' => __( 'Booking Room Add-on', 'wphb-booking-room' ),
					'desc'  => __( 'Settings for WP Hotel Booking Room add-on', 'wphb-booking-room' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'enable_archive_book',
					'title'   => __( 'Archive room page', 'wphb-booking-room' ),
					'default' => 0,
					'desc'    => __( 'Enable Book Now in archive room page', 'wphb-booking-room' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'enable_single_book',
					'title'   => __( 'Single room page', 'wphb-booking-room' ),
					'default' => 0,
					'desc'    => __( 'Enable Book Now in single room page', 'wphb-booking-room' )
				),
				array(
					'type' => 'section_end',
					'id'   => 'booking_room_settings'
				),
			) );

			return array_merge( $settings, $booking_room_settings );
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-room-' . get_locale() . '.mo';
			$plugin_file = WPHB_ROOM_ABSPATH . '/languages/wp-hotel-booking-room-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-booking-room', $file );
			}
		}

		/**
		 * Admin notice when WP Hotel Booking not active.
		 *
		 * @since 2.0
		 */
		public function add_notices() { ?>
            <div class="error">
                <p>
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Room</strong> add-on.', 'wphb-booking-room' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Admin notice required update WP Hotel Booking 2.0.
		 *
		 * @since 2.0
		 */
		public function required_update() { ?>
            <div class="error">
                <p>
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking Room</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-booking-room' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

		static function instance() {
			if ( is_null( self::$instance ) ) {
				return self::$instance = new self();
			}

			return self::$instance;
		}

	}
}

WP_Hotel_Booking_Room::instance();