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
			// add admin setting
			add_filter( 'wphb_admin_setting_payments_sections', array( $this, 'add_section' ) );
			add_filter( 'wphb_admin_setting_fields_payments', array( $this, 'add_fields' ), 10, 2 );
		}

		/**
		 * Add admin setting section.
		 *
		 * @param $sections
		 *
		 * @return mixed
		 */
		public function add_section( $sections ) {
			if ( $this->_slug && $this->_title ) {
				$sections[ $this->_slug ] = $this->_title;
			}

			return $sections;
		}

		/**
		 * Add admin setting fields for section.
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return array
		 */
		public function add_fields( $fields, $section ) {
			if ( $section == $this->_slug ) {
				$fields = $this->setting_fields();
			}

			return $fields;
		}

		/**
		 * Get setting fields.
		 *
		 * @return array
		 */
		public function setting_fields() {
			return array();
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