<?php
/**
 * WP Hotel Booking admin room setting class.
 *
 * @class       WPHB_Admin_Setting_Room
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Admin_Setting_Room' ) ) {

	/**
	 * Class WPHB_Admin_Setting_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Setting_Room extends WPHB_Abstract_Setting {

		/**
		 * @var string
		 */
		protected $id = 'room';

		/**
		 * WPHB_Admin_Setting_Room constructor.
		 *
		 * @since 2.0
		 *
		 */
		function __construct() {
			$this->title = __( 'Room', 'wp-hotel-booking' );
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
					'id'    => 'catalog_room_setting',
					'title' => __( 'Catalog Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Catalog settings display column number and image size used in room list ( archive page, related room ).', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_catalog_number_column',
					'type'    => 'number',
					'default' => 4,
					'title'   => __( 'Number of column display catalog page', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_posts_per_page',
					'type'    => 'number',
					'default' => 8,
					'title'   => __( 'Number of post display in page', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_catalog_image',
					'type'    => 'image_size',
					'default' => array(
						'width'  => 270,
						'height' => 270
					),
					'options' => array(
						'width'  => 270,
						'height' => 270
					),
					'title'   => __( 'Catalog images size', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_catalog_display_rating',
					'title'   => __( 'Display rating', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1
				),
				array(
					'type' => 'section_end',
					'id'   => 'catalog_room_setting'
				),
				array(
					'type'  => 'section_start',
					'id'    => 'room_setting',
					'title' => __( 'Room Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Room settings display column number and image size used in gallery single page', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_room_image_gallery',
					'type'    => 'image_size',
					'default' => array(
						'width'  => 270,
						'height' => 270
					),
					'options' => array(
						'width'  => 270,
						'height' => 270
					),
					'title'   => __( 'Room images size gallery', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_room_thumbnail',
					'type'    => 'image_size',
					'default' => array(
						'width'  => 150,
						'height' => 150
					),
					'options' => array(
						'width'  => 150,
						'height' => 150
					),
					'title'   => __( 'Room images thumbnail', 'wp-hotel-booking' )
				),
				array(
					'id'      => 'tp_hotel_booking_display_pricing_plans',
					'title'   => __( 'Display pricing plans', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1
				),
				array(
					'id'      => 'tp_hotel_booking_enable_review_rating',
					'title'   => __( 'Enable ratings on reviews', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1,
					'atts'    => array(
						'onchange' => "jQuery('.enable_ratings_on_reviews').toggleClass( 'hide-if-js', ! this.checked );"
					)
				),
				array(
					'id'      => 'tp_hotel_booking_review_rating_required',
					'title'   => __( 'Ratings are required to leave a review', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1,
					'trclass' => array( 'enable_ratings_on_reviews' )
				),
				array(
					'id'      => 'tp_hotel_booking_enable_gallery_lightbox',
					'title'   => __( 'Enable gallery lightbox', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1
				),
				array(
					'type' => 'section_end',
					'id'   => 'room_setting'
				),

			) );
		}

	}

}

return new WPHB_Admin_Setting_Room();