<?php

/**
 * Abstract WP Hotel Booking CURD class.
 *
 * @class       WPHB_Abstract_CURD
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Abstract_CURD' ) ) {
	/**
	 * Class WPHB_Abstract_CURD
	 */
	 class WPHB_Abstract_CURD {
		/**
		 * Errors codes and message.
		 *
		 * @var bool
		 */
		protected $_error_messages = false;

		/**
		 * Add new meta data.
		 *
		 * @param $object
		 * @param $meta
		 */
		public function add_meta( &$object, $meta ) {
			// TODO: Implement add_meta() method.
		}

		/**
		 * Delete meta data.
		 *
		 * @param $object
		 * @param $meta
		 */
		public function delete_meta( &$object, $meta ) {
			// TODO: Implement delete_meta() method.
		}

		/**
		 * Read all meta data from DB.
		 *
		 * @param $object
		 */
		public function read_meta( &$object ) {
		}

		/**
		 * Update meta data.
		 *
		 * @param $object
		 * @param $meta
		 */
		public function update_meta( &$object, $meta ) {
		}

		/**
		 * @param $type
		 */
		public static function get( $type ) {
		}

		/**
		 * Get WP_Object.
		 *
		 * @param $code
		 *
		 * @return bool|WP_Error
		 */
		protected function get_error( $code ) {
			if ( isset( $this->_error_messages[ $code ] ) ) {
				return new WP_Error( $code, $this->_error_messages[ $code ] );
			}

			return false;
		}
	}
}