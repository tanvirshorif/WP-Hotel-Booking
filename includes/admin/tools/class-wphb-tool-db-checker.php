<?php
/**
 * WP Hotel Booking admin database checker class.
 *
 * @class       WPHB_Admin_Tool_DB_Checker
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_DB_Checker' ) ) {

	/**
	 * Class WPHB_Admin_Tool_DB_Checker.
	 */
	class WPHB_Admin_Tool_DB_Checker extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'db_checker';

		/**
		 * WPHB_Admin_Tool_DB_Checker constructor.
		 */
		public function __construct() {
			$this->title = __( 'Database', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			echo 'yyy';
		}

	}

}

return new WPHB_Admin_Tool_DB_Checker();
