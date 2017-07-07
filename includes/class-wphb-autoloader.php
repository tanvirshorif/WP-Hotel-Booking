<?php

/**
 * WP Hotel Booking autoloader class.
 *
 * @class       WPHB_Autoloader
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Autoloader' ) ) {

	/**
	 * Class WPHB_Autoloader.
	 *
	 * @since 2.0
	 */
	class WPHB_Autoloader {

		/**
		 * WPHB_Autoloader constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			if ( function_exists( "__autoload" ) ) {
				spl_autoload_register( "__autoload" );
			}

			spl_autoload_register( array( $this, 'autoload' ) );
		}

		/**
		 * Take a class name and turn it into a file name.
		 *
		 * @since 2.0
		 *
		 * @param  string $class
		 *
		 * @return string
		 */
		private function get_file_name_from_class( $class ) {
			return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		}

		/**
		 * Include a class file.
		 *
		 * @since 2.0
		 *
		 * @param  string $path
		 *
		 * @return bool successful or not
		 */
		private function load_file( $path ) {
			if ( $path && is_readable( $path ) ) {
				include_once( $path );

				return true;
			}

			return false;
		}

		/**
		 * Auto-load WPHB classes on demand to reduce memory consumption.
		 *
		 * @since 2.0
		 *
		 * @param $class
		 */
		public function autoload( $class ) {
			$class = strtolower( $class );

			$file = $this->get_file_name_from_class( $class );
			$path = WPHB_INCLUDES;

			if ( stripos( $class, 'wphb_abstract_' ) === 0 ) {
				// abstract class
				$path = WPHB_INCLUDES . '/abstracts/';
			} else if ( stripos( $class, 'wphb_admin_metabox_' ) === 0 ) {
				// admin metaboxes
				$path = WPHB_INCLUDES . '/admin/metaboxes/';
			} else if ( strpos( $class, 'wphb_payment_gateway_' ) === 0 ) {
				// payment gateways
				$path = WPHB_INCLUDES . 'gateways/';
			} else if ( stripos( $class, 'hb_widget_' ) === 0 ) {
				// widgets
				$path = WPHB_INCLUDES . '/widgets/';
			}

			$this->load_file( $path . $file );

		}

	}

}

new WPHB_Autoloader();
