<?php

/**
 * WP Hotel Booking Woocommerce class.
 *
 * @class       WPHB_Woocommerce
 * @version     2.0
 * @package     WP_Hotel_Booking_Woocommerce/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Woocommerce' ) ) {

	/**
	 * Class WPHB_Woocommerce.
	 *
	 * @since 2.0
	 */
	class WPHB_Woocommerce extends WPHB_Abstract_Payment_Gateway {

		/**
		 * @var string
		 */
		protected $_slug = 'woocommerce';

		/**
		 * WPHB_Woocommerce constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			parent::__construct();

			$this->_title = __( 'Woocommerce', 'wphb-woocommerce' );

			$settings = hb_settings();

			add_filter( 'hb_payment_gateways', array( $this, 'add_payment_gateway' ) );

			if ( 'yes' === $settings->get( 'wc_enable' ) ) {
				// filter WPHB currency to WC currency
				add_filter( 'hb_currency', array( $this, 'woocommerce_currency' ), 50 );
				add_filter( 'hotel_booking_payment_current_currency', array( $this, 'woocommerce_currency' ), 50 );
				add_filter( 'hb_currency_symbol', array( $this, 'woocommerce_currency_symbol' ), 50, 2 );
				add_filter( 'hb_price_format', array( $this, 'woocommerce_price_format' ), 50, 3 );

				// room price
				add_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );
				// extra package price
				add_filter( 'hotel_booking_extra_regular_price_tax', array(
					$this,
					'packages_regular_price_tax'
				), 10, 3 );
				// cart amount
				add_filter( 'hotel_booking_cart_item_total_amount', array( $this, 'cart_item_total_amount' ), 10, 4 );
				// tax enable
				add_filter( 'hb_price_including_tax', array( $this, 'price_including_tax' ), 10, 2 );
				/**
				 * WP Hotel Booking hook
				 * create cart item
				 * remove cart item
				 * remove extra packages
				 */
				// trigger WC cart room item
				add_filter( 'hotel_booking_added_cart', array( $this, 'hotel_add_to_cart' ), 10, 2 );
				// trigger WC remove cart room item
				add_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 2 );
				// return cart url
				add_filter( 'hb_cart_url', array( $this, 'hotel_cart_url' ) );
				// return checkout url
				add_filter( 'hb_checkout_url', array( $this, 'hotel_checkout_url' ), 999 );
				// display tax price
				add_filter( 'hotel_booking_cart_tax_display', array( $this, 'cart_tax_display' ) );
				add_filter( 'hotel_booking_get_cart_total', array( $this, 'cart_total_result_display' ) );
				add_action( 'template_redirect', array( $this, 'template_redirect' ), 50 );
				/**
				 * Woocommerce hook
				 * woocommerce_remove_cart_item remove
				 * woocommerce_update_cart_validation update
				 * woocommerce_restore_cart_item undo remove
				 */
				add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
				add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_update_cart' ), 10, 4 );
				add_action( 'woocommerce_restore_cart_item', array( $this, 'woocommerce_restore_cart_item' ), 10, 2 );
				add_filter( 'woocommerce_cart_item_class', array(
					$this,
					'woocommerce_cart_package_item_class'
				), 10, 3 );
				add_action( 'woocommerce_order_status_changed', array(
					$this,
					'woocommerce_order_status_changed'
				), 10, 3 );
				// sort room - product item
				add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'woocommerce_sort_rooms' ), 999 );

				add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
				add_filter( 'woocommerce_get_cart_item_from_session', array(
					$this,
					'get_cart_item_from_session'
				), 10, 3 );
				add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 2 );
				// // tax enable
				add_filter( 'hotel_booking_extra_tax_enable', array( $this, 'tax_enable' ) );
				// override woo mail templates
				add_filter( 'woocommerce_locate_template', array( $this, 'woo_booking_mail_template' ), 10, 3 );
			}
		}


		/**
		 * Add Authorize to WP Hotel Booking payment gateways.
		 *
		 * @since 2.0
		 *
		 * @param $payments
		 *
		 * @return mixed
		 */
		public function add_payment_gateway( $payments ) {
			if ( array_key_exists( $this->_slug, $payments ) ) {
				return $payments;
			}

			$payments[ $this->_slug ] = new self();

			return $payments;
		}

		/**
		 * Override woo mail templates.
		 *
		 * @since 2.0
		 *
		 * @param $template
		 * @param $template_name
		 * @param $template_path
		 *
		 * @return string
		 */
		public function woo_booking_mail_template( $template, $template_name, $template_path ) {
			global $woocommerce;

			$_template = $template;
			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = WPHB_WOO_PAYMENT_ABSPATH . '/templates/woocommerce/';
			// Look within passed path within the theme - this is priority
			$template = locate_template( array( $template_path . $template_name, $template_name ) );

			// Modification: Get the template from this plugin, if it exists
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			// Use default template
			if ( ! $template ) {
				$template = $_template;
			}

			// Return what we found
			return $template;
		}

		/**
		 * Check Woocommerce enable tax.
		 *
		 * @since 2.0
		 *
		 * @param $enable
		 *
		 * @return bool
		 */
		public function tax_enable( $enable ) {
			if ( get_option( 'woocommerce_tax_display_shop' ) === 'incl' ) {
				return true;
			}

			return false;
		}

		/**
		 * Trigger add room to cart hotel to update Woocommerce cart.
		 *
		 * @since 2.0
		 *
		 * @param $cart_item_id
		 * @param $params
		 *
		 * @return string
		 */
		public function hotel_add_to_cart( $cart_item_id, $params ) {

			global $woocommerce;

			if ( ! $woocommerce || ! $woocommerce->cart ) {
				return '';
			}

			$cart_items = $woocommerce->cart->get_cart();

			$woo_cart_param = array(
				'product_id'     => $params['product_id'],
				'check_in_date'  => $params['check_in_date'],
				'check_out_date' => $params['check_out_date']
			);

			if ( isset( $params['parent_id'] ) ) {
				$woo_cart_param['parent_id'] = $params['parent_id'];
			}

			$woo_cart_param = apply_filters( 'hotel_booking_wc_cart_params', $woo_cart_param, $cart_item_id );

			$woo_cart_id = $woocommerce->cart->generate_cart_id( $woo_cart_param['product_id'], null, array(), $woo_cart_param );
			if ( array_key_exists( $woo_cart_id, $cart_items ) ) {
				$woocommerce->cart->set_quantity( $woo_cart_id, $params['quantity'] );
			} else {
				$woocommerce->cart->add_to_cart( $woo_cart_param['product_id'], $params['quantity'], null, array(), $woo_cart_param );
			}

			do_action( 'hb_wc_after_add_to_cart', $cart_item_id, $params );

			return $cart_item_id;
		}

		/**
		 * WP Hotel Booking remove cart item action.
		 *
		 * @since 2.0
		 *
		 * @param $cart_item_id
		 * @param $remove_params
		 */
		public function hotel_remove_cart_item( $cart_item_id, $remove_params ) {
			remove_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10 );
			global $woocommerce;

			$woo_cart_items = $woocommerce->cart->cart_contents;

			$woo_cart_id = $woocommerce->cart->generate_cart_id( $remove_params['product_id'], null, array(), $remove_params );

			if ( array_key_exists( $woo_cart_id, $woo_cart_items ) ) {
				$woocommerce->cart->remove_cart_item( $woo_cart_id );
			}

			if ( ! isset( $remove_params['parent_id'] ) ) {
				foreach ( $woo_cart_items as $cart_id => $cart_item ) {
					if ( ! isset( $cart_item['check_in_date'] ) || ! isset( $cart_item['check_out_date'] ) || ! isset( $cart_item['parent_id'] ) ) {
						continue;
					}
					if ( $cart_item['parent_id'] === $cart_item_id ) {
						$woocommerce->cart->remove_cart_item( $cart_id );
					}
				}
			}

			add_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 2 );
			do_action( 'hb_wc_remove_cart_room_item', $cart_item_id );
		}

		/**
		 * Remove cart item in hotel booking cart.
		 *
		 * @since 2.0
		 *
		 * @param $cart_item_key
		 * @param $cart
		 */
		public function woocommerce_remove_cart_item( $cart_item_key, $cart ) {
			remove_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10 );
			if ( $cart_item = $cart->get_cart_item( $cart_item_key ) ) {
				if ( ! isset( $cart_item['check_in_date'] ) && ! isset( $cart_item['check_out_date'] ) ) {
					return;
				}

				add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
				$hotel_cart_param = array(
					'product_id'     => $cart_item['product_id'],
					'check_in_date'  => $cart_item['check_in_date'],
					'check_out_date' => $cart_item['check_out_date']
				);

				if ( isset( $cart_item['parent_id'] ) ) {
					$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
				}

				$wphb_cart     = WPHB_Cart::instance();
				$hotel_cart_id = $wphb_cart->generate_cart_id( $hotel_cart_param );

				$hotel_cart_contents = $wphb_cart->cart_contents;

				if ( array_key_exists( $hotel_cart_id, $hotel_cart_contents ) ) {
					$wphb_cart->remove_cart_item( $hotel_cart_id );
				}
			}
		}

		/**
		 * Woocommerce update cart action.
		 *
		 * @since 2.0
		 *
		 * @param $return
		 * @param $cart_item_key
		 * @param $values
		 * @param $quantity
		 *
		 * @return mixed
		 */
		public function woocommerce_update_cart( $return, $cart_item_key, $values, $quantity ) {
			global $woocommerce;
			if ( $cart_item = $woocommerce->cart->get_cart_item( $cart_item_key ) ) {
				if ( ! isset( $cart_item['check_in_date'] ) && ! isset( $cart_item['check_out_date'] ) ) {
					return $return;
				}

				$cart = WPHB_Cart::instance();

				// param render hotel cart id
				$hotel_cart_param = array(
					'product_id'     => $cart_item['product_id'],
					'check_in_date'  => $cart_item['check_in_date'],
					'check_out_date' => $cart_item['check_out_date']
				);

				if ( isset( $cart_item['parent_id'] ) ) {
					$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
				}

				// hotel cart id
				$hotel_cart_id = $cart->generate_cart_id( $hotel_cart_param );
				$cart->update_cart_item( $hotel_cart_id, $quantity );
			}

			do_action( 'hb_wc_update_cart', $return, $cart_item_key, $values, $quantity );

			return apply_filters( 'hb_wc_update_cart_return', $return, $cart_item_key, $values, $quantity );
		}

		/**
		 * Woocommerce restore cart item.
		 *
		 * @since 2.0
		 *
		 * @param $cart_item_id
		 * @param $cart
		 *
		 * @return bool
		 */
		public function woocommerce_restore_cart_item( $cart_item_id, $cart ) {
			if ( ! $cart_item = $cart->get_cart_item( $cart_item_id ) ) {
				return false;
			}

			if ( ! isset( $cart_item['check_in_date'] ) || ! isset( $cart_item['check_out_date'] ) ) {
				return false;
			}

			do_action( 'hb_wc_restore_cart_item', $cart_item_id, $cart );

			// param render hotel cart id
			$hotel_cart_param = array(
				'product_id'     => $cart_item['product_id'],
				'check_in_date'  => $cart_item['check_in_date'],
				'check_out_date' => $cart_item['check_out_date']
			);

			if ( isset( $cart_item['parent_id'] ) ) {
				$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
			}

			$cart = WPHB_Cart::instance();
			$cart->add_to_cart( $cart_item['product_id'], $hotel_cart_param, $cart_item['quantity'] );

			do_action( 'hb_wc_restored_cart_item', $cart_item_id, $cart );

			return true;
		}

		/**
		 * Get cart item form session.
		 *
		 * @since 2.0
		 *
		 * @param $session_data
		 * @param $values
		 * @param $key
		 *
		 * @return mixed
		 */
		public function get_cart_item_from_session( $session_data, $values, $key ) {
			$session_data['data']->set_props( $values );

			return $session_data;
		}

		/**
		 * Woocommerce cart extra package item class.
		 *
		 * @since 2.0
		 *
		 * @param $class
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function woocommerce_cart_package_item_class( $class, $cart_item, $cart_item_key ) {

			$class = array(
				$class
			);

			if ( ! isset( $cart_item['check_in_date'] ) || ! isset( $cart_item['check_in_date'] ) ) {
				return implode( ' ', $class );
			}

			if ( ! isset( $cart_item['parent_id'] ) ) {
				$class[] = 'hb_wc_cart_room_item';
			} else {
				$class[] = 'hb_wc_cart_package_item';
			}

			return implode( ' ', $class );
		}

		/**
		 * Order Woocommerce changed status.
		 *
		 * @since 2.0
		 *
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 */
		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			if ( $booking_id = hb_get_post_id_meta( '_hb_woo_order_id', $order_id ) ) {
				if ( in_array( $new_status, array( 'completed', 'pending', 'processing', 'cancelled' ) ) ) {
					WPHB_Booking::instance( $booking_id )->update_status( $new_status );
				} else {
					WPHB_Booking::instance( $booking_id )->update_status( 'pending' );
				}
			}
		}

		/**
		 * WP Hotel Booking cart url.
		 *
		 * @since 2.0
		 *
		 * @param $url
		 *
		 * @return mixed
		 */
		public function hotel_cart_url( $url ) {
			global $woocommerce;
			if ( ! $woocommerce->cart ) {
				return $url;
			}

			$url = wc_get_cart_url() ? wc_get_cart_url() : $url;

			return $url;
		}

		/**
		 * WP Hotel Booking checkout url.
		 *
		 * @since 2.0
		 *
		 * @param $url
		 *
		 * @return mixed
		 */
		public function hotel_checkout_url( $url ) {
			global $woocommerce;
			if ( ! $woocommerce->cart ) {
				return $url;
			}
			$url = wc_get_checkout_url() ? wc_get_checkout_url() : $url;

			return $url;
		}

		/**
		 * Woocommerce product class.
		 *
		 * @since 2.0
		 *
		 * @param $classname
		 * @param $product_type
		 * @param $post_type
		 * @param $product_id
		 *
		 * @return string
		 */
		public function product_class( $classname, $product_type, $post_type, $product_id ) {
			if ( 'hb_room' == get_post_type( $product_id ) ) {
				$classname = 'WPHB_WC_Product_Room';
			} else if ( 'hb_extra_room' == get_post_type( $product_id ) ) {
				$classname = 'WPHB_WC_Product_Package';
			}

			return $classname;
		}

		/**
		 * Parse request.
		 *
		 * @since 2.0
		 *
		 * @return bool|mixed
		 */
		private function _parse_request() {
			$segments = parse_url( hb_get_request( '_wp_http_referer' ) );
			$request  = false;
			if ( ! empty( $segments['query'] ) ) {
				parse_str( $segments['query'], $params );
				if ( ! empty( $params['hotel-booking-params'] ) ) {
					$param_str = base64_decode( $params['hotel-booking-params'] );
					$request   = unserialize( $param_str );
				}
			}

			return $request;
		}

		/**
		 * Add product class param.
		 *
		 * @since 2.0
		 *
		 * @param $cart_item
		 * @param $cart_id
		 *
		 * @return mixed
		 */
		public function add_cart_item( $cart_item, $cart_id ) {
			$post_type = get_post_type( $cart_item['data']->get_id() );

			if ( in_array( $post_type, array( 'hb_room', 'hb_extra_room' ) ) ) {
				$cart_item['data']->set_props(
					array(
						'product_id'     => $cart_item['product_id'],
						'check_in_date'  => $cart_item['check_in_date'],
						'check_out_date' => $cart_item['check_out_date'],
						'woo_cart_id'    => $cart_id
					)
				);
				if ( $post_type === 'hb_extra_room' ) {
					$cart_item['data']->set_parent_id( $cart_item['parent_id'] );
				}
			}

			return $cart_item;
		}

		/**
		 * Get Woocommerce currency setting.
		 *
		 * @since 2.0
		 *
		 * @param $currency
		 *
		 * @return string
		 */
		public function woocommerce_currency( $currency ) {
			return get_woocommerce_currency();
		}

		/**
		 * Get Woocommerce setting currency symbol.
		 *
		 * @since 2.0
		 *
		 * @param $symbol
		 * @param $currency
		 *
		 * @return string
		 */
		public function woocommerce_currency_symbol( $symbol, $currency ) {
			return get_woocommerce_currency_symbol( $currency );
		}

		/**
		 * Get price with Woocommerce format.
		 *
		 * @since 2.0
		 *
		 * @param $price_format
		 * @param $price
		 * @param $with_currency
		 *
		 * @return string
		 */
		public function woocommerce_price_format( $price_format, $price, $with_currency ) {
			return wc_price( $price );
		}

		/**
		 * Room price tax.
		 *
		 * @since 2.0
		 *
		 * @param $tax_price
		 * @param $room
		 *
		 * @return float|string
		 */
		public function room_price_tax( $tax_price, $room ) {
			remove_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10 );

			add_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );

			if ( ! function_exists( 'wc_get_price_including_tax' ) ) {
				// woo get price
				$product        = new WC_Product( $room->post->ID );
				$price_incl_tax = $product->get_price_including_tax( $room->get_data( 'quantity' ), $room->amount_singular_exclude_tax );
				$price_excl_tax = $product->get_price_excluding_tax( $room->get_data( 'quantity' ), $room->amount_singular_exclude_tax );
			} else {
				$price_incl_tax = wc_get_price_including_tax( $room, array(
					'qty'   => $room->get_data( 'quantity' ),
					'price' => $room->amount_singular_exclude_tax
				) );
				$price_excl_tax = wc_get_price_including_tax( $room, array(
					'qty'   => $room->get_data( 'quantity' ),
					'price' => $room->amount_singular_exclude_tax
				) );
			}


			return $price_incl_tax - $price_excl_tax;
		}

		/**
		 * Extra package price tax.
		 *
		 * @since 2.0
		 *
		 * @param $tax_price
		 * @param $price
		 * @param $package
		 *
		 * @return float|string
		 */
		public function packages_regular_price_tax( $tax_price, $price, $package ) {

			if ( ! function_exists( 'wc_get_price_including_tax' ) ) {
				$product = wc_get_product( $package->ID );
				$price   = $package->amount_singular_exclude_tax();

				$price_incl_tax = $product->get_price_including_tax( 1, $price );
				$price_excl_tax = $product->get_price_excluding_tax( 1, $price );
			} else {
				$price_incl_tax = wc_get_price_including_tax( $package, array(
					'qty'   => $package->get_data( 'quantity' ),
					'price' => $package->amount_singular_exclude_tax
				) );
				$price_excl_tax = wc_get_price_including_tax( $package, array(
					'qty'   => $package->get_data( 'quantity' ),
					'price' => $package->amount_singular_exclude_tax
				) );
			}

			return $price_incl_tax - $price_excl_tax;
		}

		/**
		 * Price include tax.
		 *
		 * @since 2.0
		 *
		 * @param $tax
		 * @param $cart
		 *
		 * @return bool
		 */
		public function price_including_tax( $tax, $cart ) {
			if ( ! $cart ) {
				return $tax;
			}
			if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
				$tax = true;
			}

			return $tax;
		}

		/**
		 * Cart item total amount.
		 *
		 * @since 2.0
		 *
		 * @param $amount
		 * @param $cart_id
		 * @param $cart_item
		 * @param $product
		 *
		 * @return mixed
		 */
		public function cart_item_total_amount( $amount, $cart_id, $cart_item, $product ) {
			return $amount;
		}

		/**
		 * WP Hotel Booking cart item amount singular.
		 *
		 * @since 2.0
		 *
		 * @param $amount
		 * @param $cart_id
		 * @param $cart_item
		 * @param $product
		 *
		 * @return string
		 */
		public function hotel_booking_cart_item_amount_singular( $amount, $cart_id, $cart_item, $product ) {
			if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
				// woo get price
				if ( get_post_type( $cart_item->product_id ) === 'hb_room' ) {
					$woo_product = wc_get_product( $cart_item->product_id );
					$price       = $product->get_total( $cart_item->check_in_date, $product->check_out_date, $cart_item->quantity, false, false );

					$amount = $woo_product->get_price_including_tax( $price, $product->quantity );
				}
			}

			return $amount;
		}

		/**
		 * Cart tax.
		 *
		 * @since 2.0
		 *
		 * @return string
		 */
		public function cart_tax_display() {
			global $woocommerce;

			return wc_price( $woocommerce->cart->get_taxes_total() );
		}

		/**
		 * Cart result total.
		 *
		 * @since 2.0
		 *
		 * @return string
		 */
		public function cart_total_result_display() {
			global $woocommerce;

			return wc_price( $woocommerce->cart->total );
		}

		/**
		 * Redirect cart and checkout WP Hotel Booking pages to Woocommerce pages.
		 *
		 * @since 2.0
		 */
		public function template_redirect() {
			global $post;
			if ( ! $post ) {
				return;
			}
			if ( $post->ID == hb_get_page_id( 'cart' ) ) {
				wp_redirect( wc_get_cart_url() );
				exit();
			} else if ( $post->ID == hb_get_page_id( 'checkout' ) ) {
				wp_redirect( wc_get_checkout_url() );
				exit();
			}
		}

		/**
		 * Woocommerce sort rooms as product with extra packages.
		 *
		 * @since 2.0
		 */
		public function woocommerce_sort_rooms() {
			global $woocommerce;

			$woo_cart_contents = array();

			// cart contents items
			$cart_items = $woocommerce->cart->cart_contents;

			foreach ( $cart_items as $cart_id => $item ) {

				if ( ! isset( $item['check_in_date'] ) || ! isset( $item['check_out_date'] ) ) {
					$woo_cart_contents[ $cart_id ] = $item;
					continue;
				}

				if ( ! isset( $item['parent_id'] ) ) {
					$woo_cart_contents[ $cart_id ] = $item;

					$param = array(
						'product_id'     => $item['product_id'],
						'check_in_date'  => $item['check_in_date'],
						'check_out_date' => $item['check_out_date'],
					);

					$cart      = WPHB_Cart::instance();
					$parent_id = $cart->generate_cart_id( $param );

					foreach ( $cart_items as $cart_package_id => $package ) {
						if ( ! isset( $package['parent_id'] ) || ! isset( $package['check_in_date'] ) || ! isset( $package['check_out_date'] ) ) {
							continue;
						}

						if ( $package['parent_id'] === $parent_id ) {
							$woo_cart_contents[ $cart_package_id ] = $package;
						}
					}
				}
			}

			$woocommerce->cart->cart_contents = $woo_cart_contents;
		}

		/**
		 * Admin setting page.
		 *
		 * @since 2.0
		 */
		public function admin_settings() {
			include_once WPHB_WOO_PAYMENT_ABSPATH . 'includes/admin/views/settings.php';
		}
	}
}

new WPHB_Woocommerce();