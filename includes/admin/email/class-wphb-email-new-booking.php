<?php

/**
 * WP Hotel Booking admin new booking email.
 *
 * @class       WPHB_Admin_Email_New_Booking
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Email_New_Booking' ) ) {
	/**
	 * Class WPHB_Admin_Email_New_Booking
	 */
	class WPHB_Admin_Email_New_Booking extends WPHB_Abstract_Email {
		/**
		 * WPHB_Admin_Email_New_Booking constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->_slug        = 'new-booking';
			$this->_title       = __( 'New Booking', 'wp-hotel-booking' );
			$this->_description = __( 'New booking emails are sent to chosen recipient(s) when a booking is received.', 'wp-hotel-booking' );
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
					'id'    => 'new_booking',
					'title' => __( 'New Booking', 'wp-hotel-booking' ),
					'desc'  => __( 'New booking emails are sent to admin when a booking is received.', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'email_new_booking_enable',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'default' => 1,
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_new_booking_recipients',
					'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' )
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_new_booking_subject',
					'title'       => __( 'Subject', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => '[{site_title}] New booking received ({booking_number}) - {booking_date}',
					'placeholder' => '[{site_title}] New booking received ({booking_number}) - {booking_date}'
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_new_booking_heading',
					'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
					'desc'        => __( 'The main heading displays in the top of email. Default heading: New booking received', 'wp-hotel-booking' ),
					'default'     => 'New Booking Received',
					'placeholder' => 'New Booking Received'
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_new_booking_heading_desc',
					'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
					'default'     => __( 'The customer has placed booking', 'wp-hotel-booking' ),
					'placeholder' => __( 'The customer has placed booking', 'wp-hotel-booking' )
				),
				array(
					'type' => 'section_end',
					'id'   => 'new_booking'
				)
			);
		}
	}
}

new WPHB_Admin_Email_New_Booking();