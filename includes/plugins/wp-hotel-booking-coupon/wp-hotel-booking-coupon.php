<?php

/*
    Plugin Name: WP Hotel Booking Coupon
    Plugin URI: https://thimpress.com/
    Description: WP Hotel Booking Coupon
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


if ( ! class_exists( 'WP_Hotel_Booking_Coupon' ) ) {

	/**
	 * Main WP Hotel Booking Coupon Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Coupon {

		/**
		 * WP Hotel Booking Coupon version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_Coupon constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Coupon.
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
			define( 'WPHB_COUPON_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WPHB_COUPON_URI', plugin_dir_url( __FILE__ ) );
			define( 'WPHB_COUPON_VER', $this->_version );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_COUPON_ABSPATH . '/includes/class-wphb-coupon.php';
			require_once WPHB_COUPON_ABSPATH . '/includes/class-wphb-coupon-post-types.php';
			require_once WPHB_COUPON_ABSPATH . '/includes/class-wphb-coupon-ajax.php';


			require_once WPHB_COUPON_ABSPATH . '/includes/admin/views/booking-coupon-js-template.php';
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'hotel_booking_before_cart_total', array( $this, 'add_form' ) );
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-coupon-' . get_locale() . '.mo';
			$plugin_file = WPHB_COUPON_ABSPATH . '/languages/wp-hotel-booking-coupon-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-coupon', $file );
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
					<?php _e( wp_kses( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Coupon</strong> add-on.', array( 'strong' => array() ) ), 'wphb-coupon' ); ?>
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
			if ( is_admin() ) {
				wp_enqueue_script( 'wphb-coupon-admin', WPHB_COUPON_URI . '/assets/js/admin.wphb-coupon.js', array( 'jquery' ), WPHB_COUPON_VER, true );
				wp_localize_script( 'wphb-coupon-admin', 'wphb_coupon', array(
					'select_coupon' => __( 'Enter coupon code.', 'wphb-coupon' ),
				) );
			} else {
				wp_enqueue_script( 'wphb-coupon-site', WPHB_COUPON_URI . '/assets/js/wphb-coupon.js', array( 'jquery' ), WPHB_COUPON_VER, true );
			}
		}

		/**
		 * Add coupon form in cart and checkout page.
		 *
		 * @since 2.0
		 */
		public function add_form() {
			$cart = WPHB_Cart::instance();
			if ( $coupon = $cart->coupon ) {
				$coupon = WPHB_Coupon::instance( $coupon );
				?>
                <tr class="hb_coupon">
                    <td class="hb_coupon_remove" colspan="8">
                        <p class="hb-remove-coupon" align="right">
                            <a href="" id="hb-remove-coupon"><i class="fa fa-times"></i></a>
                        </p>
                        <span class="hb-remove-coupon_code"><?php printf( __( 'Coupon applied: %s', 'wphb-coupon' ), $coupon->coupon_code ); ?></span>
                        <span class="hb-align-right">-<?php echo hb_format_price( $coupon->discount_value ); ?></span>
                    </td>
                </tr>
			<?php } else { ?>
                <tr class="hb_coupon">
                    <td colspan="8" class="hb-align-center">
                        <input type="text" name="hb-coupon-code" value=""
                               placeholder="<?php _e( 'Coupon', 'wphb-coupon' ); ?>"/>
                        <button type="button"
                                id="hb-apply-coupon"><?php _e( 'Apply Coupon', 'wphb-coupon' ); ?></button>
                    </td>
                </tr>
			<?php }
		}

	}

}

new WP_Hotel_Booking_Coupon();
