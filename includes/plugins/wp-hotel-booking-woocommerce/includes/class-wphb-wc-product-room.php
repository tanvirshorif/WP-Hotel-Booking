<?php

/**
 * WP Hotel Booking Woocommerce Product room class.
 *
 * @class       WPHB_WC_Product_Room
 * @version     2.0
 * @package     WP_Hotel_Booking_Woocommerce/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class_exists( 'WC_Product_Simple' ) || exit;

! class_exists( 'WPHB_WC_Product_Room' ) || exit;


global $woocommerce;

if ( $woocommerce && version_compare( $woocommerce->version, '3.0.0', '>' ) ) {

	/**
	 * Class WPHB_WC_Product_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_WC_Product_Room extends WC_Product_Simple {

		/**
		 * Product total.
		 *
		 * @since 2.0
		 *
		 * @var
		 */
		public $total;

		/**
		 * WPHB_WC_Product_Room constructor.
		 *
		 * @since 2.0
		 *
		 * @param int $product
		 */
		public function __construct( $product = 0 ) {
			// Should not call constructor of parent
			//parent::__construct( $product );
			if ( is_numeric( $product ) && $product > 0 ) {
				$this->set_id( $product );
			} elseif ( $product instanceof self ) {
				$this->set_id( absint( $product->get_id() ) );
			} elseif ( ! empty( $product->ID ) ) {
				$this->set_id( absint( $product->ID ) );
			}
		}

		/**
		 * Get price.
		 *
		 * @since 2.0
		 *
		 * @param string $context
		 *
		 * @return mixed
		 */
		function get_price( $context = 'view' ) {
			$room = WPHB_Room::instance( $this->get_id(), $this->get_data() );

			return $room->amount_singular_exclude_tax;
		}

		/**
		 * Check if a product is purchasable.
		 *
		 * @since 2.0
		 *
		 * @param string $context
		 *
		 * @return bool
		 */
		function is_purchasable( $context = 'view' ) {
			return true;
		}


		/**
		 * Get stock status.
		 *
		 * @since 2.0
		 *
		 * @param string $context
		 *
		 * @return string
		 */
		public function get_stock_status( $context = 'view' ) {
			return $this->get_stock_quantity( $context ) > 0 ? 'instock' : '';
		}

		/**
		 * Check product exists.
		 *
		 * @since 2.0
		 *
		 * @param string $context
		 *
		 * @return bool
		 */
		public function exists( $context = 'view' ) {
			return $this->get_id() && ( get_post_type( $this->get_id() ) == 'hb_room' ) && ( ! in_array( get_post_status( $this->get_id() ), array(
					'draft',
					'auto-draft'
				) ) );
		}

		/**
		 * Is virtual product.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_virtual() {
			return true;
		}

		/**
		 * Get product name.
		 *
		 * @since 2.0
		 *
		 * @param string $context
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return get_the_title( $this->get_id() );
		}

		/**
		 * Check is in stock.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_in_stock() {
			return true;
		}

		/**
		 * Set check in date.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 */
		public function set_check_in_date( $value ) {
			$this->data['check_in_date'] = $value;
		}

		/**
		 * Set check out date.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 */
		public function set_check_out_date( $value ) {
			$this->data['check_out_date'] = $value;
		}

		/**
		 * Set parent id.
		 *
		 * @since 2.0
		 *
		 * @param int $value
		 */
		public function set_parent_id( $value ) {
			$this->data['parent_id'] = $value;
		}

		/**
		 * Set product id.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 */
		public function set_product_id( $value ) {
			$this->data['product_id'] = $value;
		}

		/**
		 * Set Woocommerce cart id.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 */
		public function set_woo_cart_id( $value ) {
			$this->data['woo_cart_id'] = $value;
		}
	}
} else {

	/**
	 * Class WPHB_WC_Product_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_WC_Product_Room extends WC_Product_Simple {

		/**
		 * Product data.
		 *
		 * @var null
		 */
		public $data = null;

		/**
		 * Product total.
		 *
		 * @var
		 */
		public $total;

		/**
		 * WPHB_WC_Product_Room constructor.
		 *
		 * @since 2.0
		 *
		 * @param int|mixed $the_product
		 */
		function __construct( $the_product ) {
			parent::__construct( $the_product );
		}

		/**
		 * Get product price.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		function get_price() {
			$room = WPHB_Room::instance( $this->post, $this->data );

			return $room->amount_singular_exclude_tax;
		}

		/**
		 * Check if a product is purchasable.
		 *
		 * @since 2.0
		 */
		function is_purchasable() {
			return true;
		}

	}
}
