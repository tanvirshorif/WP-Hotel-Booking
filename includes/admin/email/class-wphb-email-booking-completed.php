<?php

/**
 * WP Hotel Booking admin booking completed email.
 *
 * @class       WPHB_Admin_Email_Booking_Completed
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Email_Booking_Completed' ) ) {
	/**
	 * Class WPHB_Admin_Email_Booking_Completed
	 */
	class WPHB_Admin_Email_Booking_Completed extends WPHB_Abstract_Email {
		/**
		 * WPHB_Admin_Email_Booking_Completed constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->_slug        = 'booking-completed';
			$this->_title       = __( 'Booking Completed', 'wp-hotel-booking' );
			$this->_description = __( 'Booking completed emails are sent to chosen recipient(s) when booking has been marked completed.', 'wp-hotel-booking' );
		}

		/**
		 * Admin settings fields.
		 *
		 * @return array
		 */
		public function setting_fields() {
			$prefix = 'tp_hotel_booking_';

			return array(
				array(
					'type'  => 'section_start',
					'id'    => 'booking_completed',
					'title' => __( 'Booking Completed', 'wp-hotel-booking' ),
					'desc'  => __( 'Booking completed emails are sent to admin when a booking is completed.', 'wp-hotel-booking' )
				),

				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'email_booking_completed_enable',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'default' => 1,
				),

				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_booking_completed_recipients',
					'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' )
				),

				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_booking_completed_subject',
					'title'       => __( 'Subject', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}',
					'placeholder' => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}'
				),

				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_booking_completed_heading',
					'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
					'desc'        => __( 'The main heading displays in the top of email. Default heading: Booking completed', 'wp-hotel-booking' ),
					'default'     => 'Booking completed',
					'placeholder' => 'Booking completed'
				),

				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_booking_completed_heading_desc',
					'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
					'default'     => __( 'The customer has completed the transaction', 'wp-hotel-booking' ),
					'placeholder' => __( 'The customer has completed the transaction', 'wp-hotel-booking' )
				),

				array(
					'type' => 'section_end',
					'id'   => 'booking_completed'
				)
			);
		}
	}
}

new WPHB_Admin_Email_Booking_Completed();