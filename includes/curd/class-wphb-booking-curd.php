<?php

/**
 * WP Hotel Booking Booking CURD class.
 *
 * @class       WPHB_Booking_CURD
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Booking_CURD' ) ) {
	/**
	 * Class WPHB_Booking_CURD.
	 *
	 * @since 2.0
	 */
	class WPHB_Booking_CURD extends WPHB_Abstract_CURD implements WPHB_Interface_CURD {

		/**
		 * @var array
		 */
		protected $_args = array();

		/**
		 * WPHB_Booking_CURD constructor.
		 */
		public function __construct() {
			$this->_args = array(
				'id'        => 0,
				'check_in'  => '',
				'check_out' => '',
				'qty'       => '',
				'available' => 0,
				'extra'     => array()
			);
		}

		/**
		 * Create booking.
		 *
		 * @param object $booking
		 */
		public function create( &$booking ) {
		}

		/**
		 * @param object $booking
		 *
		 * @return array|bool
		 */
		public function load( &$booking ) {
			if ( ! $booking->ID || get_post_type( $booking->ID ) != WPHB_Booking_CPT ) {
				return false;
			}
			$id   = $booking->ID;
			$data = array(
				'booking'  => array(
					'id'                      => $id,
					'status'                  => get_post_status( $id ),
					'note'                    => get_the_content( $id ),
					'tax'                     => get_post_meta( $id, '_hb_tax', true ),
					'advance_payment'         => get_post_meta( $id, '_hb_advance_payment', true ),
					'advance_payment_setting' => get_post_meta( $id, '_hb_advance_payment_setting', true ),
					'currency'                => get_post_meta( $id, '_hb_currency', true ),
					'method'                  => get_post_meta( $id, '_hb_method', true ),
					'method_title'            => get_post_meta( $id, '_hb_method_title', true ),
					'total'                   => get_post_meta( $id, '_hb_total', true ),
					'sub_total'               => get_post_meta( $id, '_hb_sub_total', true ),
					'woo_order_id'            => get_post_meta( $id, '_hb_woo_order_id', true )
				),
				'customer' => array(
					'id'          => get_post_meta( $id, '_hb_user_id', true ),
					'title'       => get_post_meta( $id, '_hb_customer_title', true ),
					'avatar'      => get_avatar_url( get_post_meta( $id, '_hb_user_id', true ) ),
					'link'        => get_edit_user_link( get_post_meta( $id, '_hb_user_id', true ) ),
					'user_login'  => get_post_meta( $id, '_hb_user_id', true ) ? get_userdata( get_post_meta( $id, '_hb_user_id', true ) )->user_login : __( '[Guest]', 'wp-hotel-booking' ),
					'first_name'  => get_post_meta( $id, '_hb_customer_first_name', true ),
					'last_name'   => get_post_meta( $id, '_hb_customer_last_name', true ),
					'address'     => get_post_meta( $id, '_hb_customer_address', true ),
					'city'        => get_post_meta( $id, '_hb_customer_city', true ),
					'state'       => get_post_meta( $id, '_hb_customer_state', true ),
					'postal_code' => get_post_meta( $id, '_hb_customer_postal_code', true ),
					'country'     => get_post_meta( $id, '_hb_customer_country', true ),
					'phone'       => get_post_meta( $id, '_hb_customer_phone', true ),
					'email'       => get_post_meta( $id, '_hb_customer_email', true ),
					'fax'         => get_post_meta( $id, '_hb_customer_fax', true ),
				),
				'rooms'    => hb_get_order_items( $id, 'line_item', null, true ),
				'newItem'  => array(
					'id'        => 0,
					'check_in'  => '',
					'check_out' => '',
					'qty'       => 0,
					'available' => 0,
					'extra'     => array()
				),
				'modal'    => array(
					'i18n' => array(
						'addNew'     => __( 'Add new item', 'wp-hotel-booking' ),
						'updateItem' => __( 'Update item', 'wp-hotel-booking' )
					)
				),
				'users'    => WPHB_User::get_users_info(),
				'action'   => 'wphb_admin_booking',
				'nonce'    => wp_create_nonce( 'wphb_admin_booking_nonce' )
			);

			return $data;
		}

		public function delete( &$object ) {
			// TODO: Implement delete() method.
		}

		/**
		 * Update booking.
		 *
		 * @param object $booking
		 */
		public function update( &$booking ) {
		}

		/**
		 * Get number room available.
		 *
		 * @param $booking_id
		 * @param $item
		 *
		 * @return array|bool
		 */
		public function check_room_available( $booking_id, $item ) {
			$item = wp_parse_args( $item, $this->_args );

			$room_id = absint( $item['id'] );

			// search room args
			$args = array(
				'check_in_date'  => strtotime( $item['check_in'] ),
				'check_out_date' => strtotime( $item['check_out'] ),
				'excerpt'        => array( $booking_id )
			);

			$qty = WPHB_Room_CURD::get_room_available( $room_id, $args );

			if ( $qty && ! is_wp_error( $qty ) ) {

//				// HB_Room_Extra instead of HB_Room
//				$extra_product = WPHB_Extra_Product::instance( $room_id );
//				$room_extra    = $extra_product->get_extra();
//
//				$args = apply_filters( 'hotel_booking_check_room_available', array(
//					'status'       => true,
//					'qty'          => $qty,
//					'qty_selected' => isset( $_POST['order_item_id'] ) ? hb_get_order_item_meta( $_POST['order_item_id'], 'qty', true ) : 0,
//					'product_id'   => $room_id,
//					'extra'        => $room_extra
//				) );
//				wp_send_json( $args );

				$item['available'] = $qty;

				$extra_product = WPHB_Extra_Product::instance( $room_id );
				$item['extra'] = $extra_product->get_extra();

//				echo '<pre>';
//				var_dump($extra_product->get_extra());
//				echo '</pre>';
//				die();
			} else {
				return false;
//				wp_send_json( array(
//					'status'  => false,
//					'message' => $qty->get_error_message()
//				) );
			}

//			$item['available'] = 1;

			return $item;
		}

		public function add_item( $booking_id, $item ) {

			$errors         = new WP_Error();
			$product_id     = $item['id'];
			$qty            = $item['qty'];
			$check_in_date  = strtotime( $item['check_in'] );
			$check_out_date = strtotime( $item['check_out'] );

			$order_item_id = 0;
			$return        = true;
			if ( isset( $_POST['order_item_id'] ) && $_POST['order_item_id'] ) {
				$order_item_id = absint( $_POST['order_item_id'] );
			}

			$args = array(
				'order_item_name'   => get_the_title( $product_id ),
				'order_item_type'   => isset( $_POST['order_item_type'] ) && $_POST['order_item_type'] ? sanitize_title( $_POST['order_item_type'] ) : 'line_item',
				'order_item_parent' => isset( $_POST['order_item_parent'] ) && $_POST['order_item_parent'] ? absint( $_POST['order_item_parent'] ) : null
			);
			if ( ! $order_item_id ) {
				// add new order item
				$order_item_id = hb_add_order_item( $booking_id, $args );
			} else {
				// update order item
				hb_update_order_item( $order_item_id, $args );
			}

			// update order item meta
			hb_update_order_item_meta( $order_item_id, 'check_in_date', $check_in_date );
			hb_update_order_item_meta( $order_item_id, 'check_out_date', $check_out_date );
			// product_id
			hb_update_order_item_meta( $order_item_id, 'product_id', $product_id );
			hb_update_order_item_meta( $order_item_id, 'qty', $qty );

			$params        = array(
				'check_in_date'  => $check_in_date,
				'check_out_date' => $check_out_date,
				'quantity'       => $qty,
				'order_item_id'  => $order_item_id
			);
			$product_class = hotel_booking_get_product_class( $product_id, $params );

			// update subtotal, total
			$subtotal = $product_class->amount_exclude_tax();
			$total    = $product_class->amount_include_tax();
			hb_update_order_item_meta( $order_item_id, 'subtotal', $subtotal );
			hb_update_order_item_meta( $order_item_id, 'total', $total );
			hb_update_order_item_meta( $order_item_id, 'tax_total', $total - $subtotal );
			// allow hook
			do_action( 'hotel_booking_updated_order_item', $booking_id, $order_item_id );

			return array(
				'order_id'          => $booking_id,
				'order_item_id'     => $order_item_id,
				'order_item_name'   => $args['order_item_name'],
				'order_item_parent' => $args['order_item_parent'],
				'order_item_type'   => $args['order_item_type'],
				'edit_link'         => get_edit_post_link( hb_get_order_item_meta( $order_item_id, 'product_id', true ) ),
				'check_in_date'     => date_i18n( hb_get_date_format(), $check_in_date ),
				'check_out_date'    => date_i18n( hb_get_date_format(), $check_out_date ),
				'night'             => hb_count_nights_two_dates( $check_out_date, $check_in_date ),
				'qty'               => hb_get_order_item_meta( $order_item_id, 'qty', true ),
				'price'             => hb_get_order_item_meta( $order_item_id, 'subtotal', true ),
				'extra'             => array(),
			);

//			$post = get_post( $booking_id );
//
//			ob_start();
//			hb_admin_view( 'metaboxes/booking-details', array(), true );
//			$html = ob_get_clean();
//
//			wp_send_json( array( 'status' => true, 'html' => $html ) );
		}

		/**
		 * Remove booking item.
		 *
		 * @param $booking_item_id
		 *
		 * @return bool
		 */
		public static function remove_booking_item( $booking_item_id ) {

			if ( ! $booking_item_id ) {
				return false;
			}

			global $wpdb;

			$wpdb->delete( $wpdb->hotel_booking_order_items, array( 'order_item_id' => $booking_item_id ), array( '%d' ) );
			$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array( 'hotel_booking_order_item_id' => $booking_item_id ), array( '%d' ) );

			do_action( 'hotel_booking_remove_order_item', $booking_item_id );

			return true;
		}
	}
}

new WPHB_Booking_CURD();