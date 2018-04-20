<?php
/**
 * WP Hotel Booking assets class.
 *
 * @class       WPHB_Assets
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Assets' ) ) {
	/**
	 * Class WPHB_Assets
	 */
	class WPHB_Assets {

		/**
		 * WPHB_Assets constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_print_scripts', array( $this, 'global_js' ) );

		}

		/**
		 * Frontend assets.
		 */
		public function enqueue_assets() {

			// room gallery
			wp_enqueue_script( 'wphb-library-gallery', WPHB_PLUGIN_URL . 'assets/js/vendor/gallery.min.js', array( 'jquery' ), WPHB_VERSION, true );
			// time picker
			wp_enqueue_style( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/css/jquery.timepicker.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wphb-library-timepicker', WPHB_PLUGIN_URL . 'assets/js/vendor/jquery.timepicker.min.js', array(), WPHB_VERSION, true );

			wp_enqueue_style( 'wphb-site', WPHB_PLUGIN_URL . 'assets/css/wphb.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wphb-site', WPHB_PLUGIN_URL . 'assets/js/wphb.js', array(
				'jquery',
				'jquery-ui-sortable',
				'jquery-ui-datepicker',
				'wp-util',
				'wphb-library-timepicker',
				'jquery-ui-slider',
				'backbone'
			), WPHB_VERSION, true );
			wp_localize_script( 'wphb-site', 'wphb_js', hb_i18n() );
		}

		/**
		 * Output global js settings.
		 */
		public function global_js() {
			$upload_dir       = wp_upload_dir();
			$upload_base_url  = $upload_dir['baseurl'];
			$min_booking_date = get_option( 'tp_hotel_booking_minimum_booking_day' ) ? get_option( 'tp_hotel_booking_minimum_booking_day' ) : 1; ?>
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
