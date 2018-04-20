<?php

/**
 * WP Hotel Booking admin class.
 *
 * @class       WPHB_Admin
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin' ) ) {
	/**
	 * Class WPHB_Admin.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin {
		/**
		 * WPHB_Admin constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			// include files
			$this->includes();

			// update room pricing plan
			add_action( 'admin_init', array( $this, 'update_pricing_plan' ) );
		}

		/**
		 * Include admin component.
		 *
		 * @since 2.0
		 */
		public function includes() {
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-ajax.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-assets.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-settings.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-menu.php' );
			include_once( WPHB_INCLUDES . 'admin/class-wphb-admin-tools.php' );
			include_once( WPHB_INCLUDES . 'admin/wphb-admin-functions.php' );
		}

		/**
		 * Update room pricing plan.
		 *
		 * @since 2.0
		 */
		public function update_pricing_plan() {
			if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['hb-update-pricing-plan-field'] ), 'hb-update-pricing-plan' ) ) {
				return;			}

			if ( empty( $_POST['price'] ) || ! isset( $_POST['room_id'] ) ) {
				return;
			}

			$room_id = absint( $_POST['room_id'] );
			$plans   = hb_room_get_pricing_plans( $room_id );

			$ignore = array();
			foreach ( (array) $_POST['price'] as $t => $v ) {
				$start  = isset( $_POST['date-start-timestamp'][ $t ] ) ? sanitize_text_field( $_POST['date-start-timestamp'][ $t ] ) : '';
				$end    = isset( $_POST['date-end-timestamp'][ $t ] ) ? sanitize_text_field( $_POST['date-end-timestamp'][ $t ] ) : '';
				$prices = (array) $_POST['price'][ $t ];

				$plan_id  = hb_room_set_pricing_plan( array(
					'start_time' => $start,
					'end_time'   => $end,
					'pricing'    => $prices,
					'room_id'    => $room_id,
					'plan_id'    => $t
				) );
				$ignore[] = $plan_id;
			}

			foreach ( $plans as $id => $plan ) {
				if ( ! in_array( $id, $ignore ) ) {
					hb_room_remove_pricing( $id );
				}
			}
		}
	}
}

new WPHB_Admin();
