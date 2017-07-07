<?php

/**
 * Abstract WP Hotel Booking user class.
 *
 * @class       WPHB_Abstract_User
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Abstract_User' ) ) {

	/**
	 * Class WPHB_Abstract_User.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_User {

		/**
		 * @var null
		 */
		protected $user = null;

		/**
		 * @var null
		 */
		protected $id = null;

		/**
		 * WPHB_Abstract_User constructor.
		 *
		 * @since 2.0
		 *
		 * @param null $user
		 */
		public function __construct( $user = null ) {
			if ( is_numeric( $user ) && ( $user = get_user_by( 'ID', $user ) ) ) {
				$this->user = $user;
				$this->id   = $this->user->ID;
			} else if ( $user instanceof WP_User ) {
				$this->user = $user;
				$this->id   = $this->user->ID;
			}

			if ( ! $user ) {
				$current_user = wp_get_current_user();
				$this->id     = $current_user->ID;
			}
		}

		/**
		 * Get user data.
		 *
		 * @param $key
		 *
		 * @return bool|mixed
		 */
		public function __get( $key ) {
			if ( ! isset( $this->{$key} ) || ! method_exists( $this, $key ) ) {
				return get_user_meta( $this->id, '_hb_' . $key, true );
			}

			return false;
		}

		/**
		 * Get all user bookings.
		 *
		 * @since 2.0
		 *
		 * @return array|null
		 */
		public function get_bookings() {
			if ( ! $this->id ) {
				return null;
			}

			global $wpdb;

			$query = $wpdb->prepare( "
				SELECT booking.ID FROM $wpdb->posts AS booking
					INNER JOIN $wpdb->postmeta AS bookingmeta ON bookingmeta.post_ID = booking.ID AND bookingmeta.meta_key = %s
					INNER JOIN $wpdb->users AS users ON users.ID = bookingmeta.meta_value
				WHERE
					booking.post_type = %s
					AND bookingmeta.meta_value = %d
					ORDER BY booking.ID DESC
			", '_hb_user_id', 'hb_booking', $this->id );

			$results = $wpdb->get_col( $query );

			$bookings = array();

			if ( ! empty( $results ) ) {
				foreach ( $results as $k => $booking_id ) {
					$bookings[] = WPHB_Booking::instance( $booking_id );
				}
			}

			return $bookings;
		}
	}
}