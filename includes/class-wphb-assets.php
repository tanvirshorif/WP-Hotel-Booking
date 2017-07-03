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

			$dependencies = array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'wp-util' );

			/**
			 * Register scripts.
			 */
			// fontawesome
			wp_register_style( 'wphb-libraries', WPHB_PLUGIN_URL . 'assets/css/libraries.css', array(), WPHB_VERSION );
			// select2
			wp_register_script( 'wphb-select2', WPHB_PLUGIN_URL . 'assets/js/select2.min.js', array(), WPHB_VERSION, true );

			if ( is_admin() ) {
				$dependencies = array_merge( $dependencies, array( 'backbone' ) );

				wp_enqueue_script( 'wphb-vue', WPHB_PLUGIN_URL . 'assets/js/vue.js', $dependencies, WPHB_VERSION, true );

				wp_register_style( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/css/admin-wphb.css' );
				wp_register_script( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/js/admin-wphb.js', $dependencies, WPHB_VERSION, true );

//				wp_register_script( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/js/admin.hotel-booking.js', $dependencies, WPHB_VERSION, true );
				wp_localize_script( 'wphb-admin', 'hotel_booking_i18n', hb_admin_i18n() );

				wp_register_script( 'wphb-library-moment', WPHB_PLUGIN_URL . 'assets/js/moment.min.js', $dependencies, WPHB_VERSION, true );

				wp_register_style( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/css/fullcalendar.min.css', array(), WPHB_VERSION );
				wp_register_script( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/js/fullcalendar.min.js', $dependencies, WPHB_VERSION, true );

			} else {

				wp_register_style( 'wphb-site', WPHB_PLUGIN_URL . 'assets/css/wphb.css', array(), WPHB_VERSION );
				wp_register_script( 'wphb-site', WPHB_PLUGIN_URL . 'assets/js/hotel-booking.js', $dependencies, WPHB_VERSION, true );
				wp_localize_script( 'wphb-site', 'hotel_booking_i18n', hb_i18n() );

				// room gallery
				wp_register_script( 'wphb-library-gallery', WPHB_PLUGIN_URL . 'assets/js/gallery.min.js', $dependencies, WPHB_VERSION, true );
				// rooms slider widget
				wp_register_script( 'wphb-library-owl-carousel', WPHB_PLUGIN_URL . 'assets/js/owl.carousel.min.js', $dependencies, WPHB_VERSION, true );
				// lightbox
				wp_register_style( 'wphb-lightbox2', WPHB_PLUGIN_URL . 'assets/css/lightbox.min.css', array(), WPHB_VERSION );
				wp_register_script( 'wphb-lightbox2', WPHB_PLUGIN_URL . 'assets/js/lightbox.min.js', $dependencies, WPHB_VERSION, true );
			}

			/**
			 * Enqueue scripts.
			 */
			if ( is_admin() ) {

				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );
				wp_enqueue_script( 'backbone' );

				wp_enqueue_style( 'wphb-admin' );
				wp_enqueue_script( 'wphb-admin' );

				wp_enqueue_script( 'wphb-library-moment' );
				wp_enqueue_style( 'wphb-library-fullcalendar' );
				wp_enqueue_script( 'wphb-library-fullcalendar' );
			} else {
				wp_enqueue_style( 'wphb-site' );
				wp_enqueue_script( 'wphb-site' );

				wp_enqueue_script( 'wphb-library-owl-carousel' );
				wp_enqueue_script( 'wp-hotel-booking-gallery' );
			}
			wp_enqueue_style( 'wphb-libraries' );
			wp_enqueue_script( 'wphb-select2' );
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
