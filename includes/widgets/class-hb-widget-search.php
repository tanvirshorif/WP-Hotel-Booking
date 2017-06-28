<?php

/**
 * WP Hotel Booking search room widget class.
 *
 * @class       HB_Widget_Search
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'HB_Widget_Search' ) ) {

	/**
	 * Class HB_Widget_Search.
	 *
	 * @since 2.0
	 */
	class HB_Widget_Search extends WPHB_Abstract_Widget {

		/**
		 * @var string
		 */
		protected $widget_id = 'hb_widget_search';

		/**
		 * HB_Widget_Best_Reviews constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->widget_title       = __( 'HB Search Rooms', 'wp-hotel-booking' );
			$this->widget_description = __( 'the form for search rooms', 'wp-hotel-booking' );
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
			if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
				echo sprintf( '%s', $args['before_title'] . $title . $args['after_title'] );
			}
			// check show label search form
			$show_label = 'true';
			if ( isset( $instance['show_label'] ) ) {
				$show_label = $instance['show_label'];
			}
			echo do_shortcode( '[hotel_booking show_label="' . $show_label . '"]' );
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
		function form( $instance ) {
			$title         = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$checked_label = ( ! isset( $instance['show_label'] ) || $instance['show_label'] === 'true' ) ? 'checked' : '';
			?>
            <p>
				<?php $title_id = $this->get_field_id( 'title' ); ?>
                <label for="<?php echo esc_attr( $title_id ); ?>"><?php _e( 'Title:', 'wp-hotel-booking' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $title_id ); ?>"
                       name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
				<?php $label_id = $this->get_field_id( 'show_label' ); ?>
                <input type="checkbox" id="<?php echo esc_attr( $label_id ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'show_label' ) ); ?>"
                       value="true"<?php printf( '%s', $checked_label ); ?>>
                <label for="<?php echo esc_attr( $label_id ); ?>"><?php _e( 'Show label search form', 'wp-hotel-booking' ) ?></label>
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
			$instance               = array();
			$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['show_label'] = ( isset( $new_instance['show_label'] ) ) ? strip_tags( $new_instance['show_label'] ) : 'false';

			return $instance;
		}
	}

}