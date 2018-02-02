<?php

/**
 * WP Hotel Booking admin general setting class.
 *
 * @class       WPHB_Admin_Setting_General
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Setting_General' ) ) {

	/**
	 * Class WPHB_Admin_Setting_General.
	 */
	class WPHB_Admin_Setting_General extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'general';

		/**
		 * WPHB_Admin_Setting_General constructor.
		 *
		 * @since 2.0
		 *
		 */
		function __construct() {
			$this->title = __( 'General', 'wp-hotel-booking' );
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

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'general_settings',
					'title' => __( 'General Options', 'wp-hotel-booking' ),
					'desc'  => __( 'General options for system.', 'wp-hotel-booking' ),
					'class' => 'general-section'
				),
				array(
					'type'    => 'number',
					'id'      => $prefix . 'minimum_booking_day',
					'title'   => __( 'Minimum booking nights', 'wp-hotel-booking' ),
					'default' => 1,
					'min'     => 0,
					'step'    => 'any'
				),
				array(
					'type'    => 'number',
					'id'      => $prefix . 'tax',
					'title'   => __( 'Tax', 'wp-hotel-booking' ),
					'default' => 10,
					'min'     => 0,
					'step'    => 'any'
				),
				array(
					'type'    => 'number',
					'id'      => $prefix . 'advance_payment',
					'title'   => __( 'Advance Payment', 'wp-hotel-booking' ),
					'desc'    => __( 'Payment advance. Eg: 50%', 'wp-hotel-booking' ),
					'default' => 50,
					'min'     => 0,
					'max'     => 100
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'multiple_location',
					'title'   => __( 'Multiple Location', 'wp-hotel-booking' ),
					'default' => 0,
					'desc'    => __( 'Enable multiple location', 'wp-hotel-booking' )
				),
				array(
					'type' => 'section_end',
					'id'   => 'general_settings'
				),
				array(
					'type'  => 'section_start',
					'id'    => 'currency_settings',
					'title' => __( 'Currency Options', 'wp-hotel-booking' ),
					'desc'  => __( 'The options for display rooms price on the frontend.', 'wp-hotel-booking' ),
					'class' => 'general-section'
				),
				array(
					'type'    => 'select',
					'id'      => $prefix . 'currency',
					'title'   => __( 'Currency', 'wp-hotel-booking' ),
					'options' => hb_payment_currencies(),
					'default' => 'USD'
				),
				array(
					'type'    => 'select',
					'id'      => $prefix . 'price_currency_position',
					'title'   => __( 'Currency Position', 'wp-hotel-booking' ),
					'options' => array(
						'left'             => __( 'Left ( $69.99 )', 'wp-hotel-booking' ),
						'right'            => __( 'Right ( 69.99$ )', 'wp-hotel-booking' ),
						'left_with_space'  => __( 'Left with space ( $ 69.99 )', 'wp-hotel-booking' ),
						'right_with_space' => __( 'Right with space ( 69.99 $ )', 'wp-hotel-booking' )
					),
					'default' => 'left'
				),
				array(
					'type'    => 'text',
					'id'      => $prefix . 'price_thousands_separator',
					'title'   => __( 'Thousands Separator', 'wp-hotel-booking' ),
					'default' => ','
				),
				array(
					'type'    => 'text',
					'id'      => $prefix . 'price_decimals_separator',
					'title'   => __( 'Decimals Separator', 'wp-hotel-booking' ),
					'default' => '.'
				),
				array(
					'type'    => 'number',
					'id'      => $prefix . 'price_number_of_decimal',
					'title'   => __( 'Number of decimal', 'wp-hotel-booking' ),
					'default' => 1,
					'min'     => 0,
					'max'     => 3,
				),
				array(
					'type' => 'section_end',
					'id'   => 'currency_settings'
				)
			) );
		}

	}

}

return new WPHB_Admin_Setting_General();
