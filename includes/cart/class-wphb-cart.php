<?php

/**
 * WP Hotel Booking cart class.
 *
 * @class       WPHB_Cart
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Cart' ) ) {

	/**
	 * Class WPHB_Cart.
	 *
	 * @since 2.0
	 */
	class WPHB_Cart {

		/**
		 * @var bool
		 */
		private static $instance = null;

		/**
		 * $sessions object
		 * @var null
		 */
		public $sessions = null;

		/**
		 * $customer_sessions object
		 * @var null
		 */
		private $customer_sessions = null;

		/**
		 * $customer_sessions object
		 * @var null
		 */
		private $booking_sessions = null;
		// load cart contents
		public $cart_contents = array();
		public $cart_total_include_tax = 0;
		public $cart_total = 0;
		public $cart_total_exclude_tax = 0;
		public $cart_items_count = 0;
		// customer
		public $customer_id = null;
		// customer
		public $customer_email = null;
		// coupon
		public $coupon = null;
		// booking id
		public $booking_id = null;

		function __construct( $appfix = null ) {
			// session class
			$this->sessions = WPHB_Sessions::instance( 'thimpress_hotel_booking_' . WPHB_BLOG_ID . $appfix, true );

			// session customer object
			$this->customer_sessions = WPHB_Sessions::instance( 'thimpress_hotel_booking_customer_' . WPHB_BLOG_ID . $appfix, true );

			// session booking object
			$this->booking_sessions = WPHB_Sessions::instance( 'thimpress_hotel_booking_info_' . WPHB_BLOG_ID . $appfix, true );

			// refresh cart session
			add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );

			// update init hook
			add_action( 'init', array( $this, 'hotel_booking_cart_update' ), 999 );


//======================================= Extra cart hooks ============================================================//
			/**
			 * new script
			 */
			add_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );
			/**
			 * add filter add to cart results array
			 * render object build mini cart
			 */
			add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_results' ), 10, 2 );

			/**
			 * after mini cart item loop
			 */
			add_action( 'hotel_booking_before_mini_cart_loop_price', array( $this, 'mini_cart_loop' ), 10, 2 );

			/**
			 * sortable cart item
			 */
			add_filter( 'hotel_booking_load_cart_from_session', array(
				$this,
				'hotel_booking_load_cart_from_session'
			), 10, 1 );


			/**
			 * append package into cart
			 */
			add_action( 'hotel_booking_cart_after_item', array( $this, 'cart_package_after_item' ), 10, 2 );

			/**
			 * append package into cart admin
			 */
			add_action( 'hotel_booking_admin_cart_after_item', array( $this, 'admin_cart_package_after_item' ), 10, 3 );

			// email new booking hook
			add_action( 'hotel_booking_email_new_booking', array( $this, 'email_new_booking' ), 10, 3 );

			add_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );

			// room item in booking details
			add_action( 'hotel_booking_after_room_item', array( $this, 'booking_post_type_extra_item' ), 10, 2 );

			// room item in email
			add_action( 'hotel_booking_email_after_room_item', array(
				$this,
				'email_booking_post_type_extra_item'
			), 10, 2 );


			// admin process
			add_filter( 'hotel_booking_check_room_available', array( $this, 'admin_load_package' ) );
			add_filter( 'hotel_booking_admin_load_order_item', array( $this, 'admin_load_package' ) );

			add_action( 'hotel_booking_updated_order_item', array( $this, 'admin_add_package_order' ), 10, 2 );
		}

		public function wp_loaded() {
			$this->refresh();
		}

		function __get( $key ) {
			switch ( $key ) {
				case 'cart_contents':
					$return = $this->get_cart_contents();
					break;

				case 'cart_total_include_tax':
					$return = $this->cart_total_include_tax();
					break;

				case 'cart_total_exclude_tax':
					$return = $this->cart_total_exclude_tax();
					break;

				case 'cart_items_count':
					$return = count( $this->get_cart_contents() );
					break;

				case 'sub_total':
					$return = $this->get_sub_total();
					break;

				case 'total':
					$return = $this->get_total();
					break;

				case 'advance_payment':
					$return = $this->get_advance_payment();
					break;

				default:
					$return = '';
					break;
			}

			return $return;
		}

		// load cart contents
		function get_cart_contents() {
			// load cart session object
			if ( $this->sessions && $this->sessions->session ) {
				foreach ( $this->sessions->session as $cart_id => $param ) {
					$cart_item = new stdClass;
					if ( is_array( $param ) || is_object( $param ) ) {
						foreach ( $param as $k => $v ) {
							$cart_item->{$k} = $v;
						}

						if ( $cart_item->product_id ) {
							// product class
							$product = hotel_booking_get_product_class( $cart_item->product_id, $param );
							// set product data
							$cart_item->product_data = $product;
							// amount item include tax
							$cart_item->amount_include_tax = apply_filters( 'hotel_booking_cart_item_amount_incl_tax', $product->amount_include_tax(), $cart_id, $cart_item, $product );

							// amount item exclude tax
							$cart_item->amount_exclude_tax = apply_filters( 'hotel_booking_cart_item_amount_excl_tax', $product->amount_exclude_tax(), $cart_id, $cart_item, $product );

							// amount item exclude tax
							$cart_item->amount = apply_filters( 'hotel_booking_cart_item_total_amount', $product->amount( true ), $cart_id, $cart_item, $product );

							// amount tax
							$cart_item->amount_tax = $cart_item->amount_include_tax - $cart_item->amount_exclude_tax;

							// singular include tax
							$cart_item->amount_singular_include_tax = apply_filters( 'hotel_booking_cart_item_amount_singular_incl_tax', $product->amount_singular_include_tax(), $cart_id, $cart_item, $product );

							// singular exclude tax
							$cart_item->amount_singular_exclude_tax = apply_filters( 'hotel_booking_cart_item_amount_singular_incl_tax', $product->amount_singular_exclude_tax(), $cart_id, $cart_item, $product );

							// singular
							$cart_item->amount_singular = apply_filters( 'hotel_booking_cart_item_amount_singular', $product->amount_singular( true ), $cart_id, $cart_item, $product );
						}

						$this->cart_contents[ $cart_id ] = $cart_item;
					}
				}
			}

			return apply_filters( 'hotel_booking_load_cart_from_session', $this->cart_contents );
		}

		// load customer
		function load_customer() {
			// load customer session object
			if ( $this->customer_sessions && $this->customer_sessions->session ) {
				if ( isset( $this->customer_sessions->session['customer_id'] ) ) {
					$this->customer_id = $this->customer_sessions->session['customer_id'];
				}

				if ( isset( $this->customer_sessions->session['customer_email'] ) ) {
					$this->customer_email = $this->customer_sessions->session['customer_email'];
				}

				if ( isset( $this->customer_sessions->session['coupon'] ) ) {
					$this->coupon = $this->customer_sessions->session['coupon'];
				}
				$this->customer_id = apply_filters( 'hotel_booking_load_customer_from_session', $this->customer_id );
				$this->coupon      = apply_filters( 'hotel_booking_load_customer_from_session', $this->coupon );
			}
		}

		// load booking
		function load_booking() {
			// load customer session object
			if ( $this->booking_sessions && $this->booking_sessions->session ) {
				if ( isset( $this->booking_sessions->session['booking_id'] ) ) {
					$this->booking_id = $this->booking_sessions->session['booking_id'];
				}
				$this->booking_id = apply_filters( 'hotel_booking_load_booking_from_session', $this->booking_id );
			}
		}

		/**
		 * add_to_cart
		 *
		 * @param $post_id
		 * @param $params product
		 * @param $qty product
		 * @param $group_post_id use with extra packages
		 * @param $asc if set true $qty++
		 */
		function add_to_cart( $post_id = null, $params = array(), $qty = 1, $group_post_id = null, $asc = false ) {
			if ( ! $post_id ) {
				return new WP_Error( 'hotel_booking_add_to_cart_error', __( 'Can not add to cart, product is not exist.', 'wp-hotel-booking' ) );
			}

			$post_id = absint( $post_id );

			$cart_item_id = $this->generate_cart_id( $params );
			if ( $qty == 0 ) {
				return $this->remove_cart_item( $cart_item_id );
			}

			// set params product_id
			$params['product_id'] = $post_id;

			// set params quantity
			$params['quantity'] = $qty;

			$params = apply_filters( 'hotel_booking_add_to_cart_params', $params, $post_id );

			if ( ! isset( $params['quantity'] ) ) {
				return;
			}

			// cart item is exist
			if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
				$this->update_cart_item( $cart_item_id, $qty, $asc, false );
			} else {
				// set session cart
				$this->sessions->set( $cart_item_id, $params );
			}

			// do action
			do_action( 'hotel_booking_added_cart', $cart_item_id, $params, $_POST );

			// do action woocommerce
			$cart_item_id = apply_filters( 'hotel_booking_added_cart_results', $cart_item_id, $params, $_POST );

			// refresh cart
			$this->refresh();

			return $cart_item_id;
		}

		// update cart item
		function update_cart_item( $cart_id = null, $qty = 0, $asc = false, $refresh = true ) {
			if ( ! $cart_id ) {
				return;
			}

			if ( ! empty( $this->cart_contents[ $cart_id ] ) && $cart_item = $this->get_cart_item_param( $cart_id ) ) {
				if ( $qty === 0 ) {
					$this->remove_cart_item( $cart_id );
				}

				if ( $asc === true ) {
					$qty = $qty + $this->cart_contents[ $cart_id ]->quantity;
				}

				$cart_item['quantity'] = $qty;

				$this->sessions->set( $cart_id, $cart_item );

				do_action( 'hotel_booking_updated_cart_item', $cart_id, $cart_item );

				// refresh cart
				if ( $refresh ) {
					$this->refresh();
				}
			}
		}

		// remove cart item by id
		function remove_cart_item( $cart_item_id = null ) {
			$remove_params = array();
			if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
				$item = $this->cart_contents[ $cart_item_id ];

				// param generate cart id
				$remove_params = array(
					'product_id'     => $item->product_id,
					'check_in_date'  => $item->check_in_date,
					'check_out_date' => $item->check_out_date
				);
				if ( isset( $item->parent_id ) ) {
					$remove_params['parent_id'] = $item->parent_id;
				}
				// hook
				do_action( 'hotel_booking_remove_cart_item', $cart_item_id, $remove_params );
				// unset
				unset( $this->cart_contents[ $cart_item_id ] );
			}

			// set null
			$this->sessions->set( $cart_item_id, null );

			if ( ! empty( $this->cart_contents ) ) {
				foreach ( $this->cart_contents as $cart_id => $cart_item ) {
					if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_item_id ) {
						$item = $this->cart_contents[ $cart_id ];
						// unset
						unset( $this->cart_contents[ $cart_id ] );
						// param generate cart id
						$param = array(
							'product_id'     => $item->product_id,
							'check_in_date'  => $item->check_in_date,
							'check_out_date' => $item->check_out_date
						);
						if ( isset( $item->parent_id ) ) {
							$param['parent_id'] = $item->parent_id;
						}

						// hook
						do_action( 'hotel_booking_remove_cart_sub_item', $cart_item_id, $param );
						// set session, cookie
						$this->sessions->set( $cart_id, null );
						// hook
						do_action( 'hotel_booking_removed_cart_sub_item', $cart_item_id, $param );
					}
				}
			}
			// hook
			do_action( 'hotel_booking_removed_cart_item', $cart_item_id, $remove_params );

			// refresh cart
			$this->refresh();

			// return cart item removed
			return $cart_item_id;
		}

		function get_products() {

			$products = array();
			if ( ! $this->cart_contents ) {
				return $products;
			}

			foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
				$products[ $cart_item_id ] = $cart_item->product_data;
				if ( isset( $cart_item->parent_id ) ) {
					$products[ $cart_item_id ]->parent_id = $cart_item->parent_id;
				}
			}

			return $products;
		}

		// get rooms of cart_contents
		function get_rooms() {
			if ( ! $this->cart_contents ) {
				return null;
			}

			$rooms = array();
			foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
				if ( ! isset( $cart_item->parent_id ) ) {
					$rooms[ $cart_item_id ] = $cart_item->product_data;
				}
			}

			return $rooms;
		}

		// get extra packages
		function get_extra_packages( $parent_cart_id = null ) {
			$packages = array();
			if ( $this->cart_contents ) {
				foreach ( $this->cart_contents as $cart_id => $cart_item ) {
					if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $parent_cart_id ) {
						$packages[ $cart_id ] = $cart_item;
					}
				}
			}

			return $packages;
		}

		// set empty cart
		function empty_cart() {
			// remove
			$this->cart_contents = array();

			if ( $this->sessions ) {
				// reset all sessions
				$this->sessions = $this->sessions->remove();
			}

			if ( $this->booking_sessions ) {
				$this->booking_sessions = $this->booking_sessions->remove();
			}

			$this->set_customer( 'coupon', null );

			do_action( 'hotel_booking_empty_cart' );
			// refresh cart contents
			$this->refresh();
		}

		// generate cart item id
		function generate_cart_id( $params = array() ) {
			ksort( $params );

			return hb_generate_cart_item_id( $params );
		}

		// get cart item
		function get_cart_item( $cart_item_id = null ) {
			if ( ! $cart_item_id ) {
				return null;
			}

			if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
				return $this->cart_contents[ $cart_item_id ];
			}

			return null;
		}

		// get cart item params
		function get_cart_item_param( $cart_item_id = null ) {
			$params    = array();
			$cart_item = $this->get_cart_item( $cart_item_id );
			if ( $cart_item ) {
				$params = array(
					'product_id'     => $cart_item->product_id,
					'check_in_date'  => $cart_item->check_in_date,
					'check_out_date' => $cart_item->check_out_date,
				);
				if ( isset( $cart_item->parent_id ) ) {
					$params['parent_id'] = $cart_item->parent_id;
				}
			}

			return apply_filters( 'hotel_booking_cart_item_atributes', $params );
		}

		// set customer object
		function set_customer( $name = null, $val = null ) {
			if ( ! $name ) {
				return;
			}
			// set session cart
			$this->customer_sessions->set( $name, $val );
			if ( isset( $this->customer_sessions->session[ $name ] ) ) {
				$this->customer_sessions->session[ $name ] = $val;
			}
			// refresh
			$this->load_customer();
		}

		// set customer object
		function set_booking( $name = null, $val = null ) {
			if ( ! $name || ! $val ) {
				return;
			}
			// set session cart
			$this->booking_sessions->set( $name, $val );

			// refresh
			$this->load_booking();
		}

		// get cart item by parent_id
		function get_cart_item_by_parent( $parent_id = null ) {
			if ( ! $parent_id || empty( $this->cart_contents ) ) {
				return;
			}

			$results = array();
			foreach ( $this->cart_contents as $cart_id => $cart_item ) {
				if ( isset( $cart_item->parent_id ) === $parent_id ) {
					$results[ $cart_id ] = $cart_item;
				}
			}

			return $results;
		}

		// refresh carts
		function refresh() {
			// refresh cart_contents
			$this->cart_contents = $this->get_cart_contents();

			// refresh cart_totals
			$this->cart_total_include_tax = $this->cart_total = $this->cart_total_include_tax();

			// refresh cart_totals_exclude_tax
			$this->cart_totals_exclude_tax = $this->cart_total_exclude_tax();

			// refresh cart_items_count
			$this->cart_items_count = count( $this->cart_contents );

			// refresh customer
			$this->load_customer();

			// refresh booking
			$this->load_booking();
		}

		// update cart
		function hotel_booking_cart_update() {
			if ( ! isset( $_POST ) || empty( $_POST['hotel_booking_cart'] ) ) {
				return;
			}

			if ( ! isset( $_POST['hotel_booking_cart'] ) ) {
				return;
			}

			if ( ! isset( $_POST['hb_cart_field'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['hb_cart_field'] ), 'hb_cart_field' ) ) {
				return;
			}

			$cart_number   = (array) $_POST['hotel_booking_cart'];
			$cart_contents = $this->get_cart_contents();
			foreach ( $cart_number as $cart_id => $qty ) {
				// if not in array keys $cart_contents
				if ( ! array_key_exists( $cart_id, $cart_contents ) ) {
					continue;
				}

				$cart_item = $cart_contents[ $cart_id ];

				if ( ! $cart_item ) {
					continue;
				}

				if ( $qty == 0 ) {
					$this->remove_cart_item( $cart_id );
				} else {
					$this->update_cart_item( $cart_id, $qty );
				}
			}

			do_action( 'hotel_booking_cart_update', (array) $_POST );
			//refresh
			$this->refresh();

			return;
		}

		// cart total include tax
		function cart_total_include_tax() {
			$total = 0;
			if ( ! empty( $this->cart_contents ) ) {
				foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
					$total = $total + $cart_item->amount_include_tax;
				}
			}

			return apply_filters( 'hotel_booking_cart_total_include_tax', $total );
		}

		// cart total exclude tax
		function cart_total_exclude_tax() {
			$total = 0;
			if ( ! empty( $this->cart_contents ) ) {
				foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
					$total = $total + $cart_item->amount_exclude_tax;
				}
			}

			return apply_filters( 'hotel_booking_cart_total_exclude_tax', $total );
		}

		/**
		 * Calculate sub total (without tax) and return
		 *
		 * @return mixed
		 */
		function get_sub_total() {
			return apply_filters( 'hb_cart_sub_total', $this->cart_total_exclude_tax() );
		}

		/**
		 * Calculate cart total (with tax) and return
		 *
		 * @return mixed
		 */
		function get_total() {
			return apply_filters( 'hotel_booking_get_cart_total', $this->sub_total + $this->sub_total * hb_get_tax_settings() );
		}

		/**
		 * Get advance payment based on cart total
		 *
		 * @return float|int
		 */
		function get_advance_payment() {
			$total = $this->get_total();
			if ( $advance_payment = hb_get_advance_payment() ) {
				$total = $total * $advance_payment / 100;
			}

			return $total;
		}

		// total > 0
		public function needs_payment() {
			return apply_filters( 'hb_cart_needs_payment', $this->total > 0, $this );
		}

		function is_empty() {
			return apply_filters( 'hotel_booking_cart_is_empty', $this->cart_items_count ? true : false );
		}

		/**
		 * generate transaction object payment
		 * @return object
		 */
		function generate_transaction( $payment_method = null ) {
			if ( $this->is_empty ) {
				return new WP_Error( 'hotel_booking_transaction_error', __( 'Your cart is empty.', 'wp-hotel-booking' ) );
			}

			// initialize object
			$transaction  = new stdClass();
			$booking_info = array();

			// use coupon
			$settings = hb_settings();
			$cart     = WPHB_Cart::instance();
			if ( $settings->get( 'enable_coupon' ) && $coupon = $cart->coupon ) {
				$coupon = WPHB_Coupon::instance( $coupon );

				$booking_info['_hb_coupon_id']    = $coupon->ID;
				$booking_info['_hb_coupon_code']  = $coupon->coupon_code;
				$booking_info['_hb_coupon_value'] = $coupon->discount_value;
			}

			// booking info array param
			$booking_info = array_merge( $booking_info, array(
				'_hb_tax'                     => $this->cart_total_include_tax - $this->cart_total_exclude_tax,
				'_hb_advance_payment'         => $this->hb_get_cart_total( ! hb_get_request( 'pay_all' ) ),
				'_hb_advance_payment_setting' => hb_settings()->get( 'advance_payment', 50 ),
				'_hb_currency'                => apply_filters( 'hotel_booking_payment_currency', hb_get_currency() ),
				// '_hb_customer_id'               => $customer_id,
				'_hb_user_id'                 => get_current_blog_id(),
				'_hb_method'                  => $payment_method->slug,
				'_hb_method_title'            => $payment_method->title,
				'_hb_method_id'               => $payment_method->method_id,
				// customer
				'_hb_customer_title'          => hb_get_request( 'title' ),
				'_hb_customer_first_name'     => hb_get_request( 'first_name' ),
				'_hb_customer_last_name'      => hb_get_request( 'last_name' ),
				'_hb_customer_address'        => hb_get_request( 'address' ),
				'_hb_customer_city'           => hb_get_request( 'city' ),
				'_hb_customer_state'          => hb_get_request( 'state' ),
				'_hb_customer_postal_code'    => hb_get_request( 'postal_code' ),
				'_hb_customer_country'        => hb_get_request( 'country' ),
				'_hb_customer_phone'          => hb_get_request( 'phone' ),
				'_hb_customer_email'          => hb_get_request( 'email' ),
				'_hb_customer_fax'            => hb_get_request( 'fax' )
			) );

			// set booking info
			$transaction->booking_info = $booking_info;

			// get rooms
			$products  = $this->get_products();
			$_products = array();
			foreach ( $products as $k => $product ) {
				$check_in  = strtotime( $product->get_data( 'check_in_date' ) );
				$check_out = strtotime( $product->get_data( 'check_out_date' ) );
				$total     = $product->amount_include_tax();
				$sub_total = $product->amount_exclude_tax();

				$_products[ $k ] = apply_filters( 'hb_generate_transaction_object_room', array(
					'parent_id'      => isset( $product->parent_id ) ? $product->parent_id : null,
					'product_id'     => $product->ID,
					'qty'            => $product->get_data( 'quantity' ),
					'check_in_date'  => $check_in,
					'check_out_date' => $check_out,
					'subtotal'       => $sub_total,
					'total'          => $total,
					'tax_total'      => $total - $sub_total
				), $product );
			}

			$transaction->order_items = $_products;

			return apply_filters( 'hb_generate_transaction_object', $transaction, $payment_method );
		}

		/**
		 * Get cart total
		 *
		 * @param bool $pre_paid
		 *
		 * @return float|int|mixed
		 */
		function hb_get_cart_total( $pre_paid = false ) {
			if ( $pre_paid ) {
				$total = $this->get_advance_payment();
			} else {
				$total = $this->total;
			}

			return $total;
		}

		// instance instead of new Class
		static function instance( $appfix = null ) {
			if ( empty( self::$instance[ $appfix ] ) ) {
				return self::$instance[ $appfix ] = new self( $appfix );
			}

			return self::$instance[ $appfix ];
		}


//==================================================== Extra cart functions ==========================================//

		function ajax_added_cart( $cart_id, $cart_item, $posts ) {
			if ( empty( $posts['hb_optional_quantity_selected'] ) || empty( $posts['hb_optional_quantity'] ) ) {
				return;
			}

			remove_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );

			if ( $posts['hb_optional_quantity_selected'] ) {
				$selected_quantity = $posts['hb_optional_quantity'];
				$turn_on           = $posts['hb_optional_quantity_selected'];

				foreach ( $selected_quantity as $extra_id => $qty ) {
					// param
					$param = array(
						'product_id'     => $extra_id,
						'parent_id'      => $cart_id,
						'check_in_date'  => $cart_item['check_in_date'],
						'check_out_date' => $cart_item['check_out_date']
					);
					if ( array_key_exists( $extra_id, $turn_on ) ) {
						$extra_cart_item_id = $this->add_to_cart( $extra_id, $param, $qty );
					} else {
						$extra_cart_item_id = $this->generate_cart_id( $param );
						$this->remove_cart_item( $extra_cart_item_id );
					}
				}
			}
			add_action( 'hotel_booking_added_cart', array( $this, 'ajax_added_cart' ), 10, 3 );
		}

		/**
		 * extra package each cart item
		 *
		 * @param
		 *
		 * @return
		 */
		public function mini_cart_loop( $room, $cart_id ) {
			$cart_item = $this->get_cart_item( $cart_id );
			if ( ! $cart_item ) {
				return;
			}

			$packages = array();
			foreach ( $this->cart_contents as $id => $cart_item ) {
				if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
					$cart_item->cart_id = $id;
					$packages[]         = $cart_item;
				}
			}

			ob_start();
			hb_get_template( 'extra/mini-cart-extra.php', array( 'packages' => $packages ) );
			echo ob_get_clean();
		}

		// add extra price
		function hotel_booking_load_cart_from_session( $cart_contents ) {
			foreach ( $cart_contents as $parent_id => $cart_item ) {
				if ( ! isset( $cart_item->parent_id ) ) {
					foreach ( $cart_contents as $id => $item ) {
						if ( isset( $item->parent_id ) && $item->parent_id === $parent_id ) {
							$cart_contents[ $parent_id ]->amount += $item->amount;
						}
					}
				}
			}

			return $cart_contents;
		}

		/**
		 * add to cart results
		 *
		 * @param [array] $results [results]
		 * @param [object] $room    [room object class]
		 */
		function add_to_cart_results( $results, $room ) {
			if ( ! isset( $results['cart_id'] ) ) {
				return $results;
			}

			$cart_id       = $results['cart_id'];
			$cart_contents = $this->cart_contents;

			if ( $cart_contents ) {
				$extra_packages = array();
				foreach ( $cart_contents as $cart_item_id => $cart_item ) {
					if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
						// extra class
						$extra            = WPHB_Extra_Package::instance( $cart_item->product_id );
						$extra_packages[] = array(
							'package_title'    => sprintf( '%s (%s)', $extra->title, hb_format_price( $extra->amount_singular ) ),
							'package_id'       => $extra->ID,
							'cart_id'          => $cart_item_id,
							'package_quantity' => sprintf( 'x%s', $cart_item->quantity )
						);
					}
				}
				$results['extra_packages'] = $extra_packages;
			}

			return $results;
		}

		// cart fontend
		function cart_package_after_item( $room, $cart_id ) {
			$cart           = WPHB_Cart::instance();
			$extra_packages = $cart->get_extra_packages( $cart_id );

			if ( $extra_packages ) {
				if ( is_hb_checkout() ) {
					$page = 'checkout';
				} else {
					$page = 'cart';
				}

				hb_get_template( 'extra/addition-services-title.php', array(
					'page'    => $page,
					'room'    => $room,
					'cart_id' => $cart_id
				) );
				foreach ( $extra_packages as $package_cart_id => $cart_item ) {
					hb_get_template( 'extra/cart-extra-package.php', array(
						'cart_id' => $package_cart_id,
						'package' => $cart_item
					) );
				}
			}
		}

		// cart admin
		function admin_cart_package_after_item( $cart_params, $cart_id, $booking ) {
			$html = array();
			ob_start();
			$html[] = ob_get_clean();
			foreach ( $cart_params as $id => $cart_item ) {
				if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
					ob_start();
					$html[] = ob_get_clean();
				}
			}
			echo implode( '', $html );
		}

		// email new booking
		function email_new_booking( $cart_params, $cart_id, $booking ) {
			?>
            <tr class="hb_addition_services_title hb_table_center">
                <td style="text-align: center;" colspan="7">
					<?php _e( 'Addition Services', 'wp-hotel-booking' ); ?>
                </td>
            </tr>
			<?php
			foreach ( $cart_params as $id => $cart_item ) {
				if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
					?>
                    <tr style="background-color: #FFFFFF;">

                        <td></td>

                        <td>
							<?php echo esc_html( $cart_item->quantity ); ?>
                        </td>

                        <td colspan="3">
							<?php printf( '%s', $cart_item->product_data->title ) ?>
                        </td>

                        <td>
							<?php echo hb_format_price( $cart_item->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ) ?>
                        </td>

                    </tr>

					<?php
				}
			}
		}

		function check_respondent( $respondent ) {
			// remove_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );
			if ( is_page( hb_get_page_id( 'checkout' ) ) || hb_get_request( 'hotel-booking' ) === 'checkout' ) {
				return false;
			}

			if ( is_page( hb_get_page_id( 'cart' ) ) || hb_get_request( 'hotel-booking' ) === 'cart' ) {
				if ( $respondent === 'trip' ) {
					return false;
				}
			}
			add_filter( 'hb_extra_cart_input', array( $this, 'check_respondent' ) );

			return $respondent;
		}

		function booking_post_type_extra_item( $room, $hb_booking ) {
			$packages = hb_get_order_items( $hb_booking->id, 'sub_item', $room->order_item_id );

			if ( ! $packages ) {
				return;
			}

			$html = array();
			foreach ( $packages as $k => $package ) {
				$extra = hotel_booking_get_product_class( hb_get_order_item_meta( $package->order_item_id, 'product_id', true ) );
				// $extra->respondent === 'number'
				$html[] = '<tr data-order-parent="' . esc_attr( $room->order_item_id ) . '">';

				$html[] = sprintf( '<td class="center"><input type="checkbox" name="book_item[]" value="%s" /></td>', $package->order_item_id );

				$html[] = sprintf( '<td class="name" colspan="3">%s</td>', $package->order_item_name );

				$html[] = sprintf( '<td class="qty">%s</td>', hb_get_order_item_meta( $package->order_item_id, 'qty', true ) );

				$html[] = sprintf( '<td class="total">%s</td>', hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $hb_booking->currency ) ) );

				$html[] = '<td class="actions">';

				if ( $extra->respondent === 'number' ) {
					$html[] = '<a href="#" class="edit" data-order-id="' . esc_attr( $hb_booking->id ) . '" data-order-item-id="' . esc_attr( $package->order_item_id ) . '" data-order-item-type="sub_item" data-order-item-parent="' . $package->order_item_parent . '">
							<i class="fa fa-pencil"></i>
						</a>';
				}
				$html[] = '<a href="#" class="remove" data-order-id="' . esc_attr( $hb_booking->id ) . '" data-order-item-id="' . esc_attr( $package->order_item_id ) . '" data-order-item-type="sub_item" data-order-item-parent="' . $package->order_item_parent . '">
						<i class="fa fa-times-circle"></i>
					</a>
				</td>';

				$html[] = '</tr>';
			}

			printf( '%s', implode( '', $html ) );
		}

		function email_booking_post_type_extra_item( $room, $hb_booking ) {
			$packages = hb_get_order_items( $hb_booking->id, 'sub_item', $room->order_item_id );

			if ( ! $packages ) {
				return;
			}

			$html = array();
			foreach ( $packages as $k => $package ) {
				$html[] = '<tr>';

				$html[] = '<td>' . sprintf( '%s', $package->order_item_name ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $package->order_item_id, 'check_in_date', true ) ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $package->order_item_id, 'check_out_date', true ) ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', hb_get_order_item_meta( $package->order_item_id, 'qty', true ) ) . '</td>';
				$html[] = '<td>' . sprintf( '%s', hb_format_price( hb_get_order_item_meta( $package->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $hb_booking->currency ) ) ) . '</td>';

				$html[] = '</tr>';
			}

			printf( '%s', implode( '', $html ) );
		}

		// load package in edit room
		public function admin_load_package( $args ) {

			if ( ! isset( $args['product_id'] ) ) {
				return $args;
			}

			$product_id = absint( $args['product_id'] );
			if ( get_post_type( $product_id ) !== 'hb_room' ) {
				return $args;
			}

			$extra_product = WPHB_Extra_Product::instance( $product_id );
			$room_extra    = $extra_product->get_extra();

			$order_child_id = array();
			$order_subs     = array();
			if ( isset( $args['order_id'], $args['order_item_id'] ) ) {
				$sub_items = hb_get_sub_item_order_item_id( $args['order_item_id'] );
				if ( $sub_items ) {
					foreach ( $sub_items as $it_id ) {
						$order_child_id[ hb_get_order_item_meta( $it_id, 'product_id', true ) ] = hb_get_order_item_meta( $it_id, 'qty', true );
						$order_subs[ hb_get_order_item_meta( $it_id, 'product_id', true ) ]     = $it_id;
					}
				}
			}

			if ( $room_extra ) {
				$args['sub_items'] = array();
				foreach ( $room_extra as $k => $extra ) {
					$param = array(
						'ID'         => $extra->ID,
						'title'      => $extra->title,
						'respondent' => $extra->respondent,
						'selected'   => array_key_exists( $extra->ID, $order_child_id ) ? true : false,
						'qty'        => array_key_exists( $extra->ID, $order_child_id ) ? $order_child_id[ $extra->ID ] : 1
					);
					if ( isset( $order_subs[ $extra->ID ] ) ) {
						$param['order_item_id'] = $order_subs[ $extra->ID ];
					}
					$args['sub_items'][] = $param;
				}
			}

			return $args;
		}

		public function admin_add_package_order( $order_id, $order_item_id ) {
			if ( ! isset( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['sub_items'] ) ) {
				return;
			}

			$sub_items      = $_POST['sub_items'];
			$check_in_date  = isset( $_POST['check_in_date_timestamp'] ) ? $_POST['check_in_date_timestamp'] : '';
			$check_out_date = isset( $_POST['check_out_date_timestamp'] ) ? $_POST['check_out_date_timestamp'] : '';

			foreach ( $sub_items as $product_id => $optional ) {
				if ( isset( $optional['checked'] ) && $optional['checked'] === 'on' ) {
					$qty   = isset( $optional['qty'] ) ? $optional['qty'] : 0;
					$param = array(
						'order_item_name'   => get_the_title( $product_id ),
						'order_item_type'   => 'sub_item',
						'order_item_parent' => $order_item_id,
						'order_id'          => $order_id
					);

					$product = hotel_booking_get_product_class( $product_id, array(
						'check_in_date'  => $check_in_date,
						'check_out_date' => $check_out_date,
						'room_quantity'  => hb_get_order_item_meta( $order_item_id, 'qty', true ),
						'quantity'       => isset( $optional['qty'] ) ? $optional['qty'] : 0
					) );

					if ( isset( $optional['order_item_id'] ) ) {
						$sub_order_item_id = absint( $optional['order_item_id'] );
						if ( $qty === 0 ) {
							hb_remove_order_item( $sub_order_item_id );
						} else {
							hb_update_order_item( $sub_order_item_id, $param );
						}
					} else {
						$sub_order_item_id = hb_add_order_item( $order_id, $param );
					}

					if ( $qty ) {
						hb_update_order_item_meta( $sub_order_item_id, 'product_id', $product_id );
						hb_update_order_item_meta( $sub_order_item_id, 'qty', $qty );
						hb_update_order_item_meta( $sub_order_item_id, 'check_in_date', $check_in_date );
						hb_update_order_item_meta( $sub_order_item_id, 'check_out_date', $check_out_date );
						hb_update_order_item_meta( $sub_order_item_id, 'subtotal', $product->price );
						hb_update_order_item_meta( $sub_order_item_id, 'total', $product->price_tax );
						hb_update_order_item_meta( $sub_order_item_id, 'tax_total', $product->price_tax - $product->price );
					}

				} else {
					if ( isset( $optional['order_item_id'] ) ) {
						hb_remove_order_item( $optional['order_item_id'] );
					}
				}
			}
		}

	}

}

