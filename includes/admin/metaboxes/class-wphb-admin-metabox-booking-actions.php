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

			if ( ! ( isset( $_POST['hotel_booking_metabox_booking_actions_nonce'] ) && wp_verify_nonce( $_POST['hotel_booking_metabox_booking_actions_nonce'], 'hotel-booking-metabox-booking-actions' ) ) ) {
				return;
			}

			foreach ( $_POST as $key => $value ) {
				if ( strpos( $key, '_hb_' ) !== 0 ) {
					continue;
				}

				update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
				do_action( 'hb_booking_detail_update_meta_box_' . $key, $value, $post_id );
//				do_action( 'hb_booking_detail_update_meta_box', $key, $value, $post_id );
			}
		}
	}

}
