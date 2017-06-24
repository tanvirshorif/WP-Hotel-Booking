<?php

/**
 * WP Hotel Booking booking actions meta box class.
 *
 * @class       WPHB_Admin_Metabox_Booking_Actions
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Metabox_Booking_Actions' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Booking_Actions.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Booking_Actions extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'hb-booking-actions';

		/**
		 * @var string
		 */
		protected $context = 'side';

		/**
		 * @var string
		 */
		protected $screen = 'hb_booking';

		/**
		 * @var null
		 */
		protected $view = 'booking-actions';

		/**
		 * WPHB_Admin_Metabox_Booking_Actions constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Booking Actions', 'wp-hotel-booking' );
			parent::__construct();

			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10 );
			add_action( 'save_post', array( $this, 'update' ) );
		}
	}

}
