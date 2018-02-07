<?php

/*
    Plugin Name: WP Hotel Booking WPML Support
    Plugin URI: https://thimpress.com/
    Description: Integrated WPML with WP Hotel Booking
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


if ( ! class_exists( 'WP_Hotel_Booking_WPML_Support' ) ) {

	/**
	 * Main WP Hotel Booking WPML Support Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_WPML_Support {

		/**
		 * WP Hotel Booking WPML Support version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_WPML_Support constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_WPML_Support.
		 *
		 * @since 2.0
		 */
		public function init() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'SitePress' ) ) {
				if ( WPHB_VERSION && version_compare( WPHB_VERSION, '2.0' ) >= 0 ) {
					$this->define_constants();
					$this->includes();
					$this->init_hooks();
				} else {
					add_action( 'admin_notices', array( $this, 'required_update' ) );
				}
			} else if ( ! is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) ) {
				add_action( 'admin_notices', array( $this, 'add_wphb_notices' ) );
			} else if ( ! class_exists( 'SitePress' ) ) {
				add_action( 'admin_notices', array( $this, 'add_wpml_notices' ) );
			}

		}

		/**
		 * Define WP Hotel Booking WPML constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_WPML_ABSPATH', plugin_dir_path( __FILE__ ) );
			define( 'WPHB_WPML_VER', $this->_version );
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_WPML_ABSPATH . 'includes/class-wphb-support.php';
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-wpml-support-' . get_locale() . '.mo';
			$plugin_file = WPHB_WPML_ABSPATH . 'languages/wp-hotel-booking-wpml-support-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-wpml', $file );
			}
		}

		/**
		 * Admin notice when WP Hotel Booking not active.
		 *
		 * @since 2.0
		 */
		public function add_wphb_notices() { ?>
            <div class="error">
                <p>
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WPML Support</strong> add-on.', 'wphb-wpml' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Admin notice when WPML CSM not active.
		 *
		 * @since 2.0
		 */
		public function add_wpml_notices() { ?>
            <div class="error">
                <p>
					<?php _e( wp_kses( 'The <strong>WPML Multilingual CMS</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WPML Support</strong> add-on.', array( 'strong' => array() ) ), 'wphb-wpml' ); ?>
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking WPML Support</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-wpml' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}
	}
}

new WP_Hotel_Booking_WPML_Support();