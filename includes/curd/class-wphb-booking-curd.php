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
		/**
		 * @param object $booking
		 *
		 * @return array|bool
		 * @throws Exception
		 */
		public function load( &$booking ) {
			if ( ! $booking->ID || get_post_type( $booking->ID ) != WPHB_Booking_CPT ) {
				return false;
			}

			// WPHB_Booking
			$booking = WPHB_Booking::instance( $booking->ID );

			$id   = $booking->id;
			$data = array(
				'booking'  => array(
					'id'                      => $id,
					'status'                  => get_post_status( $id ),
					'note'                    => get_the_content( $id ),
					'advance_payment'         => get_post_meta( $id, '_hb_advance_payment', true ),
					'advance_payment_setting' => get_post_meta( $id, '_hb_advance_payment_setting', true ),
					'currency'                => hb_get_currency_symbol( $booking->currency ),
					'method'                  => get_post_meta( $id, '_hb_method', true ),
					'method_title'            => get_post_meta( $id, '_hb_method_title', true ),
					'sub_total'               => hb_booking_subtotal( $id ),
					'tax'                     => hb_booking_tax_total( $id ),
					'total'                   => hb_booking_total( $id ),
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
				'rooms'    => hb_get_booking_items( $id, 'line_item', null, true ),
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

		public function delete( &$booking_id ) {
			if ( ! $booking_id || get_post_type( $booking_id ) != WPHB_Booking_CPT ) {
				return false;
			}

			global $wpdb;

			$sql = $wpdb->prepare(
				"SELECT items.order_item_id FROM $wpdb->hotel_booking_order_items AS items
						  WHERE items.order_id = %d", $booking_id );
			// get booking items id
			$booking_items_id = $wpdb->get_results( $sql, ARRAY_A );


			// delete booking items by booking id
			$wpdb->delete( $wpdb->hotel_booking_order_items, array( 'order_id' => $booking_id ), array( '%d' ) );

			// delete booking items meta
			foreach ( $booking_items_id as $item ) {
				$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array( 'hotel_booking_order_item_id' => $item['order_item_id'] ), array( '%d' ) );
			}

			do_action( 'wphb_before_delete_booking', $booking_id );

			return true;
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

			$room_curd = new WPHB_Room_CURD();
			$qty       = $room_curd::get_room_available( $room_id, $args );

			if ( $qty && ! is_wp_error( $qty ) ) {
				$item['available'] = $qty;
				$item['extra']     = $room_curd::get_room_extra( $room_id );
			} else {
				wp_send_json( array(
					'status'  => false,
					'message' => $qty->get_error_message()
				) );
			}

			return $item;
		}

		/**
		 * Add room and extra item to booking.
		 *
		 * @param $booking_id
		 * @param $item
		 *
		 * @return array|bool
		 */
		public function add_items( $booking_id, $item ) {

			if ( empty( $item ) || ! $item['id'] ) {
				return false;
			}

			$room_id        = $item['id'];
			$room_qty       = $item['qty'];
			$check_in_date  = strtotime( $item['check_in'] );
			$check_out_date = strtotime( $item['check_out'] );

			// add room booking item
			$booking_room_item_id = hb_add_booking_item( $booking_id, array(
				'order_item_name' => get_the_title( $room_id ),
				'order_item_type' => 'line_item'
			) );

			// update booking room item meta
			hb_update_booking_item_meta( $booking_room_item_id, 'product_id', $room_id );
			hb_update_booking_item_meta( $booking_room_item_id, 'qty', $room_qty );
			hb_update_booking_item_meta( $booking_room_item_id, 'check_in_date', $check_in_date );
			hb_update_booking_item_meta( $booking_room_item_id, 'check_out_date', $check_out_date );

			$product_class = hotel_booking_get_product_class( $room_id, array(
				'check_in_date'  => $check_in_date,
				'check_out_date' => $check_out_date,
				'quantity'       => $room_qty,
				'order_item_id'  => $booking_room_item_id
			) );
			$subtotal      = $product_class->amount_exclude_tax();
			$total         = $product_class->amount_include_tax();
			hb_update_booking_item_meta( $booking_room_item_id, 'subtotal', $subtotal );
			hb_update_booking_item_meta( $booking_room_item_id, 'total', $total );
			hb_update_booking_item_meta( $booking_room_item_id, 'tax_total', $total - $subtotal );

			$extra_data = array();
			// add extra booking item
			foreach ( $item['extra'] as $extra_id => $extra ) {
				if ( $extra['selected'] ) {
					$booking_extra_item_id = hb_add_booking_item( $booking_id, array(
						'order_item_name'   => get_the_title( $extra_id ),
						'order_item_type'   => 'sub_item',
						'order_item_parent' => $booking_room_item_id
					) );

					// update booking extra item meta
					hb_update_booking_item_meta( $booking_extra_item_id, 'product_id', $extra_id );
					hb_update_booking_item_meta( $booking_extra_item_id, 'qty', $extra['qty'] );
					hb_update_booking_item_meta( $booking_extra_item_id, 'check_in_date', $check_in_date );
					hb_update_booking_item_meta( $booking_extra_item_id, 'check_out_date', $check_out_date );

					$product_class = hotel_booking_get_product_class( $extra_id, array(
						'check_in_date'  => $check_in_date,
						'check_out_date' => $check_out_date,
						'quantity'       => $extra['qty'],
						'order_item_id'  => $booking_extra_item_id
					) );
					$subtotal      = $product_class->amount_exclude_tax();
					$total         = $product_class->amount_include_tax();
					hb_update_booking_item_meta( $booking_extra_item_id, 'subtotal', $subtotal );
					hb_update_booking_item_meta( $booking_extra_item_id, 'total', $total );
					hb_update_booking_item_meta( $booking_extra_item_id, 'tax_total', $total - $subtotal );

					$extra_data[ $extra_id ] = array(
						'order_id'          => $booking_id,
						'order_item_id'     => $booking_extra_item_id,
						'order_item_name'   => get_the_title( $extra_id ),
						'order_item_parent' => $booking_room_item_id,
						'order_item_type'   => 'sub_item',
						'edit_link'         => get_edit_post_link( hb_get_booking_item_meta( $booking_extra_item_id, 'product_id', true ) ),
						'check_in_date'     => date_i18n( hb_get_date_format(), $check_in_date ),
						'check_out_date'    => date_i18n( hb_get_date_format(), $check_out_date ),
						'night'             => hb_count_nights_two_dates( $check_out_date, $check_in_date ),
						'qty'               => hb_get_booking_item_meta( $booking_extra_item_id, 'qty', true ),
						'price'             => hb_get_booking_item_meta( $booking_extra_item_id, 'subtotal', true ),
					);
				}
			}

			return array(
				'order_id'          => $booking_id,
				'order_item_id'     => $booking_room_item_id,
				'order_item_name'   => get_the_title( $room_id ),
				'order_item_parent' => null,
				'order_item_type'   => 'line_item',
				'edit_link'         => get_edit_post_link( hb_get_booking_item_meta( $booking_room_item_id, 'product_id', true ) ),
				'check_in_date'     => date_i18n( hb_get_date_format(), $check_in_date ),
				'check_out_date'    => date_i18n( hb_get_date_format(), $check_out_date ),
				'night'             => hb_count_nights_two_dates( $check_out_date, $check_in_date ),
				'qty'               => hb_get_booking_item_meta( $booking_room_item_id, 'qty', true ),
				'price'             => hb_get_booking_item_meta( $booking_room_item_id, 'subtotal', true ),
				'extra'             => $extra_data,
			);
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

			// delete room booking item
			$wpdb->delete( $wpdb->hotel_booking_order_items, array( 'order_item_id' => $booking_item_id ), array( '%d' ) );
			$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array( 'hotel_booking_order_item_id' => $booking_item_id ), array( '%d' ) );

			// delete extra booking item
			$sql = $wpdb->prepare( "
                SELECT booking_item.order_item_id FROM $wpdb->hotel_booking_order_items as booking_item
	                WHERE booking_item.order_item_type = %s 
	                AND     booking_item.order_item_parent = %d",
				'sub_item', $booking_item_id );

			$extra = $wpdb->get_results( $sql, ARRAY_A );

			if ( $extra ) {
				foreach ( $extra as $key => $_extra ) {
					$wpdb->delete( $wpdb->hotel_booking_order_items, array( 'order_item_id' => $_extra['order_item_id'] ), array( '%d' ) );
					$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array( 'hotel_booking_order_item_id' => $_extra['order_item_id'] ), array( '%d' ) );
				}
			}

			do_action( 'hotel_booking_remove_booking_item', $booking_item_id );

			return true;
		}
	}
}

new WPHB_Booking_CURD();