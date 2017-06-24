<?php

/**
 * WP Hotel Booking booking items meta box class.
 *
 * @class       WPHB_Admin_Metabox_Booking_Items
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Metabox_Booking_Items' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Booking_Items.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Booking_Items extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'hb-booking-items';

		/**
		 * @var string
		 */
		protected $screen = 'hb_booking';

		/**
		 * @var null
		 */
		protected $view = array( 'booking-items', 'booking-items-template-js' );

		/**
		 * WPHB_Admin_Metabox_Booking_Details constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Booking Items', 'wp-hotel-booking' );
			parent::__construct();
		}

	}

}
