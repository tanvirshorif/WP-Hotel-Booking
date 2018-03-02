<?php

/**
 * WP Hotel Booking room filter widget class.
 *
 * @class       HB_Widget_Room_Filter
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'HB_Widget_Room_Filter' ) ) {

	/**
	 * Class HB_Widget_Room_Filter.
	 *
	 * @since 2.0
	 */
	class HB_Widget_Room_Filter extends WPHB_Abstract_Widget {

		/**
		 * @var string
		 */
		protected $widget_id = 'hb_widget_room_filter';

		/**
		 * HB_Widget_Room_Filter constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->widget_title       = __( 'HB Room Filter', 'wp-hotel-booking' );
			$this->widget_description = __( 'Display a slider to filter rooms by price.', 'wp-hotel-booking' );
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
				$html[] = '[hb_widget_room_filter';
				foreach ( $instance as $att => $param ) {
					if ( is_array( $param ) ) {
						continue;
					}
					$html[] = $att . '="' . $param . '"';
				}
				$html[] = '][/hb_widget_room_filter]';
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
			$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>">
            </p>
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
			$instance          = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

			return $instance;
		}
	}
}