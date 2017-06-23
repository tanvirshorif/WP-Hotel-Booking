<?php

/**
 * WP Hotel Booking extra class.
 *
 * @class       WPHB_Extra
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Extra' ) ) {

	/**
	 * Class WPHB_Extra.
	 *
	 * @since 2.0
	 */
	class WPHB_Extra {

		/**
		 * @var null
		 */
		static $_instances = null;

		/**
		 * WPHB_Extra constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_filter( 'hotel_booking_get_product_class', array( $this, 'product_class' ), 10, 3 );
		}

		/**
		 * Extra product class.
		 *
		 * @param null $product
		 * @param null $product_id
		 * @param array $params
		 *
		 * @return WPHB_Extra_Package|null
		 */
		public function product_class( $product = null, $product_id = null, $params = array() ) {
			if ( ! $product_id || get_post_type( $product_id ) !== 'hb_extra_room' ) {
				return $product;
			}
			$parent_quantity = 1;
			if ( isset( $params['order_item_id'] ) ) {
				$parent_quantity = hb_get_order_item_meta( hb_get_parent_order_item( $params['order_item_id'] ), 'quantity', true );
			} else if ( ! is_admin() && isset( $params['parent_id'] ) && WP_Hotel_Booking::instance()->cart ) {
				$cart   = WPHB_Cart::instance();
				$parent = $cart->get_cart_item( $params['parent_id'] );
				if ( $parent ) {
					$parent_quantity = $parent->quantity;
				}
			}

			return new WPHB_Extra_Package( $product_id, array(
				'check_in_date'  => isset( $params['check_in_date'] ) ? $params['check_in_date'] : '',
				'check_out_date' => isset( $params['check_out_date'] ) ? $params['check_out_date'] : '',
				'room_quantity'  => $parent_quantity,
				'quantity'       => isset( $params['quantity'] ) ? $params['quantity'] : 1
			) );
		}

		/**
		 * Get class instance.
		 *
		 * @return null|WPHB_Extra
		 */
		public static function instance() {
			if ( empty( self::$_instances ) ) {
				self::$_instances = new self();
			}

			return self::$_instances;
		}
	}

}
