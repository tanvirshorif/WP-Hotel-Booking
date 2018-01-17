<?php
/**
 * WP Hotel Booking admin hotel pages setting class.
 *
 * @class       WPHB_Admin_Setting_Hotel_Pages
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Setting_Hotel_Pages' ) ) {

	/**
	 * Class WPHB_Admin_Setting_Hotel_Pages.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Setting_Hotel_Pages extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'pages';

		/**
		 * WPHB_Admin_Setting_Hotel_Pages constructor.
		 *
		 * @since 2.0
		 *
		 */
		public function __construct() {
			$this->title = __( 'Pages', 'wp-hotel-booking' );
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

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'hotel_booking_pages',
					'title' => __( 'System Pages', 'wp-hotel-booking' ),
					'desc'  => __( 'Setup default pages.', 'wp-hotel-booking' ),
					'class' => 'general-section'
				),
				array(
					'type'  => 'select_page',
					'id'    => $prefix . 'search_page_id',
					'title' => __( 'Search Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => $prefix . 'checkout_page_id',
					'title' => __( 'Checkout Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => $prefix . 'cart_page_id',
					'title' => __( 'Cart Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => $prefix . 'account_page_id',
					'title' => __( 'Account Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => $prefix . 'terms_page_id',
					'title' => __( 'Terms And Conditions Page', 'wp-hotel-booking' )
				),
				array(
					'type' => 'section_end',
					'id'   => 'hotel_booking_pages'
				)
			) );
		}

	}

}

return new WPHB_Admin_Setting_Hotel_Pages();