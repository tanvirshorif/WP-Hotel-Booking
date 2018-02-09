<?php
/**
 * WP Hotel Booking admin system status class.
 *
 * @class       WPHB_Admin_Tool_System_Status
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_System_Status' ) ) {

	/**
	 * Class WPHB_Admin_Tool_System_Status.
	 */
	class WPHB_Admin_Tool_System_Status extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'system_status';

		/**
		 * WPHB_Admin_Tool_System_Status constructor.
		 */
		public function __construct() {
			$this->title = __( 'System Status', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			echo 'ttt';
		}

	}

}

return new WPHB_Admin_Tool_System_Status();
