<?php

/**
 * WP Hotel Booking room settings meta box class.
 *
 * @class       WPHB_Admin_Metabox_Room_Settings
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Metabox_Room_Settings' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Room_Settings.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Room_Settings extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'room_settings';

		/**
		 * @var string
		 */
		protected $screen = 'hb_room';

		/**
		 * @var string
		 */
		protected $prefix = '_hb_';

		/**
		 * WPHB_Admin_Metabox_Booking_Actions constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Room Settings', 'wp-hotel-booking' );
			parent::__construct();
		}
	}
}