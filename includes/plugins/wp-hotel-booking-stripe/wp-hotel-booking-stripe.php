<?php

/*
    Plugin Name: WP Hotel Booking Stripe Payment
    Plugin URI: https://thimpress.com/
    Description: Stripe payment gateway for WP Hotel Booking
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


if ( ! class_exists( 'WP_Hotel_Booking_Stripe_Payment' ) ) {

	/**
	 * Main WP Hotel Booking Stripe Payment Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Stripe_Payment {

		/**
		 * WP Hotel Booking Stripe Payment version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * @var string
		 */
		public $_slug = 'stripe';

		/**
		 * WP_Hotel_Booking_Stripe_Payment constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Stripe_Payment.
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
		 * Define WP Hotel Booking Stripe Payment constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_STRIPE_PAYMENT_ABSPATH', plugin_dir_path( __FILE__ ) );
			define( 'WPHB_STRIPE_PAYMENT_URI', plugins_url( '', __FILE__ ) );
			define( 'WPHB_STRIPE_PAYMENT_VER', $this->_version );

		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );
			add_filter( 'hb_payment_gateways', array( $this, 'add_payment_gateway' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_STRIPE_PAYMENT_ABSPATH . '/includes/class-wphb-stripe-payment.php';
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-stripe-' . get_locale() . '.mo';
			$plugin_file = WPHB_STRIPE_PAYMENT_ABSPATH . '/languages/wp-hotel-booking-stripe-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-stripe-payment', $file );
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

			$payments[ $this->_slug ] = new WPHB_Payment_Gateway_Stripe();

			return $payments;
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			// stripe and checkout assets
			wp_register_script( 'wphb-stripe-js', WPHB_STRIPE_PAYMENT_URI . '/assets/js/stripe.js', array() );
			wp_register_script( 'wphb-stripe-checkout-js', WPHB_STRIPE_PAYMENT_URI . '/assets/js/checkout.js', array() );

			$setting = WPHB_Settings::instance()->get( 'stripe' );

			if ( ! empty( $setting['enable'] ) && $setting['enable'] == 'on' ) {
				wp_enqueue_script( 'wphb-stripe-js' );
				wp_enqueue_script( 'wphb-stripe-checkout-js' );
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Stripe Payment</strong> add-on.', 'wphb-stripe-payment' ), array( 'strong' => array() ) ); ?>
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking Stripe Payment</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-stripe-payment' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

	}
}

new WP_Hotel_Booking_Stripe_Payment();
