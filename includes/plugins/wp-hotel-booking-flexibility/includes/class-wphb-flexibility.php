<?php

/**
 * WP Hotel Booking Flexibility class.
 *
 * @class       WPHB_Booking_Flexibility
 * @version     2.0
 * @package     WPHB_Booking_Flexibility/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Flexibility' ) ) {

	/**
	 * Class WPHB_Flexibility.
	 *
	 * @since 2.0
	 */
	class WPHB_Flexibility {

		/**
		 * @var null
		 */
		private static $instance = null;

		/**
		 * WPHB_Flexibility constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			if ( ! hb_settings()->get( 'flexible_booking', 0 ) ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 2.0
		 */
		public function enqueue_scripts() {
			if ( is_admin() ) {
				wp_enqueue_script( 'wphb-flex-admin', WPHB_FLEX_URI . '/assets/js/admin.js', array( 'jquery' ), WPHB_FLEX_VER, true );
			} else {
				wp_enqueue_script( 'wphb-flex-site', WPHB_FLEX_URI . '/assets/js/site.js', array( 'jquery' ), WPHB_FLEX_VER, true );

				wp_enqueue_style( 'wphb-flex-date-time-picker', WPHB_FLEX_URI . 'assets/css/jquery.datetimepicker.min.css', array(), WPHB_FLEX_VER );
				wp_enqueue_script( 'wphb-flex-date-time-picker', WPHB_FLEX_URI . '/assets/js/jquery.datetimepicker.min.js', array( 'jquery' ), WPHB_FLEX_VER, true );
				wp_enqueue_script( 'wphb-flex-date-time-picker-full', WPHB_FLEX_URI . '/assets/js/jquery.datetimepicker.full.min.js', array( 'jquery' ), WPHB_FLEX_VER, true );
			}
		}

		/**
		 * Get instances.
		 *
		 * @since 2.0
		 *
		 * @return null|WPHB_Flexibility
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

}

WPHB_Flexibility::instance();