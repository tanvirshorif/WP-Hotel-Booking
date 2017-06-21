<?php

/**
 * WP Hotel Booking assets class.
 *
 * @class       WPHB_Assets
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Assets' ) ) {

	/**
	 * Class WPHB_Assets.
	 *
	 * @since 2.0
	 */
	class WPHB_Assets {

		/**
		 * WPHB_Assets constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
		}

		/**
		 * Enqueue assets for the plugin.
		 *
		 * @since 2.0
		 */
		public function enqueue_assets() {
			$dependencies = array(
				'jquery',
				'jquery-ui-sortable',
				'jquery-ui-datepicker',
				'wp-util'
			);

			wp_register_style( 'wp-hotel-booking-libaries-style', WPHB_PLUGIN_URL . 'assets/css/libraries.css' );

			// select2
			wp_register_script( 'wp-admin-hotel-booking-select2', WPHB_PLUGIN_URL . 'assets/js/select2.min.js' );
			if ( is_admin() ) {
				$dependencies = array_merge( $dependencies, array( 'backbone' ) );
				wp_register_style( 'wp-admin-hotel-booking', WPHB_PLUGIN_URL . 'assets/css/admin.tp-hotel-booking.min.css' );
				wp_register_script( 'wp-admin-hotel-booking', WPHB_PLUGIN_URL . 'assets/js/admin.hotel-booking.js', $dependencies );
				wp_localize_script( 'wp-admin-hotel-booking', 'hotel_booking_i18n', hb_admin_i18n() );
				wp_register_script( 'wp-admin-hotel-booking-moment', WPHB_PLUGIN_URL . 'assets/js/moment.min.js', $dependencies );
				wp_register_script( 'wp-admin-hotel-booking-fullcalendar', WPHB_PLUGIN_URL . 'assets/js/fullcalendar.min.js', $dependencies );
				wp_register_style( 'wp-admin-hotel-booking-fullcalendar', WPHB_PLUGIN_URL . 'assets/css/fullcalendar.min.css' );
			} else {
				wp_register_style( 'wp-hotel-booking', WPHB_PLUGIN_URL . 'assets/css/hotel-booking.min.css' );
				wp_register_script( 'wp-hotel-booking', WPHB_PLUGIN_URL . 'assets/js/hotel-booking.min.js', $dependencies, false, true );

				wp_localize_script( 'wp-hotel-booking', 'hotel_booking_i18n', hb_i18n() );

				// rooms slider widget
				wp_register_script( 'wp-hotel-booking-gallery', WPHB_PLUGIN_URL . 'includes/libraries/camera/js/gallery.min.js', $dependencies );

				// owl carousel
				wp_register_script( 'wp-hotel-booking-owl-carousel', WPHB_PLUGIN_URL . 'includes/libraries/owl-carousel/owl.carousel.min.js', $dependencies );
			}

			if ( is_admin() ) {
				wp_enqueue_style( 'wp-admin-hotel-booking' );
				wp_enqueue_script( 'wp-admin-hotel-booking' );
				wp_enqueue_script( 'backbone' );

				// report
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );

				/* fullcalendar */
				wp_enqueue_script( 'wp-admin-hotel-booking-moment' );
				wp_enqueue_style( 'wp-admin-hotel-booking-fullcalendar' );
				wp_enqueue_script( 'wp-admin-hotel-booking-fullcalendar' );
			} else {
				wp_enqueue_style( 'wp-hotel-booking' );
				wp_enqueue_script( 'wp-hotel-booking' );

				// rooms slider widget
				wp_enqueue_script( 'wp-hotel-booking-owl-carousel' );

				// room galleria
				wp_enqueue_script( 'wp-hotel-booking-gallery' );
			}
			wp_enqueue_style( 'wp-hotel-booking-libaries-style' );

			// select2
			wp_enqueue_script( 'wp-admin-hotel-booking-select2' );
			// wp_enqueue_script( 'colorpicker' );
		}

		/**
		 * Output global js settings.
		 *
		 * @since 2.0
		 */
		public function global_js() {
			$upload_dir       = wp_upload_dir();
			$upload_base_url  = $upload_dir['baseurl'];
			$min_booking_date = get_option( 'tp_hotel_booking_minimum_booking_day' ) ? get_option( 'tp_hotel_booking_minimum_booking_day' ) : 1;
			?>
			<script type="text/javascript">
                var hotel_settings = {
                    ajax: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    settings: <?php echo WPHB_Settings::instance()->toJson( apply_filters( 'hb_settings_fields', array( 'review_rating_required' ) ) ); ?>,
                    upload_base_url: '<?php echo esc_js( $upload_base_url ) ?>',
                    meta_key: {
                        prefix: '_hb_'
                    },
                    nonce: '<?php echo wp_create_nonce( 'hb_booking_nonce_action' ); ?>',
                    timezone: '<?php echo current_time( 'timestamp' ) ?>',
                    min_booking_date: <?php echo $min_booking_date; ?>
                }
			</script>
			<?php
		}
	}

}

new WPHB_Assets();
