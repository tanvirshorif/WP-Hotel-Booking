<?php

/**
 * WP Hotel Booking customer booking details meta box class.
 *
 * @class       WPHB_Admin_Metabox_Booking_Customer
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Metabox_Booking_Customer' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Booking_Customer.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Booking_Customer extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'hb-customer-details';

		/**
		 * @var string
		 */
		protected $screen = 'hb_booking';

		/**
		 * @var null
		 */
		protected $view = 'booking-customer';

		/**
		 * WPHB_Admin_Metabox_Booking_Customer constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Customer Information', 'wp-hotel-booking' );
			parent::__construct();
		}
	}

}