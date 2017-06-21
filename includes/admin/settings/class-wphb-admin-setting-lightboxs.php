<?php
/**
 * WP Hotel Booking admin lightboxs setting class.
 *
 * @class       WPHB_Admin_Setting_Lightboxs
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Setting_Lightboxs' ) ) {

	/**
	 * Class WPHB_Admin_Setting_Lightboxs.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Setting_Lightboxs extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'lightboxs';

		/**
		 * WPHB_Admin_Setting_Lightboxs constructor.
		 *
		 * @since 2.0
		 *
		 */
	public function __construct() {

			$this->title = __( 'Lightboxs', 'wp-hotel-booking' );

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
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'lightbox_settings',
					'title' => __( 'Lightbox options', 'wp-hotel-booking' ),
					'desc'  => __( 'General options for Lightbox system.', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_lightbox',
					'options' => hb_get_support_lightboxs(),
					'title'   => __( 'Lightbox type', 'wp-hotel-booking' ),
					'default' => 'lightbox2'
				),
				array(
					'type' => 'section_end',
					'id'   => 'lightbox_settings'
				)

			) );
		}

	}

}

new WPHB_Admin_Setting_Lightboxs();