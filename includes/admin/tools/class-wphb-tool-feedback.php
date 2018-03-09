<?php
/**
 * WP Hotel Booking admin report author class.
 *
 * @class       WPHB_Admin_Tool_Feedback
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_Feedback' ) ) {

	/**
	 * Class WPHB_Admin_Tool_Feedback.
	 */
	class WPHB_Admin_Tool_Feedback extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'feedback';

		/**
		 * WPHB_Admin_Tool_Feedback constructor.
		 */
		public function __construct() {
			$this->title = __( 'Feedback', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
		}

	}

}

return new WPHB_Admin_Tool_Feedback();
