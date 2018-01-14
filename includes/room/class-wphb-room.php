<?php

/**
 * WP Hotel Booking room class.
 *
 * @class       WPHB_Room
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Room' ) ) {
	/**
	 * Class WPHB_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_Room extends WPHB_Abstract_Product {
		/**
		 * @var array
		 */
		protected static $_instance = array();

		/**
		 * @return null or array
		 */
		public $_review_details = null;

		/**
		 * WPHB_Room constructor.
		 *
		 * @param $post
		 * @param null $params
		 */
		public function __construct( $post, $params = null ) {
			parent::__construct( $post, $params );
		}

		/**
		 * WPHB Room instance.
		 *
		 * @since 2.0
		 *
		 * @param $room
		 * @param null $options
		 *
		 * @return mixed|WPHB_Room
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

				if ( isset( $options['check_in_date'], $options['check_out_date'] ) && ( ( $options['check_in_date'] !== $room->get_data( 'check_in_date' ) ) || ( $options['check_out_date'] !== $room->get_data( 'check_out_date' ) ) ) || $room->quantity === false || ( ! isset( $options['quantity'] ) || $room->quantity != $options['quantity'] ) || ( ! isset( $options['extra_packages'] ) || $options['extra_packages'] != $room->extra_packages )
				) {
					return new self( $post, $options );
				}
			}

			return self::$_instance[ $id ];
		}
	}
}
