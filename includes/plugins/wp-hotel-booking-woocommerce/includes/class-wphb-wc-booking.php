<?php

/**
 * WP Hotel Booking Woocommerce Booking class.
 *
 * @class       WPHB_WC_Booking
 * @version     2.0
 * @package     WP_Hotel_Booking_Woocommerce/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_WC_Booking' ) ) {

	/**
	 * Class WPHB_WC_Booking.
	 *
	 * @since 2.0
	 */
	class WPHB_WC_Booking {

		/**
		 * WPHB_WC_Booking constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$settings = hb_settings();

			if ( 'yes' === $settings->get( 'wc_enable' ) ) {
				// booking change status
				add_action( 'woocommerce_order_status_changed', array( $this, 'woo_change_oder_status' ), 10, 3 );
				// booking status filter
				add_filter( 'hotel_booking_booking_total', array( $this, 'booking_status' ), 10, 3 );
			}
		}

		/**
		 * Change order status, trigger change booking status.
		 *
		 * @since 2.0
		 *
		 * @param $id
		 * @param $old_status
		 * @param $new_status
		 */
		public function woo_change_oder_status( $id, $old_status, $new_status ) {
			if ( ! $booking_id = get_post_meta( $id, 'hb_wc_booking_id', true ) ) {
				return;
			}

			$book = WPHB_Booking::instance( $booking_id );

			switch ( $new_status ) {
				case 'processing':
					$status = 'processing';
					break;
				case 'pending':
					$status = 'pending';
					break;
				case 'completed':
					$status = 'completed';
					break;
				default:
					$status = 'pending';
					break;
			}
			$book->update_status( $status );
		}

		/**
		 * Booking status.
		 *
		 * @since 2.0
		 *
		 * @param $html
		 * @param $column_name
		 * @param $post_id
		 *
		 * @return string
		 */
		public function booking_status( $html, $column_name, $post_id ) {
			if ( ! $order_id = get_post_meta( $post_id, '_hb_woo_order_id', true ) ) {
				return $html;
			}

			$status = get_post_status( $post_id );

			if ( $column_name === 'total' ) {
				// display paid
				if ( $status === 'hb-processing' ) {
					$total    = get_post_meta( $post_id, '_hb_total', true );
					$currency = get_post_meta( $post_id, '_hb_currency', true );
					$html     = wc_price( $total, array( 'currency' => $currency ) );
				}
				$html .= '<br /><small><a href="' . esc_attr( get_edit_post_link( $order_id ) ) . '">(' . __( 'Via WooCommerce', 'wphb-woocommerce' ) . ')</a></small>';
			}

			return $html;
		}

	}

}

new WPHB_WC_Booking();
