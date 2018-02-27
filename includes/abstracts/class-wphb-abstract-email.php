<?php

/**
 * Abstract WP Hotel Booking Email class.
 *
 * @class       WPHB_Abstract_Email
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Abstract_Email' ) ) {
	/**
	 * Class WPHB_Abstract_Email
	 */
	class WPHB_Abstract_Email {

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
		 * WPHB_Abstract_Email constructor.
		 */
		public function __construct() {
			add_filter( 'wphb_admin_setting_email_sections', array( $this, 'add_section' ) );
			add_filter( 'wphb_admin_setting_fields_emails', array( $this, 'add_fields' ), 10, 2 );
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
	}
}