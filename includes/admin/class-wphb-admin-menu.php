<?php

/**
 * WP Hotel Booking admin menu class.
 *
 * @class       WPHB_Admin_Menu
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Admin_Menu' ) ) {

	/**
	 * Class WPHB_Admin_Menu.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Menu {

		/**
		 * WPHB_Admin_Menu constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		}

		/**
		 * Register plugin menu.
		 *
		 * @since 2.0
		 */
		public function plugin_menu() {

			// Register menu.
			add_menu_page(
				__( 'WP Hotel Booking', 'wp-hotel-booking' ),
				__( 'WP Hotel Booking', 'wp-hotel-booking' ),
				'edit_hb_bookings',
				'tp_hotel_booking',
				'',
				'dashicons-calendar',
				'3.99'
			);


			$menu_items = array(
				'pricing_table' => array(
					'tp_hotel_booking',
					__( 'Pricing Plans', 'wp-hotel-booking' ),
					__( 'Pricing Plans', 'wp-hotel-booking' ),
					'manage_hb_booking',
					'wphb-pricing-table',
					array( $this, 'pricing_table' )
				),
				'extra_package' => array(
					'tp_hotel_booking',
					__( 'Addition Packages', 'wp-hotel-booking' ),
					__( 'Addition Packages', 'wp-hotel-booking' ),
					'manage_hb_booking',
					'wphb-addition-packages',
					array( $this, 'addition_packages' )
				)
			);

			// Third-party can be add more items
			$menu_items = apply_filters( 'hotel_booking_menu_items', $menu_items );

			$menu_items['settings'] = array(
				'tp_hotel_booking',
				__( 'Settings', 'wp-hotel-booking' ),
				__( 'Settings', 'wp-hotel-booking' ),
				'manage_hb_booking',
				'wphb-settings',
				array( $this, 'settings_page' )
			);

			// Register submenu.
			if ( $menu_items ) {
				foreach ( $menu_items as $item ) {
					call_user_func_array( 'add_submenu_page', $item );
				}
			}

		}

		/**
		 * Addition packages view.
		 *
		 * @since 2.0
		 */
		public function addition_packages() {
			include_once( WPHB_ABSPATH . 'includes/admin/views/addition-packages.php' );
		}

		/**
		 * Setting page view.
		 *
		 * @since 2.0
		 */
		public function settings_page() {
			WPHB_Admin_Settings::output();
		}

		/**
		 * Pricing plan view.
		 *
		 * @since 2.0
		 */
		public function pricing_table() {
			wp_enqueue_script( 'wp-util' );
			include_once( WPHB_ABSPATH . 'includes/admin/views/pricing-table.php' );
		}
	}

}
new WPHB_Admin_Menu();
