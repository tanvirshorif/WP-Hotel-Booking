<?php

/**
 * WP Hotel Booking admin email setting class.
 *
 * @class       WPHB_Admin_Setting_Emails
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Setting_Emails' ) ) {

	class WPHB_Admin_Setting_Emails extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'emails';

		/**
		 * WPHB_Admin_Setting_Emails constructor.
		 *
		 * @since 2.0
		 *
		 */
		function __construct() {
			$this->title = __( 'Emails', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Setting options.
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

			return apply_filters( 'wphb_admin_setting_fields_emails', array(
				array(
					'type'  => 'section_start',
					'id'    => 'email_options',
					'title' => __( 'Email Sender', 'wp-hotel-booking' ),
					'desc'  => __( 'The name and email address of the sender displays in email.', 'wp-hotel-booking' )
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_general_from_name',
					'title'       => __( 'From name', 'wp-hotel-booking' ),
					'default'     => get_option( 'blogname' ),
					'placeholder' => get_option( 'blogname' )
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_general_from_email',
					'title'       => __( 'From Email', 'wp-hotel-booking' ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' )
				),
				array(
					'type'        => 'text',
					'id'          => $prefix . 'email_general_subject',
					'title'       => __( 'Email subject', 'wp-hotel-booking' ),
					'default'     => __( 'Reservation', 'wp-hotel-booking' ),
					'placeholder' => __( 'Reservation', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'select',
					'id'      => $prefix . 'email_general_format',
					'title'   => __( 'Email Format', 'wp-hotel-booking' ),
					'default' => 'html',
					'options' => array(
						'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
						'html'  => __( 'HTML', 'wp-hotel-booking' )
					)
				),

				array(
					'type' => 'section_end',
					'id'   => 'email_options'
				)
			), $section );
		}

		/**
		 * Setting sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_sections() {
			$sections            = array();
			$sections['general'] = __( 'General', 'wp-hotel-booking' );

			return apply_filters( 'wphb_admin_setting_email_sections', $sections );
		}
	}

}

return new WPHB_Admin_Setting_Emails();