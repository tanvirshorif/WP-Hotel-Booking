<?php

/**
 * WP Hotel Booking booking details meta box class.
 *
 * @class       WPHB_Admin_Metabox_Booking_Details
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Metabox_Booking_Details' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Booking_Details.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Booking_Details extends WPHB_Abstract_Meta_Box {

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
		protected $view = 'booking-details';

		/**
		 * WPHB_Admin_Metabox_Booking_Details constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Booking Details', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Update booking details.
		 *
		 * @since 2.0
		 *
		 * @param $post_id
		 */
		public function update( $post_id ) {
			parent::update( $post_id );

			if ( ! isset( $_POST['hotel_booking_metabox_booking_details_nonce'] ) || ! wp_verify_nonce( $_POST['hotel_booking_metabox_booking_details_nonce'], 'hotel-booking-metabox-booking-details' ) ) {
				return;
			}

			foreach ( $_POST as $k => $vl ) {
				if ( strpos( $k, '_hb_' ) !== 0 ) {
					continue;
				}

				update_post_meta( $post_id, $k, sanitize_text_field( $vl ) );
				do_action( 'hb_booking_detail_update_meta_box_' . $k, $vl, $post_id );
				do_action( 'hb_booking_detail_update_meta_box', $k, $vl, $post_id );
			}
		}
	}

}
