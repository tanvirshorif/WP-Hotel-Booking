<?php

/**
 * WP Hotel Booking room carousel widget class.
 *
 * @class       HB_Widget_Room_Carousel
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'HB_Widget_Room_Carousel' ) ) {

	/**
	 * Class HB_Widget_Room_Carousel.
	 *
	 * @since 2.0
	 */
	class HB_Widget_Room_Carousel extends WPHB_Abstract_Widget {

		/**
		 * @var string
		 */
		protected $widget_id = 'hb_widget_carousel';

		/**
		 * HB_Widget_Best_Reviews constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->widget_title       = __( 'HB Rooms Carousel', 'wp-hotel-booking' );
			$this->widget_description = __( 'Display rooms slider', 'wp-hotel-booking' );
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
				$html[] = '[hotel_booking_slider';
				foreach ( $instance as $att => $param ) {
					if ( is_array( $param ) ) {
						continue;
					}
					$html[] = $att . '="' . $param . '"';
				}
				$html[] = '][/hotel_booking_slider]';
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
			$title     = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$rooms     = ! empty( $instance['rooms'] ) ? $instance['rooms'] : 10;
			$number    = ! empty( $instance['number'] ) ? $instance['number'] : 4;
			$thumb     = ! empty( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
			$text_link = ! empty( $instance['text_link'] ) ? $instance['text_link'] : '';
			$price     = isset( $instance['price'] ) ? $instance['price'] : 'min';
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'rooms' ) ); ?>"><?php _e( 'Number of rooms to show:' ); ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'rooms' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'rooms' ) ); ?>" type="number"
                       value="<?php echo esc_attr( $rooms ); ?>" min="1">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of items:' ); ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number"
                       value="<?php echo esc_attr( $number ); ?>" min="1">
            </p>
            <p>
                <label><?php _e( 'Navigation:' ); ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'nav' ) ); ?>1"
                       name="<?php echo esc_attr( $this->get_field_name( 'nav' ) ); ?>" type="radio"
                       value="1"<?php echo sprintf( '%s', ( ! isset( $instance['nav'] ) || $instance['nav'] ) ? 'checked' : '' ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'nav' ) ); ?>1"><?php _e( 'Yes', 'wp-hotel-booking' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'nav' ) ); ?>0"
                       name="<?php echo esc_attr( $this->get_field_name( 'nav' ) ); ?>" type="radio"
                       value="0"<?php echo sprintf( '%s', ( isset( $instance['nav'] ) && ! $instance['nav'] ) ? 'checked' : '' ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'nav' ) ); ?>0"><?php _e( 'No', 'wp-hotel-booking' ) ?></label>
            </p>
            <p>
                <label><?php _e( 'Pagination:' ); ?></label>
                <!--yes-->
                <input id="<?php echo esc_attr( $this->get_field_id( 'pagination' ) ); ?>1"
                       name="<?php echo esc_attr( $this->get_field_name( 'pagination' ) ); ?>" type="radio"
                       value="1"<?php echo sprintf( '%s', ( ! isset( $instance['pagination'] ) || $instance['pagination'] ) ? 'checked' : '' ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'pagination' ) ); ?>1"><?php _e( 'Yes', 'wp-hotel-booking' ) ?></label>
                <!--no-->
                <input id="<?php echo esc_attr( $this->get_field_id( 'pagination' ) ); ?>0"
                       name="<?php echo esc_attr( $this->get_field_name( 'pagination' ) ); ?>" type="radio"
                       value="0"<?php echo sprintf( '%s', ( isset( $instance['pagination'] ) && ! $instance['pagination'] ) ? 'checked' : '' ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'pagination' ) ); ?>0"><?php _e( 'No', 'wp-hotel-booking' ) ?></label>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'text_link' ) ); ?>"><?php _e( 'Text Link', 'wp-hotel-booking' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'text_link' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'text_link' ) ); ?>" type="text"
                       value="<?php echo esc_attr( $text_link ); ?>">
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
			$instance['rooms']      = ( ! empty( $new_instance['rooms'] ) ) ? strip_tags( $new_instance['rooms'] ) : 10;
			$instance['number']     = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : 4;
			$instance['text_link']  = ( isset( $new_instance['text_link'] ) ) ? strip_tags( $new_instance['text_link'] ) : '';
			$instance['nav']        = ( isset( $new_instance['nav'] ) ) ? strip_tags( $new_instance['nav'] ) : 1;
			$instance['pagination'] = ( isset( $new_instance['pagination'] ) ) ? strip_tags( $new_instance['pagination'] ) : 1;
			return $instance;
		}
	}

}