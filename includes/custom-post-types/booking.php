<?php
/**
 * WP Hotel Booking Booking custom post type class.
 *
 * @class       WPHB_Custom_Post_Type_Booking
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Custom_Post_Type_Booking' ) ) {
	/**
	 * Class WPHB_Custom_Post_Type_Booking
	 */
	class WPHB_Custom_Post_Type_Booking {

		/**
		 * WPHB_Custom_Post_Type_Booking constructor.
		 */
		public function __construct() {
			add_filter( 'views_edit-hb_booking', array( $this, 'views_edit_booking' ) );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}

		/**
		 * @param $views
		 *
		 * @return mixed
		 */
		public function views_edit_booking( $views ) {
			if ( 'hb_booking' != get_post_type() ) {
				return $views;
			}
			unset( $views['all'] );

			$query  = array( 'post_type' => 'hb_booking' );
			$status = $_REQUEST['post_status'] ? $_REQUEST['post_status'] : 'hb-completed';

			$new_views = hb_get_booking_statuses();
			foreach ( $new_views as $view => $name ) {
				$class = '';
				switch ( $view ) {
					case 'hb-completed':
					case 'hb-processing':
					case 'hb-pending':
					case 'hb-cancelled':
						$class                = $status == $view ? ' class="current" ' : '';
						$query['post_status'] = $view;
						break;
					default:
						$query = apply_filters( 'hb_booking_query_post', '' );
						break;
				}

				$result = new WP_Query( array( 'post_type' => 'hb_booking', 'post_status' => 'hb-pending' ) );

				$views[ $view ] = sprintf( '<a href="%s"' . $class . '>' . esc_html( $name ) . '<span class="count"> (%d)</span></a>', esc_url( add_query_arg( $query, 'edit.php' ) ), $result->found_posts );
			}

			return $views;
		}

		/**
		 * @param $query
		 */
		public function pre_get_posts( $query ) {
			$status = $_REQUEST['post_status'] ? $_REQUEST['post_status'] : 'hb-completed';

			/**
			 * @var $query WP_Query
			 */
			if ( $status ) {
				$query->set( 'post_status', $status );
			}
		}
	}
}

new WPHB_Custom_Post_Type_Booking();