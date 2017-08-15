<?php

/**
 * WP Hotel Booking room pricing meta box class.
 *
 * @class       WPHB_Admin_Metabox_Room_Price
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Metabox_Room_Price' ) ) {

	/**
	 * Class WPHB_Admin_Metabox_Room_Price.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Metabox_Room_Price extends WPHB_Abstract_Meta_Box {

		/**
		 * @var string
		 */
		protected $id = 'hb-room-price';

		/**
		 * @var string
		 */
		public $context = 'normal';

		/**
		 * @var string
		 */
		protected $screen = 'hb_room';

		/**
		 * @var null
		 */
		protected $view = 'room-pricing';

		/**
		 * WPHB_Admin_Metabox_Room_Price constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->title = __( 'Regular Price', 'wp-hotel-booking' );
			add_action( 'save_post', array( __CLASS__, 'update' ) );

			parent::__construct();
		}

		/**
		 * Update room price.
		 *
		 * @since 2.0
		 *
		 * @param $post_id
		 */
		public static function update( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['hotel-booking-room-pricing-nonce'] ) || ! wp_verify_nonce( $_POST['hotel-booking-room-pricing-nonce'], 'hotel_booking_room_pricing_nonce' ) ) {
				return;
			}

			if ( ! isset( $_POST['_hbpricing'] ) ) {
				return;
			}

			$plan_ids = isset( $_POST['_hbpricing']['plan_id'] ) ? $_POST['_hbpricing']['plan_id'] : array();
			$prices   = isset( $_POST['_hbpricing']['prices'] ) ? $_POST['_hbpricing']['prices'] : array();
			foreach ( $plan_ids as $plan_id ) {
				if ( array_key_exists( $plan_id, $prices ) ) {
					hb_room_set_pricing_plan( array(
						'start_time' => isset( $_POST['start_time'], $_POST['start_time'][ $plan_id ] ) ? $_POST['start_time'][ $plan_id ] : null,
						'end_time'   => isset( $_POST['end_time'], $_POST['end_time'][ $plan_id ] ) ? $_POST['end_time'][ $plan_id ] : null,
						'pricing'    => isset( $prices[ $plan_id ] ) ? $prices[ $plan_id ] : null,
						'room_id'    => $post_id,
						'plan_id'    => $plan_id
					) );
				} else {
					hb_room_remove_pricing( $plan_id );
				}
			}
		}

	}

}