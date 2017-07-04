<?php

/**
 * WP Hotel Booking comment class.
 *
 * @class       WPHB_Comments
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Comments' ) ) {

	/**
	 * Class WPHB_Comments.
	 *
	 * @since 2.0
	 */
	class WPHB_Comments {

		/**
		 * WPHB_Comments constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {

			add_action( 'comment_post', array( __CLASS__, 'add_comment_rating' ), 10, 2 );
			add_action( 'hotel_booking_single_room_before_tabs_content_hb_room_reviews', 'comments_template' );
			add_filter( 'comments_template', array( __CLASS__, 'load_comments_template' ) );

			// details title tab
			add_action( 'hotel_booking_single_room_after_tabs_hb_room_reviews', array( __CLASS__, 'comments_count' ) );
			add_filter( 'hotel_booking_single_room_information_tabs', array( __CLASS__, 'addTabReviews' ) );

		}

		/**
		 * Load template for room reviews if it is enable.
		 *
		 * @since 2.0
		 */
		public function comments_template() {
			if ( comments_open() ) {
				comments_template();
			}
		}

		/**
		 * Load template for reviews if we found a file in theme/plugin directory.
		 *
		 * @since 2.0
		 *
		 * @param string $template
		 *
		 * @return string
		 */
		public static function load_comments_template( $template ) {
			if ( get_post_type() === 'hb_room' ) {
				$check_dirs = array(
					trailingslashit( get_stylesheet_directory() ) . 'wp-hotel-booking',
					trailingslashit( get_template_directory() ) . 'wp-hotel-booking',
					trailingslashit( get_stylesheet_directory() ),
					trailingslashit( get_template_directory() ),
					trailingslashit( WPHB_ABSPATH . 'templates/' )
				);

				foreach ( $check_dirs as $dir ) {
					if ( file_exists( trailingslashit( $dir ) . 'single-room-reviews.php' ) ) {
						return trailingslashit( $dir ) . 'single-room-reviews.php';
					}
				}
			}

			return $template;
		}

		/**
		 * Add comment rating.
		 *
		 * @since 2.0
		 *
		 * @param int $comment_id
		 */
		public static function add_comment_rating( $comment_id, $approved ) {
			if ( isset( $_POST['rating'] ) && 'hb_room' === get_post_type( $_POST['comment_post_ID'] ) ) {
				$rating = absint( sanitize_text_field( $_POST['rating'] ) );
				if ( $rating && $rating <= 5 && $rating > 0 ) {
					// save comment rating
					add_comment_meta( $comment_id, 'rating', $rating, true );

					if ( $approved === 1 ) {
						// save post meta arveger_rating
						$comment = get_comment( $comment_id );

						$postID = absint( $comment->comment_post_ID );

						$room           = WPHB_Room::instance( $postID );
						$averger_rating = $room->average_rating();
						$old_rating     = get_post_meta( $postID, 'arveger_rating', true );

						if ( $old_rating ) {
							update_post_meta( $postID, 'arveger_rating', $averger_rating );
							update_post_meta( $postID, 'arveger_rating_last_modify', time() );
						} else {
							add_post_meta( $postID, 'arveger_rating', $averger_rating );
							add_post_meta( $postID, 'arveger_rating_last_modify', time() );
						}
					}
				}
			}
		}

		/**
		 * Count comments.
		 *
		 * @since 2.0
		 */
		public static function comments_count() {
			global $hb_room;
			echo '<span class="comment-count">(' . $hb_room->get_review_count() . ')</span>';
		}

		/**
		 * Add tab review in single room page.
		 *
		 * @since 2.0
		 *
		 * @param $tabsInfo
		 *
		 * @return array
		 */
		public static function addTabReviews( $tabsInfo ) {
			if ( ! comments_open() ) {
				return $tabsInfo;
			}

			$tabsInfo[] = array(
				'id'      => 'hb_room_reviews',
				'title'   => __( 'Reviews', 'wp-hotel-booking' ),
				'content' => ''
			);

			return $tabsInfo;
		}

	}

}

new WPHB_Comments();
