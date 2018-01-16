<?php
/*
    Plugin Name: WP Hotel Booking
    Plugin URI: https://thimpress.com/
    Description: Full of professional features for a booking room system
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


if ( ! class_exists( 'WP_Hotel_Booking' ) ) {

	/**
	 * Main WP Hotel Booking Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking {

		/**
		 * Hold the instance of main class.
		 *
		 * @var object
		 */
		protected static $_instance = null;

		/**
		 * WP Hotel Booking version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * User.
		 *
		 * @var null
		 */
		public $user = null;

		/**
		 * @var array
		 */
		public $query_vars = array();

		/**
		 * WP_Hotel_Booking constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Define WP Hotel Booking constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_FILE', __FILE__ );
			define( 'WPHB_PLUGIN_PATH', dirname( __FILE__ ) );
			define( 'WPHB_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/' );
			define( 'WPHB_BLOG_ID', get_current_blog_id() );

			define( 'WPHB_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WPHB_INCLUDES', WPHB_ABSPATH . 'includes/' );
			define( 'WPHB_VERSION', $this->_version );
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'WPHB_Install', 'install' ) );
			register_activation_hook( __FILE__, array( 'WPHB_Upgrade', 'upgrade' ) );
			register_activation_hook( __FILE__, array( 'WPHB_Install', 'uninstall' ) );

			add_action( 'init', array( $this, 'init' ), 20 );

//			add_action( 'wp_loaded', array( 'WPHB_Cart', 'wp_loaded' ) );

			// create new blog in multisite
			add_action( 'wpmu_new_blog', array( 'WPHB_Install', 'create_new_blog' ) );
			// delete table in multisite
			add_filter( 'wpmu_drop_tables', array( 'WPHB_Install', 'delete_tables' ) );
		}

		/**
		 * Init WP Hotel Booking when Wordpress initialises.
		 *
		 * @since 2.0
		 */
		public function init() {

			// Set up localisation
			$this->load_text_domain();

			// Load class instances.
			$this->user = hb_get_current_user();

//			$this->cart = WPHB_Cart::instance();
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {

			include_once( WPHB_INCLUDES . 'class-wphb-autoloader.php' );
			if ( is_admin() ) {
				include_once( WPHB_INCLUDES . 'admin/class-wphb-admin.php' );
			} else {
				if ( ! class_exists( 'Aq_Resize' ) ) {
					include_once( WPHB_INCLUDES . 'aq_resizer.php' );
				}
			}

			include_once( WPHB_INCLUDES . 'class-wphb-template-loader.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-ajax.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-install.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-upgrade.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-shortcodes.php' );

			include_once( WPHB_INCLUDES . 'class-wphb-assets.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-settings.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-addons.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-comments.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin.php' );
			include_once( WPHB_INCLUDES . 'admin/background/class-wphb-background-query-items.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin.php' );
			include_once( WPHB_INCLUDES . 'wphb-template-hooks.php' );
			include_once( WPHB_INCLUDES . 'wphb-template-functions.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-resizer.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-post-types.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-query.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-roles.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-user.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-sessions.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-cart.php' );
			include_once( WPHB_INCLUDES . 'class-wphb-checkout.php' );

			include_once( WPHB_INCLUDES . 'wphb-core-functions.php' );
			include_once( WPHB_INCLUDES . 'wphb-functions.php' );
			include_once( WPHB_INCLUDES . 'wphb-webhooks.php' );

			// interface
			include_once( WPHB_INCLUDES . 'interfaces/interface-curd.php' );

			// curd
			include_once( WPHB_INCLUDES . 'curd/class-wphb-extra-curd.php' );
			include_once( WPHB_INCLUDES . 'curd/class-wphb-room-curd.php' );

			// room
			include_once( WPHB_INCLUDES . 'room/class-wphb-room.php' );
			include_once( WPHB_INCLUDES . 'room/wphb-room-functions.php' );

			// extra package
			include_once( WPHB_INCLUDES . 'extra/class-wphb-extra.php' );
			include_once( WPHB_INCLUDES . 'extra/class-wphb-extra-package.php' );
			include_once( WPHB_INCLUDES . 'extra/class-wphb-extra-product.php' );

			// booking
			include_once( WPHB_INCLUDES . 'booking/wphb-booking-functions.php' );
			include_once( WPHB_INCLUDES . 'booking/wphb-booking-hooks.php' );
			include_once( WPHB_INCLUDES . 'booking/class-wphb-booking.php' );
		}

		/**
		 * Load language for the plugin.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {

			$prefix = basename( dirname( plugin_basename( __FILE__ ) ) );
			$locale = get_locale();
			$dir    = WPHB_ABSPATH . 'languages';
			$mofile = false;

			$globalFile = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
			$pluginFile = $dir . '/' . $prefix . '-' . $locale . '.mo';

			if ( file_exists( $globalFile ) ) {
				$mofile = $globalFile;
			} else if ( file_exists( $pluginFile ) ) {
				$mofile = $pluginFile;
			}

			if ( $mofile ) {
				// In themes/plugins/mu-plugins directory
				load_textdomain( 'wp-hotel-booking', $mofile );
			}
		}

		/**
		 * Create an instance of the plugin if it is not created.
		 *
		 * @since 2.0
		 *
		 * @return object|WP_Hotel_Booking
		 */
		static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

}

if ( ! function_exists( 'WPHB' ) ) {
	/**
	 * Main instance of WP Hotel Booking.
	 *
	 * @since 2.0
	 *
	 * @return object|WP_Hotel_Booking
	 */
	function WPHB() {
		return WP_Hotel_Booking::instance();
	}
}

$GLOBALS['wp_hotel_booking'] = WP_Hotel_Booking::instance();