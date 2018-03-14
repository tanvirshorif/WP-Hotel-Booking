<?php

/**
 * WP Hotel Booking admin class.
 *
 * @class       WPHB_Admin_Ajax
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Ajax' ) ) {
	/**
	 * Class WPHB_Admin_Ajax.
	 */
	class WPHB_Admin_Ajax {

		/**
		 * Init.
		 */
		public static function init() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$actions = array(
				'extra_panel',
				'admin_booking',
				'load_room_ajax',
				'admin_load_pricing_calendar',
				'admin_dismiss_notice',
				'admin_rating_plugin',
				'admin_force_update_db'
			);

			foreach ( $actions as $action ) {
				add_action( "wp_ajax_wphb_{$action}", array( __CLASS__, $action ) );
			}
		}

		/**
		 * Handle extra panel actions.
		 *
		 * @return bool|int|WP_Error
		 */
		public static function extra_panel() {
			check_ajax_referer( 'wphb_admin_extra_nonce', 'nonce' );

			$args = wp_parse_args( $_REQUEST, array( 'action' => '', 'type' => '' ) );

			// curd
			$curd = new WPHB_Extra_CURD();
			// response
			$result = false;

			switch ( $args['type'] ) {
				case 'new-extra':
					$extra = json_decode( wp_unslash( $args['extra'] ), true );
					// create new extra
					$data = $curd->create( $extra );

					$result = self::_handle_response( $data, __( 'New Extra successful', 'wp-hotel-booking' ) );
					break;
				case 'update-extra':
					$extra = json_decode( wp_unslash( $args['extra'] ), true );
					// update extra
					$data   = $curd->update( $extra );
					$result = self::_handle_response( $data, __( 'Update Extra successful', 'wp-hotel-booking' ) );
					break;
				case 'delete-extra':
					$id = $args['extra_id'] ? $args['extra_id'] : '';

					if ( $id && get_post_type( $id ) == 'hb_extra_room' ) {
						// delete extra
						wp_delete_post( $id, true );
						$result = self::_handle_response( true, __( 'Delete Extra successful', 'wp-hotel-booking' ) );
					}
					break;
				case 'update-list-extra':
					$list_extra = json_decode( wp_unslash( $args['listExtra'] ), true );

					$data = true;
					if ( is_array( $list_extra ) && $list_extra ) {
						foreach ( $list_extra as $extra ) {
							$update = $curd->update( $extra );
							if ( ! $update || is_wp_error( $update ) ) {
								$data = false;
								break;
							}
						}
					}
					$result = self::_handle_response( $data, __( 'Update successful', 'wp-hotel-booking' ) );
					break;
				default:
					break;
			}

			wp_send_json_success( $result );

			return false;
		}

		/**
		 * @param $data
		 * @param string $success_message
		 *
		 * @return array
		 */
		private static function _handle_response( $data, $success_message = '' ) {
			return array(
				'data'    => $data,
				'message' => ( is_wp_error( $data ) || ! $data ) ? __( 'Something wrong', 'wp-hotel-booking' ) : $success_message
			);
		}

		/**
		 * Handle admin booking actions.
		 *
		 * @return bool|int|WP_Error
		 */
		public static function admin_booking() {
			check_ajax_referer( 'wphb_admin_booking_nonce', 'nonce' );

			$args = wp_parse_args( $_REQUEST, array( 'booking_id' => '', 'action' => '', 'type' => '' ) );

			if ( ! $args['booking_id'] ) {
				return false;
			}
			$booking_id = $args['booking_id'];

			// curd
			$curd = new WPHB_Booking_CURD();
			// response
			$result = false;

			switch ( $args['type'] ) {
				case 'check-room-available':
					$item = json_decode( wp_unslash( $args['item'] ), true );
					if ( ! $item ) {
						break;
					}
					// get number room available
					$result = $curd->check_room_available( $booking_id, $item );
					break;

				case 'add-item':
					$item = json_decode( wp_unslash( $args['item'] ), true );
					if ( ! $item ) {
						break;
					}
					// add items to booking
					$result = $curd->add_items( $booking_id, $item );
					break;
				case 'remove-item':
					$booking_item_id = $args['booking_item_id'] ? $args['booking_item_id'] : 0;
					if ( ! $booking_item_id ) {
						break;
					}
					$result = $curd->remove_booking_item( $booking_item_id );
					break;
				default:
					break;
			}

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}

			wp_send_json_success( $result );

			return false;
		}

		/**
		 * Ajax load room in booking details.
		 *
		 * @since 2.0
		 */
		public static function load_room_ajax() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) || ! isset( $_POST['room'] ) ) {
				return;
			}

			$title = sanitize_text_field( $_POST['room'] );
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT room.ID AS ID, room.post_title AS post_title FROM $wpdb->posts AS room
				WHERE
					room.post_title LIKE %s
					AND room.post_type = %s
					AND room.post_status = %s
					GROUP BY room.post_name
			", '%' . $wpdb->esc_like( $title ) . '%', 'hb_room', 'publish' );

			$rooms = $wpdb->get_results( $sql );
			wp_send_json( $rooms );
			die();
		}

		/**
		 * Admin load pricing calendar.
		 *
		 * @since 2.0
		 */
		public static function admin_load_pricing_calendar() {
			check_ajax_referer( 'hb_booking_nonce_action', 'nonce' );

			if ( ! isset( $_POST['room_id'] ) ) {
				wp_send_json( array(
					'status'  => false,
					'message' => __( 'Room is not exists.', 'wp-hotel-booking' )
				) );
			}

			$room_id = absint( $_POST['room_id'] );
			if ( ! isset( $_POST['month'] ) ) {
				wp_send_json( array(
					'status'  => false,
					'message' => __( 'Date is not exists.', 'wp-hotel-booking' )
				) );
			}
			$date = sanitize_text_field( $_POST['month'] );

			wp_send_json( array(
				'status'     => true,
				'events'     => hotel_booking_print_pricing_json( $room_id, date( 'm/d/Y', strtotime( $date ) ) ),
				'next'       => date( 'm/d/Y', strtotime( '+1 month', strtotime( $date ) ) ),
				'prev'       => date( 'm/d/Y', strtotime( '-1 month', strtotime( $date ) ) ),
				'month_name' => date_i18n( 'F, Y', strtotime( $date ) )
			) );
		}

		/**
		 * Dismiss remove TP Hotel Booking plugin notice.
		 *
		 * @since 2.0
		 */
		public static function admin_dismiss_notice() {
			if ( is_multisite() ) {
				update_site_option( 'wphb_notice_remove_hotel_booking', 1 );
			} else {
				update_option( 'wphb_notice_remove_hotel_booking', 1 );
			}
			wp_send_json( array(
				'status' => 'done'
			) );
		}

		/**
		 * Admin click rating plugin.
		 */
		public static function admin_rating_plugin() {
			update_option( 'wphb_request_plugin_rating', 1 );

			return true;
		}

		/**
		 * Check missing db tables.
		 *
		 * @return bool
		 */
		public static function admin_force_update_db() {
			$install = new WPHB_Install();
			$install::create_tables();

			return true;
		}
	}
}

add_action( 'init', array( 'WPHB_Admin_Ajax', 'init' ) );