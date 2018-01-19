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
				'users'    => WPHB_User::get_users_info(),
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
	}
}

new WPHB_Booking_CURD();