<?php

/*
    Plugin Name: WP Hotel Booking Authorize Payment
    Plugin URI: https://thimpress.com/
    Description: Authorize payment gateway for WP Hotel Booking
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


if ( ! class_exists( 'WP_Hotel_Booking_Authorize_Payment' ) ) {

	/**
	 * Main WP Hotel Booking Authorize Payment Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Authorize_Payment {

		/**
		 * WP Hotel Booking Authorize Payment version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * @var string
		 */
		public $_slug = 'authorize';

		/**
		 * WP_Hotel_Booking_Authorize_Payment constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Authorize_Payment.
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
		 * Define WP Hotel Booking Authorize Payment constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_AUTHORIZE_PAYMENT_ABSPATH', plugin_dir_path( __FILE__ ) );
			define( 'WPHB_AUTHORIZE_PAYMENT_VER', $this->_version );

		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );
			add_filter( 'hb_payment_gateways', array( $this, 'add_payment_gateway' ) );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_AUTHORIZE_PAYMENT_ABSPATH . '/includes/class-wphb-authorize-payment.php';
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-authorize-' . get_locale() . '.mo';
			$plugin_file = WPHB_AUTHORIZE_PAYMENT_ABSPATH . '/languages/wp-hotel-booking-authorize-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-authorize-payment', $file );
			}
		}

		/**
		 * Add Authorize to WP Hotel Booking payment gateways.
		 *
		 * @since 2.0
		 *
		 * @param $payments
		 *
		 * @return mixed
		 */
		public function add_payment_gateway( $payments ) {
			if ( array_key_exists( $this->_slug, $payments ) ) {
				return $payments;
			}

			$payments[ $this->_slug ] = new WPHB_Payment_Gateway_Authorize();

			return $payments;
		}

		/**
		 * Admin notice when WP Hotel Booking not active.
		 *
		 * @since 2.0
		 */
		public function add_notices() { ?>
            <div class="error">
                <p>
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Authorize Payment</strong> add-on.', 'wphb-authorize-payment' ), array( 'strong' => array() ) ); ?>
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking Authorize Payment</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-authorize-payment' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

	}
}

new WP_Hotel_Booking_Authorize_Payment();
