<?php

/**
 * WP Hotel Booking admin class.
 *
 * @class       WPHB_Admin
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin' ) ) {
	/**
	 * Class WPHB_Admin.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin {
		/**
		 * WPHB_Admin constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			// include files
			$this->includes();
		}

		/**
		 * Include admin component.
		 *
		 * @since 2.0
		 */
		public function includes() {
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-ajax.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-settings.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-menu.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-tools.php' );
			include_once( WPHB_INCLUDES . 'admin/wphb-admin-functions.php' );
		}
	}
}

new WPHB_Admin();
