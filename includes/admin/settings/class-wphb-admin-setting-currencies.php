<?php

/**
 * WP Hotel Booking currencies setting class.
 *
 * @class       WPHB_Admin_Setting_Currencies
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Setting_Currencies' ) ) {

	/**
	 * Class WPHB_Admin_Setting_Currencies.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Setting_Currencies extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'currencies';

		/**
		 * WPHB_Admin_Setting_Currencies constructor.
		 *
		 * @since 2.0
		 *
		 */
		public function __construct() {
			$this->title = __( 'Currency', 'wp-hotel-booking' );
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
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'currency_settings',
					'title' => __( 'Currency Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Currency settings extension.', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tp_hotel_booking_currencies_enable',
					'title'   => __( 'Enable', 'wp-hotel-booking' ),
					'default' => 1
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tp_hotel_booking_currencies_multiple_allowed',
					'title'   => __( 'Is multiple allowed', 'wp-hotel-booking' ),
					'default' => 1
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_currencies_aggregator',
					'title'   => __( 'Currency aggregator', 'wp-hotel-booking' ),
					'options' => array(
						'yahoo'  => 'http://finance.yahoo.com',
						'google' => 'http://google.com/finance'
					),
					'default' => 'yahoo'
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_currencies_storage',
					'title'   => __( 'Currency storage', 'wp-hotel-booking' ),
					'options' => array(
						'session'   => __( 'Session', 'wp-hotel-booking' ),
						'transient' => __( 'Transient', 'wp-hotel-booking' )
					),
					'default' => 'session'
				),
				array(
					'type' => 'section_end',
					'id'   => 'currency_settings'
				)
			) );
		}

	}

}

return new WPHB_Admin_Setting_Currencies();
