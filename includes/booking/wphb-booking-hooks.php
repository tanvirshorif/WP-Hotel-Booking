<?php

/**
 * WP Hotel Booking Booking Hooks
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Hooks
 * @category    Hooks
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

add_action( 'restrict_manage_posts', 'hb_booking_restrict_manage_posts' );


if ( ! function_exists( 'hb_booking_restrict_manage_posts' ) ) {
	/**
	 * Create booking date drop down filter.
	 */
	function hb_booking_restrict_manage_posts() {
		$type = 'post';
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}

		// only add filter to hb_booking post type
		if ( 'hb_booking' == $type ) {
			//change this to the list of values you want to show
			$from           = hb_get_request( 'date-from' );
			$from_timestamp = hb_get_request( 'date-from-timestamp' );
			$to             = hb_get_request( 'date-to' );
			$to_timestamp   = hb_get_request( 'date-to-timestamp' );
			$filter_type    = hb_get_request( 'filter-type' );

			$filter_types = apply_filters(
				'hb_booking_filter_types',
				array(
					'booking-date'   => __( 'Booking date', 'wp-hotel-booking' ),
					'check-in-date'  => __( 'Check-in date', 'wp-hotel-booking' ),
					'check-out-date' => __( 'Check-out date', 'wp-hotel-booking' )
				)
			);

			?>
            <span><?php _e( 'Date Range', 'wp-hotel-booking' ); ?></span>
            <input type="text" id="hb-booking-date-from" class="hb-date-field" value="<?php echo esc_attr( $from ); ?>"
                   name="date-from" readonly placeholder="<?php _e( 'From', 'wp-hotel-booking' ); ?>"/>
            <input type="hidden" value="<?php echo esc_attr( $from_timestamp ); ?>" name="date-from-timestamp"/>
            <input type="text" id="hb-booking-date-to" class="hb-date-field" value="<?php echo esc_attr( $to ); ?>"
                   name="date-to" readonly placeholder="<?php _e( 'To', 'wp-hotel-booking' ); ?>"/>
            <input type="hidden" value="<?php echo esc_attr( $to_timestamp ); ?>" name="date-to-timestamp"/>
            <select name="filter-type">
                <option value=""><?php _e( 'Filter By', 'wp-hotel-booking' ); ?></option>
				<?php foreach ( $filter_types as $slug => $text ) { ?>
                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug == $filter_type ); ?>><?php echo esc_html( $text ); ?></option>
				<?php } ?>
            </select>
			<?php
		}
	}
}


add_action( 'hotel_booking_create_booking', 'hb_schedule_cancel_booking', 10, 1 );
add_action( 'hb_booking_status_changed', 'hb_schedule_cancel_booking', 10, 1 );

if ( ! function_exists( 'hb_schedule_cancel_booking' ) ) {
	/**
	 * Schedule cancel pending booking.
	 *
	 * @param $booking_id
	 */
	function hb_schedule_cancel_booking( $booking_id ) {
		$booking_status = get_post_status( $booking_id );
		if ( $booking_status === 'hb-pending' ) {
			wp_clear_scheduled_hook( 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
			$time = hb_settings()->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
			wp_schedule_single_event( time() + $time, 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
		}
	}
}


add_action( 'hotel_booking_change_cancel_booking_status', 'hb_cancel_booking', 10, 1 );

if ( ! function_exists( 'hb_cancel_booking' ) ) {
	/**
	 * Cancel booking when expired.
	 *
	 * @param $booking_id
	 */
	function hb_cancel_booking( $booking_id ) {
		$booking_status = get_post_status( $booking_id );
		if ( $booking_status === 'hb-pending' ) {
			wp_update_post( array(
				'ID'          => $booking_id,
				'post_status' => 'hb-cancelled'
			) );
		}
	}
}

add_action( 'hb_place_order', 'hb_send_place_booking_email', 10, 2 );

if ( ! function_exists( 'hb_send_place_booking_email' ) ) {
	/**
	 * Send email for customer and admin when customer places booking.
	 *
	 * @param array $return
	 * @param null $booking_id
	 *
	 * @return bool
	 */
	function hb_send_place_booking_email( $return = array(), $booking_id = null ) {
		if ( ! $booking_id || ! isset( $return['result'] ) || $return['result'] !== 'success' ) {
			return false;
		}

		$booking  = WPHB_Booking::instance( $booking_id );
		$settings = hb_settings();

		// send customer email
		hb_send_customer_booking_email( $booking );

		// send admin email
		if ( $settings->get( 'email_new_booking_enable' ) ) {
			hb_send_admin_booking_email( $booking );
		}

		return true;
	}
}

add_action( 'hb_booking_status_changed', 'hb_send_booking_completed_email', 10, 3 );

if ( ! function_exists( 'hb_send_booking_completed_email' ) ) {
	/**
	 * Send email for customer and admin when booking completed.
	 *
	 * @param null $booking_id
	 * @param null $old_status
	 * @param null $new_status
	 *
	 * @return bool
	 */
	function hb_send_booking_completed_email( $booking_id = null, $old_status = null, $new_status = null ) {
		if ( ! $booking_id || ( $new_status && $new_status !== 'completed' ) ) {
			return false;
		}

		$booking  = WPHB_Booking::instance( $booking_id );
		$settings = hb_settings();

		// send customer email
		hb_send_customer_booking_email( $booking, 'booking_completed' );

		// send admin email
		if ( $settings->get( 'email_booking_completed_enable' ) ) {
			hb_send_admin_booking_email( $booking, 'booking_completed' );
		}

		return true;
	}
}
