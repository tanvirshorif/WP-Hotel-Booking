<?php

/**
 * WP Hotel Booking Coupon functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking_Coupon/Functions
 * @category    Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'wphb_coupon_get_coupon_usage_meta_box_field' ) ) {
	/**
	 * Get coupon usage meta box field.
	 *
	 * @param $value
	 *
	 * @return int
	 */
	function hb_meta_box_field_coupon_used( $value ) {
		global $post;

		return intval( get_post_meta( $post->ID, '_hb_usage_count', true ) );
	}
}


if ( ! function_exists( 'wphb_coupon_get_coupon_date_meta_box_field' ) ) {
	/**
	 * Get coupon date meta box field.
	 *
	 * @param $value
	 *
	 * @return false|string
	 */
	function wphb_coupon_get_coupon_date_meta_box_field( $value ) {
		if ( intval( $value ) ) {
			return date( hb_get_date_format(), $value );
		}

		return $value;
	}
}