<?php

/**
 * WP Hotel Booking Room Extra product class.
 *
 * @class       WPHB_Extra_Product
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Extra_Product' ) ) {

	/**
	 * Class WPHB_Extra_Product.
	 *
	 * @since 2.0
	 */
	class WPHB_Extra_Product extends WPHB_Abstract_Product {

		/**
		 * @var array
		 */
		protected static $_instance = array();

		/**
		 * @var string
		 */
		protected $_meta_key = '_hb_room_extra';

		/**
		 * WPHB_Extra_Product constructor.
		 *
		 * @since 2.0
		 *
		 * @param null $post
		 * @param null $options
		 */
		public function __construct( $post, $options = null ) {
			parent::__construct( $post, $options );
		}

		/**
		 * Get extra package data.
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		public function get_extra() {

			$extras = get_post_meta( $this->ID, $this->_meta_key, true );

			$results = array();
			if ( ! empty( $extras ) ) {
				foreach ( $extras as $k => $post_id ) {
					if ( ! get_post( $post_id ) ) {
						continue;
					}
					$package              = WPHB_Extra_Package::instance( $post_id );
					$ext                  = new stdClass();
					$ext->ID              = (int) $post_id;
					$ext->title           = $package->title;
					$ext->description     = $package->description;
					$ext->amount_singular = (float) $package->amount_singular();
					$ext->respondent      = $package->respondent;
					$ext->respondent_name = $package->respondent_name;
					$ext->price           = hb_format_price( $ext->amount_singular );
					$ext->selected        = get_post_meta( $post_id, 'tp_hb_extra_room_selected', true );
					$ext->qty             = 1;
					$ext->required        = $package->required;
					$results[ $post_id ]  = $ext;
				}

			}

			return $results;
		}

		/**
		 * Extra Product instance.
		 *
		 * @since 2.0
		 *
		 * @param $room
		 * @param null $options
		 *
		 * @return mixed|WPHB_Extra_Product
		 */
		public static function instance( $room, $options = null ) {

			$post = $room;
			if ( $room instanceof WP_Post ) {
				$id = $room->ID;
			} elseif ( is_object( $room ) && isset( $room->ID ) ) {
				$id = $room->ID;
			} else {
				$id = $room;
			}

			if ( empty( self::$_instance[ $id ] ) ) {
				return self::$_instance[ $id ] = new self( $post, $options );
			} else {
				$room = self::$_instance[ $id ];

				if ( isset( $options['check_in_date'], $options['check_out_date'] )
				     && ( ( $options['check_in_date'] !== $room->check_in_date ) || ( $options['check_out_date'] !== $room->check_out_date ) )
				     || $room->quantity === false || $room->quantity != $options['quantity']
				) {
					return new self( $post, $options );
				}
			}

			return self::$_instance[ $id ];

		}
	}

}