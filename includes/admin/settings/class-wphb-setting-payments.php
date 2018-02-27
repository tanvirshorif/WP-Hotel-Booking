<?php
/**
 * WP Hotel Booking admin payment gateways setting class.
 *
 * @class       WPHB_Admin_Setting_Payments
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Setting_Payments' ) ) {

	/**
	 * Class WPHB_Admin_Setting_Payments.
	 *
	 *
	 */
	class WPHB_Admin_Setting_Payments extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'payments';

		/**
		 * WPHB_Admin_Setting_Payments constructor.
		 *
		 * @since 2.0
		 *
		 */
		public function __construct() {
			$this->title = __( 'Checkout', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Setting sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_settings() {

			$prefix = 'tp_hotel_booking_';

			$sections = $this->get_sections();
			if ( isset( $_REQUEST['section'] ) && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$section = sanitize_text_field( $_REQUEST['section'] );
			} else {
				$section = reset( $sections );
			}

			return apply_filters( 'wphb_admin_setting_fields_payments', array(
				array(
					'type'  => 'section_start',
					'id'    => 'payment_general_setting',
					'title' => __( 'General Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Payment General options for system.', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'guest_checkout',
					'title'   => __( 'Guest Checkout', 'wp-hotel-booking' ),
					'desc'    => __( 'Allows customers to checkout without creating an account.', 'wp-hotel-booking' ),
					'default' => 1
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'customer_cancel_booking',
					'title'   => __( 'Cancel Booking', 'wp-hotel-booking' ),
					'desc'    => __( 'Allow customer cancels booking from Hotel Account page', 'wp-hotel-booking' ),
					'default' => 0,
				),
				array(
					'type'    => 'number',
					'id'      => $prefix . 'cancel_payment',
					'title'   => __( 'Cancel Payment', 'wp-hotel-booking' ),
					'desc'    => __( 'Cancel Payment after hour(s)', 'wp-hotel-booking' ),
					'default' => 12,
					'min'     => 1,
				),
				array(
					'type' => 'section_end',
					'id'   => 'payment_general_setting'
				)
			), $section );
		}

		/**
		 * Get setting sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_sections() {
			$sections            = array();
			$sections['general'] = __( 'General', 'wp-hotel-booking' );

			return apply_filters( 'wphb_admin_setting_payments_sections', $sections );
		}

	}

}

return new WPHB_Admin_Setting_Payments();