<?php

/**
 * WP Hotel Booking Coupon class.
 *
 * @class       WPHB_Coupon
 * @version     2.0
 * @package     WP_Hotel_Booking_Coupon/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Coupon' ) ) {

	/**
	 * Class WPHB_Coupon.
	 *
	 * @since 2.0
	 */
	class WPHB_Coupon {

		/**
		 * @var array
		 */
		static $_instance = null;

		/**
		 * @var bool
		 */
		public $post = false;

		/**
		 * @var bool
		 */
		protected $_settings = array();

		/**
		 * WPHB_Coupon constructor.
		 *
		 * @since 2.0
		 *
		 * @param $post
		 */
		public function __construct( $post ) {
			if ( is_numeric( $post ) ) {
				$this->post = get_post( $post );
			} elseif ( $post instanceof WP_Post || ( is_object( $post ) && ! ( $post instanceof WPHB_Coupon ) ) ) {
				$this->post = $post;
			} elseif ( $post instanceof WPHB_Coupon ) {
				$this->post = $post->post;
			}
			$this->_load_settings();

			// update booking sub total
			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
			// update booking transaction
			add_filter( 'hotel_booking_cart_generate_transaction', array( $this, 'add_coupon_transaction' ) );
			// update coupon usage count
			add_action( 'hb_booking_status_changed', array( $this, 'update_coupon_usage' ), 10, 3 );
			// update coupon meta box
			add_filter( 'hb_meta_box_update_meta_value', 'update_coupon_date_meta', 10, 3 );

		}

		/**
		 * Load coupon settings.
		 *
		 * @since 2.0
		 */
		private function _load_settings() {
			if ( ! empty( $this->post->ID ) ) {
				if ( $metas = get_post_meta( $this->post->ID ) ) {
					foreach ( $metas as $k => $v ) {
						$k                     = str_replace( '_hb_', '', $k );
						$this->_settings[ $k ] = $v[0];
					}
				}
			}
		}

		/**
		 * Get coupon data.
		 *
		 * @since 2.0
		 *
		 * @param $prop
		 *
		 * @return bool|float|int
		 */
		public function __get( $prop ) {
			$return = false;
			switch ( $prop ) {
				case 'discount_value':
					$return = $this->get_discount_value();
					break;
				case 'coupon_code':
					$return = $this->post->post_title;
					break;
				default:
					if ( ! empty( $this->post->{$prop} ) ) {
						$return = $this->post->{$prop};
					}
			}

			return $return;
		}

		/**
		 * Get discount value.
		 *
		 * @since 2.0
		 *
		 * @param int $subtotal
		 *
		 * @return float|int
		 */
		public function get_discount_value( $subtotal = 0 ) {
			remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			$discount = 0;
			switch ( $this->_settings['coupon_discount_type'] ) {
				case 'percent_cart':
					$cart     = WPHB_Cart::instance();
					$subtotal = $cart->get_sub_total();
					$discount = $subtotal * $this->_settings['coupon_discount_value'] / 100;
					break;
				case 'fixed_cart':
					$discount = $this->_settings['coupon_discount_value'];
					break;
			}
			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			return $discount;
		}

		/**
		 * Apply sub total with discount.
		 *
		 * @since 2.0
		 *
		 * @param $sub_total
		 *
		 * @return int
		 */
		public function apply_sub_total_discount( $sub_total ) {
			$discount = $this->get_discount_value( $sub_total );

			return $discount < $sub_total ? $sub_total - $discount : 0;
		}


		/**
		 * Get cart sub total with discount.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_cart_sub_total() {
			remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
			$cart           = WPHB_Cart::instance();
			$cart_sub_total = $cart->get_sub_total();
			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			return $cart_sub_total;
		}

		/**
		 * Check validate coupon.
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		public function validate() {
			$return = array(
				'is_valid' => true
			);
			if ( ! empty( $this->_settings['minimum_spend'] ) && ( $minimum_spend = intval( $this->_settings['minimum_spend'] ) > 0 ) ) {
				$return['is_valid'] = $this->get_cart_sub_total() >= $minimum_spend;
				if ( ! $return['is_valid'] ) {
					$return['message'] = sprintf( __( 'The minimum spend for this coupon is %s.', 'tp-hotel-booking' ), $minimum_spend );
				}
			}

			if ( $return['is_valid'] && ! empty( $this->_settings['maximum_spend'] ) && ( $maximum_spend = intval( $this->_settings['maximum_spend'] ) > 0 ) ) {
				$return['is_valid'] = $this->get_cart_sub_total() <= $maximum_spend;
				if ( ! $return['is_valid'] ) {
					$return['message'] = sprintf( __( 'The maximum spend for this coupon is %s.', 'tp-hotel-booking' ), $maximum_spend );
				}
			}

			if ( $return['is_valid'] && ! empty( $this->_settings['limit_per_coupon'] ) && ( $limit_per_coupon = intval( $this->_settings['limit_per_coupon'] ) ) > 0 ) {
				$usage_count        = ! empty( $this->_settings['usage_count'] ) ? intval( $this->_settings['usage_count'] ) : 0;
				$return['is_valid'] = $limit_per_coupon > $usage_count;
				if ( ! $return['is_valid'] ) {
					$return['message'] = __( 'Coupon usage limit has been reached.', 'tp-hotel-booking' );
				}
			}

			return $return;
		}

		/**
		 * Add coupon in cart generate transaction.
		 *
		 * @since 2.0
		 *
		 * @param $booking_info
		 *
		 * @return mixed
		 */
		public function add_coupon_transaction( $booking_info ) {
			$cart = WPHB_Cart::instance();
			if ( $cart->coupon ) {
				$coupon                           = WPHB_Coupon::instance( $cart->coupon );
				$booking_info['_hb_coupon_id']    = $coupon->ID;
				$booking_info['_hb_coupon_code']  = $coupon->coupon_code;
				$booking_info['_hb_coupon_value'] = $coupon->discount_value;
			}

			return $booking_info;
		}

		/**
		 * Update coupon usage count.
		 *
		 * @since 2.0
		 *
		 * @param $booking_id
		 * @param $old_status
		 * @param $new_status
		 */
		public function update_coupon_usage( $booking_id, $old_status, $new_status ) {
			if ( $coupons = get_post_meta( $booking_id, '_hb_coupon_id' ) ) {
				if ( ! $coupons ) {
					return;
				}
				foreach ( $coupons as $coupon ) {
					$usage_count = get_post_meta( $coupon, '_hb_usage_count', true );
					if ( strpos( $new_status, 'completed' ) == 0 ) {
						$usage_count ++;
					} else {
						if ( $usage_count > 0 ) {
							$usage_count --;
						} else {
							$usage_count = 0;
						}
					}
					update_post_meta( $coupon, '_hb_usage_count', $usage_count );
				}
			}
		}


		/**
		 * Add coupon date meta to meta box class save meta.
		 *
		 * @since 2.0
		 *
		 * @param $value
		 * @param $field_name
		 * @param $meta_box_name
		 *
		 * @return false|int|string
		 */
		public function update_coupon_date_meta( $value, $field_name, $meta_box_name ) {
			if ( in_array( $field_name, array(
					'coupon_date_from',
					'coupon_date_to'
				) ) && $meta_box_name == 'coupon_settings'
			) {
				if ( isset( $_POST[ '_hb_' . $field_name . '_timestamp' ] ) ) {
					$value = sanitize_text_field( $_POST[ '_hb_' . $field_name . '_timestamp' ] );
				} else {
					$value = strtotime( $value );
				}
			}

			return $value;
		}


		/**
		 * Get coupons active.
		 *
		 * @since 2.0
		 *
		 * @param $date
		 * @param bool $code
		 *
		 * @return array|bool
		 */
		public function get_coupons_active( $date, $code = false ) {

			$coupons = false;
			if ( $code ) {
				$args = array(
					'post_type'      => 'hb_coupon',
					'posts_per_page' => 999,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => '_hb_coupon_date_from_timestamp',
							'compare' => '<=',
							'value'   => $date
						),
						array(
							'key'     => '_hb_coupon_date_to_timestamp',
							'compare' => '>=',
							'value'   => $date
						)
					)
				);

				if ( $coupons = get_posts( $args ) ) {
					$found = false;
					foreach ( $coupons as $coupon ) {
						if ( strcmp( $coupon->post_title, $code ) == 0 ) {
							$coupons = $coupon;
							$found   = true;
							break;
						}
					}
					if ( ! $found ) {
						$coupons = false;
					}
				}
			}

			return $coupons;
		}

		/**
		 * Get unique instance of WPHB_Coupon.
		 *
		 * @since 2.0
		 *
		 * @param $coupon
		 *
		 * @return mixed
		 */
		public static function instance( $coupon = null ) {
			$post = $coupon;

			$id = null;
			if ( $coupon instanceof WP_Post ) {
				$id = $coupon->ID;
			} elseif ( is_object( $coupon ) && isset( $coupon->ID ) ) {
				$id = $coupon->ID;
			} elseif ( $coupon instanceof WPHB_Coupon ) {
				$id = $coupon->post->ID;
			} else {
				$id = $coupon;
			}

			if ( isset( self::$_instance[ $id ] ) ) {
				return self::$_instance[ $id ];
			}

			return self::$_instance[ $id ] = new self( $post );
		}

	}

}