<?php
/**
 * WP Hotel Booking admin assets class.
 *
 * @class       WPHB_Admin_Assets
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Assets' ) ) {
	/**
	 * Class WPHB_Admin_Assets
	 */
	class WPHB_Admin_Assets {

		/**
		 * WPHB_Admin_Assets constructor.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		}

		/**
		 * Admin assets.
		 *
		 * @throws Exception
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'wphb-library-moment' );
			wp_enqueue_style( 'wphb-library-fullcalendar' );
			wp_enqueue_script( 'wphb-library-fullcalendar' );

			wp_enqueue_script( 'wphb-magnific-popup', WPHB_PLUGIN_URL . 'assets/js/vendor/jquery.magnific-popup.min.js', array() );
			wp_enqueue_style( 'wphb-magnific-popup', WPHB_PLUGIN_URL . 'assets/css/vendor/magnific-popup.min.css' );

			wp_enqueue_script( 'wphb-vue', WPHB_PLUGIN_URL . 'assets/js/vendor/vue.js', array(), '2.5.13' );
			wp_enqueue_script( 'wphb-vuex', WPHB_PLUGIN_URL . 'assets/js/vendor/vuex.js', array(), '3.0.1' );
			wp_enqueue_script( 'wphb-vue-resource', WPHB_PLUGIN_URL . 'assets/js/vendor/vue-resource.min.js', array(), '1.3.5' );

			wp_enqueue_style( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/css/admin-wphb.css' );
			wp_enqueue_script( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/js/admin-wphb.js', array(
				'jquery',
				'jquery-ui-sortable',
				'jquery-ui-datepicker',
				'wp-util',
				'jquery-ui-slider',
				'backbone'
			), WPHB_VERSION, true );
			wp_localize_script( 'wphb-admin', 'wphb_admin_js', hb_admin_js() );

			wp_enqueue_script( 'wphb-admin-vue', WPHB_PLUGIN_URL . 'assets/js/admin-vue-wphb.js', array(
				'jquery',
				'wphb-vue',
				'wphb-vuex',
				'wphb-vue-resource'
			), WPHB_VERSION );

			$screen = get_current_screen();

			switch ( $screen->id ) {
				case 'hb_booking':
					global $post;

					wp_localize_script( 'wphb-admin-vue', 'wphb_admin_booking', WPHB_Booking::localize_script( $post ) );

					wp_enqueue_script( 'wphb-admin-booking-vue', WPHB_PLUGIN_URL . 'assets/js/admin/booking-editor.js', array(
						'jquery',
						'wphb-vue',
						'wphb-vuex',
						'wphb-vue-resource'
					), WPHB_VERSION );
					break;
				default:
					break;
			}

			// select2
			wp_enqueue_script( 'wphb-select2', WPHB_PLUGIN_URL . 'assets/js/vendor/select2.min.js', array(), WPHB_VERSION, true );
			// moment
			wp_enqueue_script( 'wphb-library-moment', WPHB_PLUGIN_URL . 'assets/js/vendor/moment.min.js', array(), WPHB_VERSION, true );
			// full calendar
			wp_enqueue_style( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/css/fullcalendar.min.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/js/vendor/fullcalendar.min.js', array('jquery'), WPHB_VERSION, true );
			// time picker
			wp_enqueue_style( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/css/jquery.timepicker.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/js/vendor/jquery.timepicker.min.js', array(), WPHB_VERSION, true );
		}

		/**
		 * Add invite admin rating.
		 *
		 * @param $text
		 *
		 * @return string
		 */
		public function admin_footer_text( $text ) {
			$current_screen = get_current_screen();
			$wphb_pages     = wphb_get_screen_ids();

			if ( isset( $current_screen->id ) && in_array( $current_screen->id, $wphb_pages ) ) {
				if ( ! get_option( 'wphb_request_plugin_rating' ) ) {
					$text = sprintf( __( 'If you like <strong>WP Hotel Booking</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thanks in advance!', 'wp-hotel-booking' ), '<a href="https://wordpress.org/support/plugin/wp-hotel-booking/reviews/?filter=5#postform" target="_blank" class="wphb-rating-star" data-rated="' . esc_attr__( 'Thanks you so much!', 'wp-hotel-booking' ) . '">', '</a>' );
				} else {
					$text = wp_kses( __( '<i>Thank you for create Your hotel website with <strong>WP Hotel Booking</strong></i>.', 'wp-hotel-booking' ), array(
						'i'      => array(),
						'strong' => array()
					) );
				}
			}

			return $text;
		}
	}
}

new WPHB_Admin_Assets();