<?php
/**
 * WP Hotel Booking admin report author class.
 *
 * @class       WPHB_Admin_Tool_Report
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_Report' ) ) {

	/**
	 * Class WPHB_Admin_Tool_Report.
	 */
	class WPHB_Admin_Tool_Report extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'report';

		/**
		 * WPHB_Admin_Tool_Report constructor.
		 */
		public function __construct() {
			$this->title = __( 'report', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			echo 'develop access';
		}

	}

}

return new WPHB_Admin_Tool_Report();
