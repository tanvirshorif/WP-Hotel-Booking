<?php

/**
 * WP Hotel Booking currency switcher widget class.
 *
 * @class       HB_Widget_Currency_Switch
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'HB_Widget_Currency_Switch' ) ) {

	/**
	 * Class HB_Widget_Currency_Switch.
	 *
	 * @since 2.0
	 */
	class HB_Widget_Currency_Switch extends WPHB_Abstract_Widget {

		/**
		 * @var string
		 */
		protected $widget_id = 'hb_widget_currency_switcher';

		/**
		 * HB_Widget_Best_Reviews constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->widget_title       = __( 'HB Currency Switcher', 'wp-hotel-booking' );
			$this->widget_description = __( 'Display hotel booking currency switcher', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Display the search form in widget.
		 *
		 * @since 2.0
		 *
		 * @param array $args
		 * @param array $instance
		 *
		 * @return void
		 */
		public function widget( $args, $instance ) {
			echo sprintf( '%s', $args['before_widget'] );
			$html = array();
			if ( $instance ) {
				if ( ! empty( $instance['title'] ) ) {
					$html[] = '<h3>' . $instance['title'] . '</h3>';
				}

				$html[] = '[hotel_booking_curreny_switcher';
				foreach ( $instance as $att => $param ) {
					if ( is_array( $param ) ) {
						$param = implode( ',', $param );
					}
					$html[] = $att . '="' . $param . '"';
				}
				$html[] = '][/hotel_booking_curreny_switcher]';
			}

			echo do_shortcode( implode( ' ', $html ) );
			echo sprintf( '%s', $args['after_widget'] );
		}

		/**
		 * Widget options.
		 *
		 * @since 2.0
		 *
		 * @param array $instance
		 *
		 * @return void
		 */
		public function form( $instance ) {
			$title      = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$currencies = ! empty( $instance['currencies'] ) ? $instance['currencies'] : array();
			$id         = uniqid();
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wp-hotel-booking' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'currencies' ) ); ?>"><?php _e( 'Select Currencies:', 'wp-hotel-booking' ); ?></label>
                <select name="<?php echo esc_attr( $this->get_field_name( 'currencies' ) ); ?>[]"
                        id="tp_hb_currencies_select_<?php echo esc_attr( $id ) ?>" class="tokenize-sample widefat"
                        multiple="multiple">
					<?php foreach ( hb_payment_currencies() as $k => $cur ) : ?>
                        <option value="<?php echo esc_attr( $k ); ?>"<?php echo in_array( $k, $currencies ) ? ' selected' : '' ?>>
							<?php printf( '%s', $cur ) ?>
                        </option>
					<?php endforeach; ?>
                </select>
            </p>

            <script type="text/javascript">
                (function ($) {
                    $(document).ready(function () {
                        if (typeof $.fn.tokenize !== 'undefined') {
                            $('#tp_hb_currencies_select_<?php echo esc_js( $id ) ?>').tokenize();
                        }
                    });
                })(jQuery);
            </script>
			<?php
		}

		/**
		 * Handle update.
		 *
		 * @since 2.0
		 *
		 * @param $new_instance
		 * @param $old_instance
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance               = array();
			$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['currencies'] = ( ! empty( $new_instance['currencies'] ) ) ? $new_instance['currencies'] : array();

			return $instance;
		}
	}

}