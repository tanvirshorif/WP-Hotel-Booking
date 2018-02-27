<?php

/**
 * WP Hotel Booking admin booking cancelled email.
 *
 * @class       WPHB_Admin_Email_Booking_Cancelled
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Email_Booking_Cancelled' ) ) {
	/**
	 * Class WPHB_Admin_Email_Booking_Cancelled
	 */
	class WPHB_Admin_Email_Booking_Cancelled extends WPHB_Abstract_Email {
		/**
		 * WPHB_Admin_Email_Booking_Cancelled constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->_slug        = 'booking-cancelled';
			$this->_title       = __( 'Booking Cancelled', 'wp-hotel-booking' );
			$this->_description = __( 'Booking cancelled emails are sent to chosen recipient(s) when booking has been marked cancelled.', 'wp-hotel-booking' );
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
					'id'    => 'cancel_booking',
					'title' => __( 'Booking Cancelled ', 'wp-hotel-booking' ),
					'desc'  => __( 'Booking cancelled emails are sent to chosen recipient(s) when booking has been marked cancelled.', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tp_hotel_booking_email_cancel_booking_enable',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'default' => 1,
				),
				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_cancel_booking_recipients',
					'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' )
				),
				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_cancel_booking_subject',
					'title'       => __( 'Subject', 'wp-hotel-booking' ),
					'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
					'default'     => '[{site_title}] Cancelled Reservation ({booking_number}) - {booking_date}',
					'placeholder' => '[{site_title}] Cancelled Reservation  ({booking_number}) - {booking_date}'
				),
				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_cancel_booking_heading',
					'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
					'desc'        => __( 'The main heading displays in the top of email. Default heading: Cancelled booking', 'wp-hotel-booking' ),
					'default'     => 'Cancelled booking',
					'placeholder' => 'Cancelled booking'
				),
				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_cancel_booking_heading_desc',
					'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
					'default'     => __( 'Booking has been marked cancelled', 'wp-hotel-booking' ),
					'placeholder' => __( 'Booking has been marked cancelled', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_email_cancel_booking_format',
					'title'   => __( 'Email Format', 'wp-hotel-booking' ),
					'default' => 'html',
					'options' => array(
						'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
						'html'  => __( 'HTML', 'wp-hotel-booking' )
					)
				),
				array(
					'type' => 'section_end',
					'id'   => 'cancel_booking'
				)
			);
		}
	}
}

new WPHB_Admin_Email_Booking_Cancelled();