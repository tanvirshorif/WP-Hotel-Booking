<?php

/**
 * WP Hotel Booking booking editor meta box class.
 *
 * @class       WPHB_Metabox_Booking_Editor
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Metabox_Booking_Editor' ) ) {

	/**
	 * Class WPHB_Metabox_Booking_Editor.
	 *
	 * @since 2.0
	 */
	class WPHB_Metabox_Booking_Editor extends WPHB_Abstract_Meta_Box {

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
		protected $view = 'booking-editor';

		/**
		 * WPHB_Metabox_Booking_Editor constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Booking Details', 'wp-hotel-booking' );
			parent::__construct();
		}
	}

}
