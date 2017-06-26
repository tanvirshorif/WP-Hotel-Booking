<?php

/**
 * Abstract WP Hotel Booking payment gateway class.
 *
 * @class       WPHB_Abstract_Payment_Gateway
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Abstract_Payment_Gateway' ) ) {

	/**
	 * Class WPHB_Abstract_Payment_Gateway.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_Payment_Gateway {

		/**
		 * @var string
		 */
		protected $_title = '';

		/**
		 * @var string
		 */
		protected $_description = '';

		/**
		 * @var string
		 */
		protected $_slug = '';

		/**
		 * WPHB_Abstract_Payment_Gateway constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
		}

		/**
		 * Get payment data.
		 *
		 * @since 2.0
		 *
		 * @param $key
		 *
		 * @return bool|string
		 */
		public function __get( $key ) {
			$return = false;
			switch ( $key ) {
				case 'title':
					$return = $this->_title;
					break;
				case 'description':
					$return = $this->_description;
					break;
				case 'slug':
					if ( empty( $this->_slug ) ) {
						$return = sanitize_title( $this->_title );
					} else {
						$return = $this->_slug;
					}
			}

			return $return;
		}

		/**
		 * Check payment gateway enable.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_enable() {
			return false;
		}

		/**
		 * Checkout process.
		 *
		 * @since 2.0
		 *
		 * @param null $customer_id
		 *
		 * @return array
		 */
		public function process_checkout( $customer_id = null ) {
			return array(
				'result' => ''
			);
		}

	}
}