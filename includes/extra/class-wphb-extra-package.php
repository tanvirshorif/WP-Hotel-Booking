<?php

/**
 * WP Hotel Booking extra package class.
 *
 * @class       WPHB_Extra_Package
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Extra_Package' ) ) {

	/**
	 * Class WPHB_Extra_Package.
	 *
	 * @since 2.0
	 */
	class WPHB_Extra_Package {

		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * @var null
		 */
		public $_package = null;

		/**
		 * extra post
		 *
		 * @var null
		 */
		protected $_post = null;

		/**
		 * room check in
		 *
		 * @var null
		 */
		protected $check_in_date = null;

		/**
		 * room check out
		 *
		 * @var null
		 */
		protected $check_out_date = null;

		/**
		 * room quantity
		 *
		 * @var null
		 */
		public $parent_quantity = null;

		/**
		 * package quantity
		 *
		 * @var null
		 */
		public $quantity = null;

		/**
		 * @var null
		 */
		public $price = null;

		/**
		 * @var null
		 */
		public $price_tax = null;

		/**
		 * @var null
		 */
		public $regular_price_tax = null;

		/**
		 * @var null
		 */
		public $regular_price = null;

		/**
		 * @var null
		 */
		public $respondent = null;

		/**
		 * WPHB_Extra_Package constructor.
		 *
		 * @since 2.0
		 *
		 * @param $post
		 * @param array $params
		 */
		public function __construct( $post, $params = array() ) {
			$params = wp_parse_args( $params, array(
				'check_in_date'  => '',
				'check_out_date' => '',
				'room_quantity'  => 1,
				'quantity'       => 1
			) );

			$this->check_in_date = $params['check_in_date'];
			if ( ! $this->check_in_date ) {
				$this->check_in_date = time();
			}

			$this->check_out_date = $params['check_out_date'];
			if ( ! $this->check_out_date ) {
				$this->check_out_date = time();
			}

			$this->parent_quantity = $params['room_quantity'];

			$this->quantity = $params['quantity'];

			if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_extra_room' ) {
				$this->_post = get_post( $post );
			} elseif ( $post instanceof WP_Post || is_object( $post ) ) {
				$this->_post = $post;
			}

			if ( ! $this->_post ) {
				return;
			}
		}

		/**
		 * Extra package get values.
		 *
		 * @since 2.0
		 *
		 * @param $key
		 *
		 * @return float|int|null|string
		 */
		public function __get( $key ) {
			switch ( $key ) {
				case 'ID':
					$return = $this->_post->ID;
					break;
				case 'title':
					$return = $this->_post->post_title;
					break;
				case 'description':
					$return = $this->_post->post_content;
					break;
				case 'regular_price':
					$return = $this->get_regular_price();
					break;
				case 'regular_price_tax':
					$return = $this->get_regular_price( true );
					break;
				case 'quantity':
					$return = $this->quantity;
					break;
				case 'price':
					$return = $this->get_price_package( false );
					break;
				case 'price_tax':
					$return = $this->get_price_package();
					break;
				case 'respondent':
					$return = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_respondent', true );
					break;
				case 'respondent_name':
					$return = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_respondent_name', true );
					break;
				case 'night':
					$return = hb_count_nights_two_dates( $this->check_out_date, $this->check_in_date );
					break;
				case 'amount_singular':
					$return = $this->amount_singular();
					break;
				case 'amount_singular_exclude_tax':
					$return = $this->amount_singular_exclude_tax();
					break;
				case 'amount_singular_include_tax':
					$return = $this->amount_singular_include_tax();
					break;
				default:
					$return = null;
					break;
			}

			return $return;
		}

		/**
		 * Extra get data.
		 *
		 * @since 2.0
		 *
		 * @param null $key
		 *
		 * @return mixed
		 */
		public function get_data( $key = null ) {
			if ( ! $key ) {
				return false;
			}

			if ( isset( $this->{$key} ) ) {
				return $this->{$key};
			}

			return false;
		}


		/**
		 * Get extra package price with booking date.
		 *
		 * @since 2.0
		 *
		 * @param bool $tax
		 *
		 * @return float
		 */
		public function get_price_package( $tax = true ) {
			if ( $tax ) {
				$regular_price = (float) $this->regular_price_tax;
			} else {
				$regular_price = (float) $this->regular_price;
			}

			$price = $regular_price;
			// price of type 'number' = price * quantity * night
			//               'trip' = price ( quantity  = 1 )
			if ( $this->respondent === 'number' ) {
				$price = $price * $this->quantity * $this->night;
			}

			$price = apply_filters( 'hotel_booking_regular_extra_price', $price, $regular_price, $this, $tax );

			return (float) $price;
		}

		/**
		 * Get extra package regular price.
		 *
		 * @since 2.0
		 *
		 * @param bool $tax
		 *
		 * @return float|bool
		 */
		public function get_regular_price( $tax = false ) {
			if ( ! $this->_post ) {
				return false;
			}
			$price = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_price', true );

			if ( $tax ) {
				$tax_price = apply_filters( 'hotel_booking_extra_package_regular_price_incl_tax', hb_get_tax_settings(), $price, $this );
				$price     = (float) ( $price * ( 1 + $tax_price ) );
			}

			return $price;
		}

		/**
		 * Get extra amount.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount( $cart = false ) {
			return hb_price_including_tax() ? $this->get_price_package() : $this->get_price_package( false );
		}

		/**
		 * Get extra amount include tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_include_tax() {
			return $this->price_tax;
		}

		/**
		 * Get extra amount exclude tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_exclude_tax() {
			return $this->price;
		}

		/**
		 * Get extra amount singular exclude tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_singular_exclude_tax() {
			return apply_filters( 'hotel_booking_package_singular_total_exclude_tax', $this->get_regular_price( false ), $this );
		}

		/**
		 * Get extra amount singular include tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_singular_include_tax() {
			return apply_filters( 'hotel_booking_package_singular_total_include_tax', $this->get_regular_price( true ), $this );
		}

		/**
		 * Get extra amount singular.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_singular() {
			$amount = hb_price_including_tax() ? $this->amount_singular_include_tax() : $this->amount_singular_exclude_tax();

			return apply_filters( 'hotel_booking_package_amount_singular', $amount, $this );
		}

		/**
		 * Check tax.
		 *
		 * @since 2.0
		 *
		 * @param string $content
		 *
		 * @return bool
		 */
		public function is_taxable( $content = 'view' ) {
			return false;
		}

		/**
		 * Get tax class.
		 *
		 * @since 2.0
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public function get_tax_class( $content = 'view' ) {
			return '';
		}

		/**
		 * Check extra package in stock.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_in_stock() {
			return true;
		}

		/**
		 * Extra package instance.
		 *
		 * @since 2.0
		 *
		 * @param $id
		 * @param array $params
		 *
		 * @param  integer $id post id
		 * @param  datetime $checkIn checkin date
		 * @param  datetime $checkOut checkout date
		 * @param  integer $room_quantity number of room
		 * @param  integer $package_quantity number of package
		 *
		 * @return WPHB_Extra_Package
		 */
		static function instance( $id, $params = array() ) {
			$params = wp_parse_args( $params, array(
				'check_in_date'  => '',
				'check_out_date' => '',
				'room_quantity'  => 1,
				'quantity'       => 1
			) );

			if ( ! empty( self::$_instance[ $id ] ) ) {
				$package = self::$_instance[ $id ];

				if ( $package->check_in_date === $params['check_in_date'] &&
				     $package->check_out_date === $params['check_out_date'] &&
				     $package->parent_quantity == $params['room_quantity'] &&
				     $package->quantity == $params['quantity']
				) {
					return $package;
				}
			}

			return new self( $id, $params );
		}

	}
}