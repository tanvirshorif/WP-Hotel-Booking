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

			// enqueue scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// add search room time picker form field
			add_action( 'hotel_booking_after_check_in_field', array( $this, 'check_in_time' ), 10, 2 );
			add_action( 'hotel_booking_after_check_out_field', array( $this, 'check_out_time' ), 10, 2 );

			add_filter( 'hb_search_room_params', array( $this, 'time_picker_params' ) );
			add_filter( 'hb_search_room_args', array( $this, 'search_room_args' ) );
			add_filter( 'hb_search_booking_except_join', array( $this, 'booking_except_join' ), 20, 3 );
			add_filter( 'hb_search_booking_except_conditions', array( $this, 'booking_except_conditions' ), 20, 3 );

			// add booking time in booking admin page
			add_action( 'hb_booking_admin_booking_check_in', array( $this, 'admin_check_in_time' ) );
			add_action( 'hb_booking_admin_booking_check_out', array( $this, 'admin_check_out_time' ) );
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
				wp_enqueue_style( 'wphb-flex-site', WPHB_FLEX_URI . '/assets/css/site.css', array(), WPHB_FLEX_VER );
				wp_enqueue_script( 'wphb-flex-site', WPHB_FLEX_URI . '/assets/js/site.js', array( 'jquery' ), WPHB_FLEX_VER, true );

				wp_enqueue_style( 'wphb-flex-time-picker', WPHB_FLEX_URI . 'assets/css/jquery.timepicker.css', array(), WPHB_FLEX_VER );
				wp_enqueue_script( 'wphb-flex-time-picker', WPHB_FLEX_URI . '/assets/js/jquery.timepicker.min.js', array( 'jquery' ), WPHB_FLEX_VER, true );
			}
		}

		/**
		 * Search room Check in time form field.
		 *
		 * @since 2.0
		 *
		 * @param $uniqid
		 * @param $show_label
		 */
		public function check_in_time( $uniqid, $show_label ) {
			$check_in_time = hb_get_request( 'check_in_time' );
			?>
            <li class="hb-form-field">
				<?php echo $show_label ? __( 'Arrival Time', 'wp-hotel-booking' ) : ''; ?>
                <div class="hb-form-field-input hb_timepicker_input_field">
                    <input type="text" name="check_in_time" id="check_in_time_<?php echo esc_attr( $uniqid ) ?>"
                           class="hb_input_time_check" value="<?php echo esc_attr( $check_in_time ); ?>"
                           placeholder="<?php _e( 'Arrival Time', 'wp-hotel-booking' ); ?>"/>
                    <input type="hidden" name="hb_check_in_time" class="hb_input_time_check" value=""/>
                </div>
            </li>
		<?php }

		/**
		 * Search room Check out time form field.
		 *
		 * @since 2.0
		 *
		 * @param $uniqid
		 * @param $show_label
		 */
		public function check_out_time( $uniqid, $show_label ) {
			$check_out_time = hb_get_request( 'check_out_time' );
			?>
            <li class="hb-form-field">
				<?php echo $show_label ? __( 'Departure Time', 'wp-hotel-booking' ) : ''; ?>
                <div class="hb-form-field-input hb_timepicker_input_field">
                    <input type="text" name="check_out_time" id="check_out_time_<?php echo esc_attr( $uniqid ) ?>"
                           class="hb_input_time_check" value="<?php echo esc_attr( $check_out_time ); ?>"
                           placeholder="<?php _e( 'Departure Time', 'wp-hotel-booking' ); ?>"/>
                    <input type="hidden" name="hb_check_out_time" class="hb_input_time_check" value=""/>
                </div>
            </li>
			<?php
		}

		/**
		 * Add time to search room params.
		 *
		 * @since 2.0
		 *
		 * @param $params
		 *
		 * @return array
		 */
		public function time_picker_params( $params ) {

			$time = array(
				'check_in_time'     => hb_get_request( 'check_in_time' ),
				'hb_check_in_time'  => hb_get_request( 'hb_check_in_time' ),
				'check_out_time'    => hb_get_request( 'check_out_time' ),
				'hb_check_out_time' => hb_get_request( 'hb_check_out_time' )
			);

			return array_merge( $params, $time );
		}

		/**
		 * Add time to search room args.
		 *
		 * @param $args
		 *
		 * @return array
		 */
		public function search_room_args( $args ) {
			$time = array(
				'check_in_time'  => hb_get_request( 'hb_check_in_time', 0 ),
				'check_out_time' => hb_get_request( 'hb_check_out_time', 0 )
			);

			return array_merge( $args, $time );
		}


		public function booking_except_join() {
			global $wpdb;

			$join = "LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS time_in ON order_item.order_item_id = time_in.hotel_booking_order_item_id AND time_in.meta_key = 'check_in_time'
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS time_out ON order_item.order_item_id = time_out.hotel_booking_order_item_id AND time_out.meta_key = 'check_out_time'";

			return $join;
		}

		/**
		 * Where conditions when except booking search room.
		 *
		 * @since 2.0
		 *
		 * @param $default
		 * @param $check_in
		 * @param $check_out
		 *
		 * @return string
		 */
		public function booking_except_conditions( $default, $check_in, $check_out ) {

			$conditions = "( check_in.meta_value > $check_in AND check_in.meta_value < $check_out )
						OR 	( check_out.meta_value > $check_in AND check_out.meta_value < $check_out )
						OR 	( check_in.meta_value < $check_in AND check_out.meta_value > $check_out )";

			return $conditions;
		}

		/**
		 * Add check in time in booking admin page.
		 *
		 * @since 2.0
		 *
		 * @param $order_item_id
		 */
		public function admin_check_in_time( $order_item_id ) {
			printf( '%s', date_i18n( hb_get_time_format(), hb_get_order_item_meta( $order_item_id, 'check_in_time', true ) + 1 ) );
		}

		/**
		 * Add check out time in booking admin page.
		 *
		 * @since 2.0
		 *
		 * @param $order_item_id
		 */
		public function admin_check_out_time( $order_item_id ) {
			printf( '%s', date_i18n( hb_get_time_format(), hb_get_order_item_meta( $order_item_id, 'check_out_time', true ) + 1 ) );
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