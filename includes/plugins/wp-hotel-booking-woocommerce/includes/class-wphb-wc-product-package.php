<?php

/**
 * WP Hotel Booking Woocommerce Product extra package class.
 *
 * @class       WPHB_WC_Product_Package
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
class_exists( 'WPHB_Extra_Package' ) || exit;
! class_exists( 'WPHB_WC_Product_Package' ) || exit;


global $woocommerce;

if ( $woocommerce && version_compare( $woocommerce->version, '3.0.0', '>' ) ) {

	/**
	 * Class WPHB_WC_Product_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_WC_Product_Package extends WC_Product_Simple {

		/**
		 * Product total.
		 *
		 * @var
		 */
		public $total;

		/**
		 * Package.
		 *
		 * @var null
		 */
		public $package = null;

		/**
		 * WPHB_WC_Product_Package constructor.
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
		 * Get extra room price.
		 *
		 * @param string $context
		 *
		 * @return mixed
		 */
		function get_price( $context = 'view' ) {

			global $woocommerce;
			$cart = $woocommerce->cart->get_cart();

			$qty = $night = 1;

			$this->package = WPHB_Extra_Package::instance( $this->get_id(), array(
				'room_quantity' => $qty,
				'quantity'      => 1
			) );

			foreach ( $cart as $key => $item ) {
				if ( $item['product_id'] == $this->get_id() ) {
					if ( get_post_meta( $this->get_id(), 'tp_hb_extra_room_respondent', true ) == 'number' ) {
						$night = hb_count_nights_two_dates( $item['check_out_date'], $item['check_in_date'] );
					}
				}
			}

			return $this->package->amount_singular_exclude_tax() * $night;
		}

		/**
		 * Check product is sold individually.
		 *
		 * @return bool
		 */
		function is_sold_individually() {
			if ( ! class_exists( 'WPHB_Extra_Package' ) ) {
				return parent::is_sold_individually();
			}

			$package = WPHB_Extra_Package::instance( $this->get_id() );

			if ( ! $package->respondent ) {
				return parent::is_sold_individually();
			}
			if ( $package->respondent === 'trip' ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if a product is purchasable.
		 *
		 * @param string $context
		 *
		 * @return bool
		 */
		function is_purchasable( $context = 'view' ) {
			return true;
		}

		/**
		 * Check product is in stock.
		 *
		 * @return bool
		 */
		function is_in_stock() {
			return true;
		}

		/**
		 * Check product exists.
		 *
		 * @param string $context
		 *
		 * @return bool
		 */
		public function exists( $context = 'view' ) {
			return $this->get_id() && ( get_post_type( $this->get_id() ) == 'hb_extra_room' ) && ( ! in_array( get_post_status( $this->get_id() ), array(
					'draft',
					'auto-draft'
				) ) );
		}

		/**
		 * Check product is virtual.
		 *
		 * @return bool
		 */
		public function is_virtual() {
			return true;
		}

		/**
		 * Get product name.
		 *
		 * @param string $context
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return get_the_title( $this->get_id() );
		}

	}
} else {
	/**
	 * Class HB_WC_Product_Package.
	 *
	 * @since 2.0
	 */
	class HB_WC_Product_Package extends WC_Product_Simple {

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
		 * Package.
		 *
		 * @var null
		 */
		public $package = null;

		/**
		 * HB_WC_Product_Package constructor.
		 *
		 * @since 2.0
		 *
		 * @param int|mixed $the_product
		 */
		public function __construct( $the_product ) {
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
			$qty = 1;
			if ( ! isset( $this->data['parent_id'] ) ) {
				$parent = WPHB_Cart::instance()->get_cart_item( $this->data['parent_id'] );
				$qty    = $parent->quantity;
			} else if ( isset( $this->data['woo_cart_id'] ) ) {
				$parent = WC()->cart->get_cart_item( $this->data['woo_cart_id'] );
				$qty    = $parent['quantity'];
			}

			$this->package = WPHB_Extra_Package::instance( $this->post, array(
				'check_in_date'  => $this->data['check_in_date'],
				'check_out_date' => $this->data['check_out_date'],
				'room_quantity'  => $qty,
				'quantity'       => 1
			) );

			return $this->package->amount_singular_exclude_tax();
		}

		/**
		 * Check product is sold individually.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		function is_sold_individually() {
			if ( ! class_exists( 'WPHB_Extra_Package' ) ) {
				return parent::is_sold_individually();
			}

			$package = WPHB_Extra_Package::instance( $this->post );

			if ( ! $package->respondent ) {
				return parent::is_sold_individually();
			}

			if ( $package->respondent === 'trip' ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if a product is purchasable.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		function is_purchasable() {
			return true;
		}

	}
}




