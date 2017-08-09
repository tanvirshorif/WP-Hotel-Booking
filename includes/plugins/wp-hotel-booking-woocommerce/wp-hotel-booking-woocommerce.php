<?php

/*
    Plugin Name: WP Hotel Booking WooCommerce
    Plugin URI: https://thimpress.com/
    Description: Support paying for a booking with the payment system provided by WooCommerce
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


if ( ! class_exists( 'WP_Hotel_Booking_Woocommerce' ) ) {

	/**
	 * Main WP Hotel Booking Woocommerce Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Woocommerce {

		/**
		 * WP Hotel Booking Woocommerce version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * @var string
		 */
		public $_slug = 'woocommerce';

		/**
		 * Hold the instance of WP_Hotel_Booking_Woocommerce.
		 *
		 * @var null
		 */
		protected static $_instance = null;

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
			if ( self::required_plugins_is_active() ) {
				$this->define_constants();
				$this->includes();
				$this->init_hooks();
			} else if ( ! self::required_plugins_is_active( 'wp-hotel-booking' ) ) {
				add_action( 'admin_notices', array( $this, 'add_wphb_notices' ) );
			} else if ( ! self::required_plugins_is_active( 'woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'add_woo_notices' ) );
			}
		}

		/**
		 * Check WP Hotel Booking plugin active.
		 *
		 * @since 2.0
		 *
		 * @param $plugin
		 *
		 * @return bool
		 */
		public static function required_plugins_is_active( $plugin = null ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( ! $plugin ) {
				return is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && is_plugin_active( 'woocommerce/woocommerce.php' );
			} else {
				return is_plugin_active( "$plugin/$plugin.php" );
			}
		}

		/**
		 * Define WP Hotel Booking Woocommerce constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_WOO_PAYMENT_ABSPATH', plugin_dir_path( __FILE__ ) );
			define( 'WPHB_WOO_PAYMENT_URI', plugins_url( '', __FILE__ ) );
			define( 'WPHB_WOO_PAYMENT_VER', $this->_version );
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
			require_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/class-wphb-woocommerce.php';
			require_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/class-wphb-wc-product-room.php';
			require_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/class-wphb-wc-product-package.php';
			require_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/class-wphb-wc-checkout.php';
			require_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/class-wphb-wc-booking.php';
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

			$payments[ $this->_slug ] = new WPHB_Woocommerce();

			return $payments;
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public static function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-woocommerce-' . get_locale() . '.mo';
			$plugin_file = WPHB_WOO_PAYMENT_ABSPATH . '/languages/wp-hotel-booking-woocommerce-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-woocommerce', $file );
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
	                <?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WooCommerce</strong> add-on.', 'wphb-woocommerce' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Admin notice when Woocommerce not active.
		 *
		 * @since 2.0
		 */
		public function add_woo_notices() { ?>
            <div class="error">
                <p>
					<?php printf( wp_kses( __( 'The <strong><a href="%s" target="_blank">Woocommerce</a></strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking WooCommerce</strong> add-on.', 'wphb-woocommerce' ),
						array(
							'a'      => array( 'href' => array(), 'target' => array() ),
							'strong' => array()
						)
					), admin_url() . 'plugin-install.php?s=woocommerce&tab=search&type=term'
					);
					?>
                </p>
            </div>
			<?php
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wphb_wc_checkout', WPHB_WOO_PAYMENT_URI . '/assets/js/frontend/site.js', array( 'jquery' ) );
			wp_enqueue_style( 'wphb_wc_site', WPHB_WOO_PAYMENT_URI . '/assets/css/frontend/site.css' );
		}

		/**
		 * Ensure that only one instance of WP_Hotel_Booking_Woocommerce is loaded in a process
		 *
		 * @return null|WP_Hotel_Booking_Woocommerce
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

	}

}

WP_Hotel_Booking_Woocommerce::instance();