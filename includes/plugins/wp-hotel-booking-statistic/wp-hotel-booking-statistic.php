<?php

/*
    Plugin Name: WP Hotel Booking Statistic
    Plugin URI: https://thimpress.com/
    Description: Statistic booking for WP Hotel Booking
    Author: ThimPress
    Version: 2.0
    Author URI: http://thimpress.com
    Requires at least: 4.2
	Tested up to: 4.8
*/

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WP_Hotel_Booking_Statistic' ) ) {

	/**
	 * Main WP Hotel Booking Statistic Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Statistic {

		/**
		 * WP Hotel Booking Statistic version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_Statistic constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Statistic.
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
		 * Define WP Hotel Booking Statistic constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_STATISTIC_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WPHB_STATISTIC_URI', plugin_dir_url( __FILE__ ) );
			define( 'WPHB_STATISTIC_VER', $this->_version );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_STATISTIC_ABSPATH . '/includes/abstracts/class-wphb-abstract-statistic.php';
			require_once WPHB_STATISTIC_ABSPATH . '/includes/class-wphb-statistic.php';
			require_once WPHB_STATISTIC_ABSPATH . '/includes/class-wphb-statistic-price.php';
			require_once WPHB_STATISTIC_ABSPATH . '/includes/class-wphb-statistic-room.php';
			require_once WPHB_STATISTIC_ABSPATH . '/includes/wphb-statistic-functions.php';
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );
			add_filter( 'hotel_booking_menu_items', array( $this, 'admin_sub_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-statistic-' . get_locale() . '.mo';
			$plugin_file = WPHB_STATISTIC_ABSPATH . '/languages/wp-hotel-booking-statistic-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-statistic', $file );
			}
		}

		/**
		 * Add Statistic sub menu in WP Hotel Booking plugin menu.
		 *
		 * @since 2.0
		 *
		 * @param  $menus array
		 *
		 * @return array
		 */
		public function admin_sub_menu( $menus ) {
			$menus['statistic'] = array(
				'tp_hotel_booking',
				__( 'Statistic', 'wphb-statistic' ),
				__( 'Statistic', 'wphb-statistic' ),
				'manage_hb_booking',
				'wphb-statistic',
				array( $this, 'booking_statistic' )
			);

			return $menus;
		}

		/**
		 * Statistic admin view.
		 *
		 * @since 2.0
		 */
		public function booking_statistic() {
			require_once WPHB_STATISTIC_ABSPATH . 'includes/admin/views/statistic.php';
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			// chart js
			wp_enqueue_script( 'wphb-statistic-chart-js', WPHB_STATISTIC_URI . 'assets/js/Chart.min.js' );
			// tokenize js
			wp_enqueue_script( 'wphb-statistic-tokenize-js', WPHB_STATISTIC_URI . 'assets/js/jquery.tokenize.min.js' );
			wp_enqueue_style( 'wphb-statistic-tokenize-css', WPHB_STATISTIC_URI . 'assets/css/jquery.tokenize.min.css' );

			// admin scripts
			wp_enqueue_style( 'wphb-statistic-js', WPHB_STATISTIC_URI . 'assets/css/admin.css' );
			wp_enqueue_script( 'wphb-statistic-js', WPHB_STATISTIC_URI . 'assets/js/admin.js', array(
				'jquery',
				'jquery-ui-datepicker'
			) );
			wp_localize_script( 'wphb-statistic-js', 'wphb_statistic_js', hb_admin_i18n() );
		}

		/**
		 * Admin notice when WP Hotel Booking not active.
		 *
		 * @since 2.0
		 */
		public function add_notices() { ?>
            <div class="error">
                <p>
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Statistic</strong> add-on.', 'wphb-statistic' ), array( 'strong' => array() ) ); ?>
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking Statistic</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-statistic' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

	}

}

new WP_Hotel_Booking_Statistic();
