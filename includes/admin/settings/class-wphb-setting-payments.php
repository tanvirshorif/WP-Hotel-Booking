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

			$section  = '';
			$sections = $this->get_sections();
			if ( isset( $_REQUEST['section'] ) && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$section = sanitize_text_field( $_REQUEST['section'] );
			}

			$section = $section ? $section : reset( array_keys( $sections ) );

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'payment_general_checkout',
					'title' => __( 'General Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Payment General options for system.', 'wp-hotel-booking' ),
					'class' => 'general-checkout'
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'guest_checkout',
					'title'   => __( 'Guest Checkout', 'wp-hotel-booking' ),
					'desc'    => __( 'Allows customers to checkout without creating an account.', 'wp-hotel-booking' ),
					'default' => 1
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
					'id'   => 'payment_general_checkout'
				),
				array(
					'type'  => 'section_start',
					'id'    => 'checkout_endpoints',
					'title' => __( 'Checkout Endpoints', 'wp-hotel-booking' ),
					'desc'  => wp_kses( __( 'Endpoints are appended to your page URLs to handle specific actions during the checkout process. <strong>They should be unique.</strong>', 'wp-hotel-booking' ), array( 'strong' => array() ) ),
					'class' => 'general-checkout'
				),
				array(
					'type'    => 'text',
					'id'      => $prefix . 'booking_received',
					'title'   => __( 'Booking Received', 'wp-hotel-booking' ),
					'default' => 'thank-you',
					'desc'    => __( 'Please update permalink after change booking received endpoint url.', 'wp-hotel-booking' )
				),
				array(
					'type' => 'section_end',
					'id'   => 'checkout_endpoints'
				),

				array(
					'type'   => 'section_start',
					'id'     => 'offline_payment',
					'title'  => __( 'Offline payment', 'wp-hotel-booking' ),
					'desc'   => __( 'Setting for Offline payment.', 'wp-hotel-booking' ),
					'class'  => 'offline-payment',
					'hidden' => $section == 'offline_payment' ? true : false
				),

				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'offline-payment[enable]',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'desc'    => __( 'Enable payment booking room via offline.', 'wp-hotel-booking' ),
					'default' => 1
				),

				array(
					'type' => 'section_end',
					'id'   => 'offline_payment'
				),

				array(
					'type'   => 'section_start',
					'id'     => 'paypal',
					'title'  => __( 'Paypal', 'wp-hotel-booking' ),
					'desc'   => __( 'Setting for Paypal payment gateway.', 'wp-hotel-booking' ),
					'class'  => 'paypal',
					'hidden' => $section == 'paypal' ? true : false
				),

				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'paypal[enable]',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'desc'    => __( 'Enable payment booking room via Paypal.', 'wp-hotel-booking' ),
					'default' => 0
				),

				array(
					'type'  => 'text',
					'id'    => $prefix . 'paypal[email]',
					'title' => __( 'Paypal email', 'wp-hotel-booking' ),
					'desc'  => __( 'Paypal email.', 'wp-hotel-booking' )
				),

				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'paypal[sandbox]',
					'title'   => __( 'Sandbox Mode', 'wp-hotel-booking' ),
					'desc'    => __( 'Enable Paypal sandbox mode.', 'wp-hotel-booking' ),
					'default' => 0
				),

				array(
					'type'  => 'text',
					'id'    => $prefix . 'paypal[sandbox_email]',
					'title' => __( 'Paypal sandbox email', 'wp-hotel-booking' ),
					'desc'  => __( 'Paypal sandbox email.', 'wp-hotel-booking' )
				),

				array(
					'type' => 'section_end',
					'id'   => 'paypal'
				),

			) );
		}

		/**
		 * Output setting page.
		 *
		 * @since 2.0
		 */
		public function output() {
			$current_section = null;

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			$payments = hb_get_payment_gateways();
			if ( $current_section && $current_section !== 'general' ) {
				foreach ( $payments as $payment ) {
					if ( $payment->slug === $current_section ) {
						$payment->admin_settings();
						break;
					}
				}
			} else {
				parent::output();
			}
		}

		/**
		 * Get setting sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_sections() {
			$sections['general-checkout'] = __( 'General', 'wp-hotel-booking' );

			$sections = array(
				'general-checkout' => __( 'General', 'wp-hotel-booking' ),
				'offline-payment'  => __( 'Offline payment', 'wp-hotel-booking' ),
				'paypal'           => __( 'Paypal', 'wp-hotel-booking' ),
			);

			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

	}

}

return new WPHB_Admin_Setting_Payments();