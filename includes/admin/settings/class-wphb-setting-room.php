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

			$prefix = 'tp_hotel_booking_';

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'catalog_room_setting',
					'title' => __( 'Catalog Options', 'wp-hotel-booking' ),
					'desc'  => __( 'Catalog settings display column number and image size used in room list ( archive page, related room ).', 'wp-hotel-booking' )
				),
				array(
					'type'    => 'checkbox',
					'id'      => $prefix . 'price_including_tax',
					'title'   => __( 'Price including tax', 'wp-hotel-booking' ),
					'desc'    => __( 'Include tax room price in archive room page', 'wp-hotel-booking' ),
					'default' => 1,
				),
				array(
					'id'      => $prefix . 'catalog_display_rating',
					'title'   => __( 'Display rating', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Show room rating in archive page', 'wp-hotel-booking' ),
					'default' => 1
				),
				array(
					'id'      => $prefix . 'catalog_number_column',
					'type'    => 'number',
					'default' => 4,
					'title'   => __( 'Number column', 'wp-hotel-booking' ),
					'desc'    => __( 'Number of column display catalog page', 'wp-hotel-booking' )
				),
				array(
					'id'      => $prefix . 'posts_per_page',
					'type'    => 'number',
					'default' => 8,
					'title'   => __( 'Posts per page', 'wp-hotel-booking' ),
					'desc'    => __( 'Number of post display in page', 'wp-hotel-booking' )
				),
				array(
					'id'      => $prefix . 'catalog_image',
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
					'type'    => 'select',
					'id'      => $prefix . 'price_display',
					'title'   => __( 'Price display', 'wp-hotel-booking' ),
					'options' => array(
						'min'        => __( 'Min', 'wp-hotel-booking' ),
						'max'        => __( 'Max', 'wp-hotel-booking' ),
						'min_to_max' => __( 'Min to Max', 'wp-hotel-booking' )
					),
					'default' => 'min',
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
					'id'      => $prefix . 'display_pricing_plans',
					'title'   => __( 'Pricing plans', 'wp-hotel-booking' ),
					'desc'    => __( 'Show pricing plans in single room page', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1
				),
				array(
					'id'      => $prefix . 'enable_review_rating',
					'title'   => __( 'Rating reviews', 'wp-hotel-booking' ),
					'desc'    => __( 'Enable ratings on reviews', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1
				),
				array(
					'id'      => $prefix . 'review_rating_required',
					'title'   => __( 'Require rating', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Ratings are required to leave a review', 'wp-hotel-booking' ),
					'default' => 1
				),
				array(
					'id'      => $prefix . 'enable_gallery_lightbox',
					'title'   => __( 'Gallery lightbox', 'wp-hotel-booking' ),
					'type'    => 'checkbox',
					'default' => 1,
					'desc'    => __( 'Enable room gallery lightbox', 'wp-hotel-booking' )
				),
				array(
					'id'      => $prefix . 'room_image_gallery',
					'type'    => 'image_size',
					'default' => array(
						'width'  => 270,
						'height' => 270
					),
					'options' => array(
						'width'  => 270,
						'height' => 270
					),
					'title'   => __( 'Images gallery', 'wp-hotel-booking' )
				),
				array(
					'id'      => $prefix . 'room_thumbnail',
					'type'    => 'image_size',
					'default' => array(
						'width'  => 150,
						'height' => 150
					),
					'options' => array(
						'width'  => 150,
						'height' => 150
					),
					'title'   => __( 'Images room thumbnail', 'wp-hotel-booking' )
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