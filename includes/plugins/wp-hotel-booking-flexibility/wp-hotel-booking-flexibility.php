<?php

/*
    Plugin Name: WP Hotel Booking Flexibility
    Plugin URI: https://thimpress.com/
    Description: Support time picker for booking room process
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


if ( ! class_exists( 'WP_Hotel_Booking_Flexibility' ) ) {

	/**
	 * Main WP Hotel Booking Flexibility Class.
	 *
	 * @version    2.0
	 */
	final class WP_Hotel_Booking_Flexibility {

		/**
		 * WP Hotel Booking Flexibility version.
		 *
		 * @var string
		 */
		public $_version = '2.0';

		/**
		 * WP_Hotel_Booking_Flexibility constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init WP_Hotel_Booking_Flexibility.
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
		 * Define WP Hotel Booking Flexibility constants.
		 *
		 * @since 2.0
		 */
		private function define_constants() {
			define( 'WPHB_FLEX_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WPHB_FLEX_URI', plugin_dir_url( __FILE__ ) );
			define( 'WPHB_FLEX_VER', $this->_version );
		}

		/**
		 * Include required core files.
		 *
		 * @since 2.0
		 */
		public function includes() {
			require_once WPHB_FLEX_ABSPATH . '/includes/class-wphb-flexibility.php';
		}

		/**
		 * Main hooks.
		 *
		 * @since 2.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'load_text_domain' ) );

			add_filter( 'hb_admin_settings_tab_after', array( $this, 'admin_settings' ) );
		}

		/**
		 * Load text domain.
		 *
		 * @since 2.0
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-flexibility-' . get_locale() . '.mo';
			$plugin_file = WPHB_FLEX_ABSPATH . '/languages/wp-hotel-booking-flexibility-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wphb-flex', $file );
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Flexibility</strong> add-on.', 'wphb-flex' ), array( 'strong' => array() ) ); ?>
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
					<?php echo wp_kses( __( 'The <strong>WP Hotel Booking Flexibility</strong> add-on requires <strong>WP Hotel Booking</strong> version 2.0 or higher.', 'wphb-flex' ), array( 'strong' => array() ) ); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Admin setting option.
		 *
		 * @since 2.0
		 *
		 * @param $tabs
		 */
		public function admin_settings(
			$tabs
		) {
			if ( $tabs !== 'pages' ) {
				return;
			}
			$settings   = hb_settings();
			$field_name = $settings->get_field_name( 'flexible_booking' );
			?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Flexible Booking', 'wphb-booking-room' ); ?></th>
                    <td>
                        <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="0"/>
                        <input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>"
						       <?php checked( $settings->get( 'flexible_booking' ) ? 1 : 0, 1 ); ?>value="1"/>
                        <p class="description"><?php _e( 'Enable flexible booking', 'wphb-flex' ); ?></p>
                    </td>
                </tr>
            </table>
			<?php
		}
	}

}

new WP_Hotel_Booking_Flexibility();
