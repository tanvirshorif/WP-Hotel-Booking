<?php

/**
 * WP Hotel Booking booking details meta box class.
 *
 * @class       WPHB_Metabox_Booking_Details
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Metabox_Booking_Details' ) ) {

	/**
	 * Class WPHB_Metabox_Booking_Details.
	 *
	 * @since 2.0
	 */
	class WPHB_Metabox_Booking_Details extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'hb-booking-details';

		/**
		 * @var string
		 */
		protected $screen = 'hb_booking';

		/**
		 * @var null
		 */
		protected $view = 'booking';

		/**
		 * WPHB_Metabox_Booking_Details constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Booking Details', 'wp-hotel-booking' );
			parent::__construct();
		}
	}

}
