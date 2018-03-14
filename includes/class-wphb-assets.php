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
			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
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
				'wp-util',
				'jquery-ui-slider'
			);

			/**
			 * Register scripts.
			 */
			// fontawesome
			wp_register_style( 'wphb-libraries', WPHB_PLUGIN_URL . 'assets/css/libraries.css', array(), WPHB_VERSION );
			// select2
			wp_register_script( 'wphb-select2', WPHB_PLUGIN_URL . 'assets/js/vendor/select2.min.js', array(), WPHB_VERSION, true );

			wp_register_script( 'wphb-library-moment', WPHB_PLUGIN_URL . 'assets/js/vendor/moment.min.js', $dependencies, WPHB_VERSION, true );

			wp_register_style( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/css/fullcalendar.min.css', array(), WPHB_VERSION );
			wp_register_script( 'wphb-library-fullcalendar', WPHB_PLUGIN_URL . 'assets/js/vendor/fullcalendar.min.js', $dependencies, WPHB_VERSION, true );

			wp_enqueue_style( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/css/jquery.timepicker.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/js/vendor/jquery.timepicker.min.js', array(), WPHB_VERSION, true );

			if ( is_admin() ) {
				$dependencies = array_merge( $dependencies, array( 'backbone' ) );

				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );
				wp_enqueue_script( 'backbone' );

				wp_enqueue_script( 'wphb-vue', WPHB_PLUGIN_URL . 'assets/js/vendor/vue.js', $dependencies, '2.5.13' );
				wp_enqueue_script( 'wphb-vuex', WPHB_PLUGIN_URL . 'assets/js/vendor/vuex.js', $dependencies, '3.0.1' );
				wp_enqueue_script( 'wphb-vue-resource', WPHB_PLUGIN_URL . 'assets/js/vendor/vue-resource.min.js', $dependencies, '1.3.5' );

				wp_enqueue_style( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/css/admin-wphb.css' );

				wp_enqueue_script( 'wphb-admin', WPHB_PLUGIN_URL . 'assets/js/admin-wphb.js', $dependencies, WPHB_VERSION, true );
				wp_localize_script( 'wphb-admin', 'wphb_admin_js', hb_admin_js() );

				wp_enqueue_script( 'wphb-admin-vue', WPHB_PLUGIN_URL . 'assets/js/admin-vue-wphb.js', array(
					'jquery',
					'wphb-vue',
					'wphb-vuex',
					'wphb-vue-resource'
				), WPHB_VERSION );

				$screen = get_current_screen();

				switch ( $screen->id ) {
					case 'wp-hotel-booking_page_wphb-addition-packages':

						wp_localize_script( 'wphb-admin-vue', 'wphb_addition_packages', WPHB_Extra::localize_script() );
						wp_enqueue_script( 'wphb-admin-extra-vue', WPHB_PLUGIN_URL . 'assets/js/admin/extra-editor.js', array(
							'jquery',
							'wphb-vue',
							'wphb-vuex',
							'wphb-vue-resource'
						), WPHB_VERSION );
						break;
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
			} else {

				wp_register_style( 'wphb-site', WPHB_PLUGIN_URL . 'assets/css/wphb.css', array(), WPHB_VERSION );
				wp_register_script( 'wphb-site', WPHB_PLUGIN_URL . 'assets/js/wphb.js', $dependencies, WPHB_VERSION, true );
				wp_localize_script( 'wphb-site', 'wphb_js', hb_i18n() );

				// room gallery
				wp_register_script( 'wphb-library-gallery', WPHB_PLUGIN_URL . 'assets/js/vendor/gallery.min.js', $dependencies, WPHB_VERSION, true );
				// rooms slider widget
				wp_register_script( 'wphb-library-owl-carousel', WPHB_PLUGIN_URL . 'assets/js/vendor/owl.carousel.min.js', $dependencies, WPHB_VERSION, true );
				// lightbox
				wp_register_style( 'wphb-lightbox2', WPHB_PLUGIN_URL . 'assets/css/vendor/lightbox.min.css', array(), WPHB_VERSION );
				wp_register_script( 'wphb-lightbox2', WPHB_PLUGIN_URL . 'assets/js/vendor/lightbox.min.js', $dependencies, WPHB_VERSION, true );
			}

			/**
			 * Enqueue scripts.
			 */
			if ( is_admin() ) {

				wp_enqueue_script( 'wphb-library-moment' );
				wp_enqueue_style( 'wphb-library-fullcalendar' );
				wp_enqueue_script( 'wphb-library-fullcalendar' );
			} else {
				wp_enqueue_style( 'wphb-site' );
				wp_enqueue_script( 'wphb-site' );

				wp_enqueue_script( 'wphb-library-owl-carousel' );
				wp_enqueue_script( 'wphb-library-gallery' );
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

new WPHB_Assets();
