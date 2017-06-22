<?php

/**
 * WP Hotel Booking shortcodes class.
 *
 * @class       WPHB_Shortcodes
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Shortcodes' ) ) {

	/**
	 * Class WPHB_Shortcodes.
	 *
	 * @since 2.0
	 */
	class WPHB_Shortcodes {

		/**
		 * Init shortcodes.
		 *
		 * @since 2.0
		 */
		public static function init() {
			$shortcodes = array(
				'hotel_booking'                   => __CLASS__ . '::hotel_booking',
				'hotel_booking_account'           => __CLASS__ . '::hotel_booking_account',
				'hotel_booking_best_reviews'      => __CLASS__ . '::hotel_booking_best_reviews',
				'hotel_booking_cart'              => __CLASS__ . '::hotel_booking_cart',
				'hotel_booking_checkout'          => __CLASS__ . '::hotel_booking_checkout',
				'hotel_booking_lastest_reviews'   => __CLASS__ . '::hotel_booking_lastest_reviews',
				'hotel_booking_mini_cart'         => __CLASS__ . '::hotel_booking_mini_cart',
				'hotel_booking_rooms'             => __CLASS__ . '::hotel_booking_rooms',
				'hotel_booking_slider'            => __CLASS__ . '::hotel_booking_slider',
				'hotel_booking_currency_switcher' => __CLASS__ . 'hotel_booking_currency_switcher'
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
			}
		}

		/**
		 * Shortcode wrapper.
		 *
		 * @since 2.0
		 *
		 * @param $function
		 * @param array $atts
		 * @param array $wrapper
		 *
		 * @return string
		 */
		public static function shortcode_wrapper(
			$function,
			$atts = array(),
			$wrapper = array(
				'class'  => 'wp-hotel-booking',
				'before' => null,
				'after'  => null
			)
		) {
			ob_start();
			echo ( ! empty( $wrapper['before'] ) ) ? $wrapper['before'] : '<div class="' . esc_attr( $wrapper['class'] ) . '">';
			call_user_func( $function, $atts );
			echo ( ! empty( $wrapper['after'] ) ) ? $wrapper['after'] : '</div>';

			return ob_get_clean();
		}

		/**
		 * Display search room form.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking( $atts ) {

		}

		/**
		 * Display latest reviews for room.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_lastest_reviews( $atts ) {

		}

		/**
		 * Display best reviews for room.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_best_reviews( $atts ) {

		}

		/**
		 * Display mini cart.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_mini_cart( $atts ) {

		}

		/**
		 * Display list rooms.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_rooms( $atts ) {

		}

		/**
		 * Display room slider.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_slider( $atts ) {

		}

		/**
		 * Display hotel booking cart page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_cart( $atts ) {

		}

		/**
		 * Display hotel booking checkout page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_checkout( $atts ) {

		}

		/**
		 * Display hotel booking account page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_account( $atts ) {

		}

		/**
		 * Display currency switcher.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_currency_switcher( $atts ) {

		}

	}

}

WPHB_Shortcodes::init();
