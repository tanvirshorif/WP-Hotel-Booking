<?php

/*
    Plugin Name: WP Hotel Booking WPML Support
    Plugin URI: https://thimpress.com/
    Description: Integrated WPML with WP Hotel Booking
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


if ( ! class_exists( 'WP_Hotel_Booking_WPML_Support' ) ) {

	/**
	 * Main WP Hotel Booking WPML Support Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Wpml_Support {

		/**
		 * WP Hotel Booking WPML Support version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_Woocommerce constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Woocommerce.
		 *
		 * @since 2.0
		 */
		public function init() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'SitePress' ) ) {
				$this->define_constants();
				$this->includes();
				$this->init_hooks();
			} else if ( ! is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) ) {
				add_action( 'admin_notices', array( $this, 'add_wphb_notices' ) );
			} else if ( ! class_exists( 'SitePress' ) ) {
				add_action( 'admin_notices', array( $this, 'add_wpml_notices' ) );
			}

		}

		/**
		 * Define WP Hotel Booking Woocommerce constants.
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
					<?php _e( wp_kses( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WooCommerce</strong> add-on.', array( 'strong' => array() ) ), 'wphb-wpml' ); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Admin notice when Woocommerce not active.
		 *
		 * @since 2.0
		 */
		public function add_wpml_notices() { ?>
            <div class="error">
                <p>
					<?php _e( wp_kses( 'The <strong>WPML Multilingual CMS</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WooCommerce</strong> add-on.', array( 'strong' => array() ) ), 'wphb-wpml' ); ?>
                </p>
            </div>
			<?php
		}
	}
}

new WP_Hotel_Booking_WPML_Support();
