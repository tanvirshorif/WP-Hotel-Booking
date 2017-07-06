<?php

/**
 * WP Hotel Booking Coupon Ajax class.
 *
 * @class       WPHB_Coupon_Ajax
 * @version     2.0
 * @package     WP_Hotel_Booking_Coupon/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Coupon_Ajax' ) ) {

	/**
	 * Class WPHB_Coupon_Ajax.
	 *
	 * @since 2.0
	 */
	class WPHB_Coupon_Ajax {

		/**
		 * WPHB_Coupon_Ajax constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$actions = array(
				'apply_coupon'          => true,
				'remove_coupon'         => true,
				'load_coupon_ajax'      => false,
				'add_booking_coupon'    => false,
				'remove_booking_coupon' => false,
			);

			foreach ( $actions as $action => $priv ) {
				add_action( "wp_ajax_wphb_coupon_{$action}", array( __CLASS__, $action ) );
				if ( $priv ) {
					add_action( "wp_ajax_nopriv_wphb_coupon_{$action}", array( __CLASS__, $action ) );
				}
			}
		}

		/**
		 * Apply coupon.
		 *
		 * @since 2.0
		 */
		public static function apply_coupon() {
			! session_id() && session_start();
			$code = hb_get_request( 'code' );
			ob_start();
			$today  = strtotime( date( 'm/d/Y' ) );
			$coupon = WPHB_Coupon::instance()->get_coupons_active( $today, $code );

			$output   = ob_get_clean();
			$response = array();
			if ( $coupon ) {
				$coupon   = WPHB_Coupon::instance( $coupon );
				$response = $coupon->validate();
				if ( $response['is_valid'] ) {
					$response['result'] = 'success';
					$response['type']   = get_post_meta( $coupon->ID, '_hb_coupon_discount_type', true );
					$response['value']  = get_post_meta( $coupon->ID, '_hb_coupon_discount_value', true );
					if ( ! session_id() ) {
						session_start();
					}
					// set session
					$cart = WPHB_Cart::instance();
					$cart->set_customer( 'coupon', $coupon->post->ID );
					hb_add_message( __( 'Coupon code applied', 'wphb-coupon' ) );
				}
			} else {
				$response['message'] = __( 'Coupon does not exist!', 'wphb-coupon' );
			}
			hb_send_json( $response );
		}


		/**
		 * Remove coupon.
		 *
		 * @since 2.0
		 */
		public static function remove_coupon() {
			! session_id() && session_start();
			// delete_transient( 'hb_user_coupon_' . session_id() );
			$cart = WPHB_Cart::instance();
			$cart->set_customer( 'coupon', null );
			hb_add_message( __( 'Coupon code removed', 'wphb-coupon' ) );
			hb_send_json(
				array(
					'result' => 'success'
				)
			);
		}

		/**
		 * Ajax load coupons code.
		 *
		 * @since 2.0
		 */
		public static function load_coupon_ajax() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) ) {
				return;
			}

			$code = sanitize_text_field( $_POST['coupon'] );
			$time = time();

			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT coupon.ID, coupon.post_title FROM $wpdb->posts AS coupon
					INNER JOIN $wpdb->postmeta AS start ON start.post_id = coupon.ID
					INNER JOIN $wpdb->postmeta AS end ON end.post_id = coupon.ID
				WHERE
					coupon.post_type = %s
					AND coupon.post_title LIKE %s
					AND coupon.post_status = %s
					AND start.meta_key = %s
					AND end.meta_key = %s
					AND ( start.meta_value <= %d AND end.meta_value >= %d )
			", 'hb_coupon', '%' . $wpdb->esc_like( $code ) . '%', 'publish', '_hb_coupon_date_from_timestamp', '_hb_coupon_date_to_timestamp', $time, $time
			);

			wp_send_json( apply_filters( 'hotel_admin_get_coupons', $wpdb->get_results( $sql ) ) );
		}

		/**
		 * Add coupon in admin booking.
		 *
		 * @since 2.0
		 */
		public static function add_booking_coupon() {
			if ( ! check_ajax_referer( 'hotel_admin_get_coupon_available', 'hotel-admin-get-coupon-available' ) || ! class_exists( 'WPHB_Coupon' ) ) {
				return;
			}

			if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['coupon_id'] ) ) {
				return;
			}

			$order_id  = absint( $_POST['order_id'] );
			$coupon_id = absint( $_POST['coupon_id'] );

			$coupon   = WPHB_Coupon::instance( $coupon_id );
			$subtotal = hb_booking_subtotal( $order_id, false ); // subtotal without coupon

			add_post_meta( $order_id, '_hb_coupon_id', $coupon_id );
			add_post_meta( $order_id, '_hb_coupon_code', $coupon->coupon_code );
			add_post_meta( $order_id, '_hb_coupon_value', $coupon->get_discount_value( $subtotal ) );

			$post = get_post( $order_id );
			ob_start();
			require_once WPHB_PLUGIN_PATH . '/includes/admin/views/metaboxes/booking-items.php';
			require_once WPHB_PLUGIN_PATH . '/includes/admin/views/metaboxes/booking-items-template-js.php';
			$html = ob_get_clean();
			wp_send_json( array(
				'status' => true,
				'html'   => $html
			) );
		}

		/**
		 * Remove coupon in admin booking.
		 *
		 * @since 2.0
		 */
		public static function remove_booking_coupon() {
			if ( ! check_ajax_referer( 'hotel-booking-confirm', 'hotel_booking_confirm' ) ) {
				return;
			}

			if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['coupon_id'] ) ) {
				return;
			}

			$order_id = absint( $_POST['order_id'] );

			delete_post_meta( $order_id, '_hb_coupon_id' );
			delete_post_meta( $order_id, '_hb_coupon_code' );
			delete_post_meta( $order_id, '_hb_coupon_value' );

			$post = get_post( $order_id );
			ob_start();
			require_once WPHB_PLUGIN_PATH . '/includes/admin/views/metaboxes/booking-items.php';
			require_once WPHB_PLUGIN_PATH . '/includes/admin/views/metaboxes/booking-items-template-js.php';
			$html = ob_get_clean();
			wp_send_json( array(
				'status' => true,
				'html'   => $html
			) );
		}
	}

}

new WPHB_Coupon_Ajax();