<?php
/*
    Plugin Name: WP Hotel Booking Block Room
    Plugin URI: https://thimpress.com/
    Description: Block booking rooms for specific dates
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


if ( ! class_exists( 'WP_Hotel_Booking_Block' ) ) {

	/**
	 * Main WP Hotel Booking Block Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Block {

		/**
		 * WP Hotel Booking Block version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_Block constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Block.
		 *
		 * @since 2.0
		 */
		public function init() {
			if ( self::wphb_is_active() ) {
				$this->define_constants();
				$this->includes();
				$this->init_hooks();
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
		 * Define WP Hotel Booking Coupon constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_BLOCK_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WPHB_BLOCK_URI', plugin_dir_url( __FILE__ ) );
			define( 'WPHB_BLOCK_VER', $this->_version );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_BLOCK_ABSPATH . '/includes/class-wphb-block.php';
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
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-block-' . get_locale() . '.mo';
			$plugin_file = WPHB_BLOCK_ABSPATH . '/languages/wp-hotel-booking-block-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-block', $file );
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Block Room</strong> add-on.', 'wphb-block' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

	}

}

new WP_Hotel_Booking_Block();
