<?php

/**
 * WP Hotel Booking shortcodes class.
 *
 * @class       WPHB_Shortcodes
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Shortcodes' ) ) {
	/**
	 * Class WPHB_Shortcodes.
	 *
	 * @since 2.0
	 */
	class WPHB_Shortcodes {
		/**
		 * Init shortcodes.
		 *
		 * @since 2.0
		 */
		public static function init() {
			$shortcodes = array(
				'hotel_booking'                 => __CLASS__ . '::hotel_booking',
				'hotel_booking_account'         => __CLASS__ . '::hotel_booking_account',
				'hotel_booking_best_reviews'    => __CLASS__ . '::hotel_booking_best_reviews',
				'hotel_booking_cart'            => __CLASS__ . '::hotel_booking_cart',
				'hotel_booking_checkout'        => __CLASS__ . '::hotel_booking_checkout',
				'hotel_booking_lastest_reviews' => __CLASS__ . '::hotel_booking_lastest_reviews',
				'hotel_booking_mini_cart'       => __CLASS__ . '::hotel_booking_mini_cart',
				'hotel_booking_rooms'           => __CLASS__ . '::hotel_booking_rooms',
				'hotel_booking_slider'          => __CLASS__ . '::hotel_booking_slider',
				'hb_widget_room_filter'         => __CLASS__ . '::hb_widget_room_filter',
				'hotel_booking_thankyou'        => __CLASS__ . '::hotel_booking_thankyou'
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
			}
		}

		/**
		 * Shortcode wrapper.
		 *
		 * @since 2.0
		 *
		 * @param $function
		 * @param array $atts
		 * @param array $wrapper
		 *
		 * @return string
		 */
		public static function shortcode_wrapper(
			$function,
			$atts = array(),
			$wrapper = array(
				'class'  => 'wp-hotel-booking',
				'before' => null,
				'after'  => null
			)
		) {
			ob_start();
			echo ( ! empty( $wrapper['before'] ) ) ? $wrapper['before'] : '<div class="' . esc_attr( $wrapper['class'] ) . '">';
			call_user_func( $function, $atts );
			echo ( ! empty( $wrapper['after'] ) ) ? $wrapper['after'] : '</div>';

			return ob_get_clean();
		}

		/**
		 * Display search room form.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking( $atts ) {

			$start_date = hb_get_request( 'hb_check_in_date' );
			if ( $start_date ) {
				$start_date = date( 'm/d/Y', $start_date );
			}
			$end_date = hb_get_request( 'hb_check_out_date' );
			if ( $end_date ) {
				$end_date = date( 'm/d/Y', $end_date );
			}

			$start_time = hb_get_request( 'hb_check_in_time' );
			$end_time   = hb_get_request( 'hb_check_out_time' );

			$adults_term = hb_get_request( 'adults', 0 );
			$adults      = $adults_term ? get_term_meta( $adults_term, 'hb_max_number_of_adults', true ) : 0;
			$max_child   = hb_get_request( 'max_child', 0 );
			$location    = hb_get_request( 'room_location', 0 );

			$atts = wp_parse_args(
				$atts, array(
					'check_in_date'  => $start_date,
					'check_in_time'  => $start_time,
					'check_out_date' => $end_date,
					'check_out_time' => $end_time,
					'adults'         => $adults,
					'max_child'      => $max_child,
					'location'       => $location,
					'search_page'    => null
				)
			);

			$page = hb_get_request( 'hotel-booking' );

			$template      = 'search/form.php';
			$template_args = array();

			// find the url for form action
			if ( $search_page = $atts['search_page'] ) {
				if ( is_numeric( $search_page ) ) {
					$search_permalink = get_the_permalink( $search_page );
				} else {
					$search_permalink = $search_page;
				}
			} else {
				$search_permalink = hb_get_url();
			}
			$template_args['search_page'] = $search_permalink;
			/**
			 * Add argument use in shortcode display
			 */
			$template_args['atts'] = $atts;

			/**
			 * Display the template based on current step
			 */
			switch ( $page ) {
				case 'results':
					$query                    = WPHB_Query::instance( $atts );
					$template                 = 'search/results.php';
					$template_args['results'] = $query->search_rooms( apply_filters( 'hb_search_room_args', $atts ) );
					break;
				default:
					break;
			}
			$template = apply_filters( 'hotel_booking_shortcode_template', $template );
			ob_start();
			do_action( 'hotel_booking_wrapper_shortcode_start' );
			hb_get_template( $template, $template_args );
			do_action( 'hotel_booking_wrapper_shortcode_end' );

			return ob_get_clean();
		}

		/**
		 * Display latest reviews for room.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_lastest_reviews( $atts ) {
			$number = isset( $atts['number'] ) ? $atts['number'] : 5;
			$args   = array(
				'post_type'      => 'hb_room',
				'meta_key'       => 'arveger_rating_last_modify',
				'posts_per_page' => $number,
				'order'          => 'DESC',
				'orderby'        => array( 'meta_value_num' => 'DESC' )
			);
			$query  = new WP_Query( $args );

			if ( $query->have_posts() ) {
				hb_get_template( 'shortcodes/latest-reviews.php', array( 'atts' => $atts, 'query' => $query ) );
			}
			wp_reset_postdata();
		}

		/**
		 * Display best reviews for room.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_best_reviews( $atts ) {
			$number = isset( $atts['number'] ) ? $atts['number'] : 5;
			$args   = array(
				'post_type'      => 'hb_room',
				'meta_key'       => 'arveger_rating',
				'posts_per_page' => $number,
				'order'          => 'DESC',
				'orderby'        => array( 'meta_value_num' => 'DESC' )
			);
			$query  = new WP_Query( $args );

			if ( $query->have_posts() ) {
				hb_get_template( 'shortcodes/best-reviews.php', array( 'atts' => $atts, 'query' => $query ) );
			}
			wp_reset_postdata();
		}

		/**
		 * Display mini cart.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_mini_cart( $atts ) {
			?>
            <div id="hotel_booking_mini_cart_<?php echo uniqid() ?>" class="hotel_booking_mini_cart">

				<?php if ( isset( $atts['title'] ) && $atts['title'] ) { ?>
                    <h3><?php echo esc_html( $atts['title'] ); ?></h3>
				<?php } ?>

				<?php $cart = WPHB_Cart::instance(); ?>

				<?php if ( $cart->get_cart_contents() ) { ?>
					<?php hb_get_template( 'cart/mini-cart.php' ); ?>
				<?php } else { ?>
                    <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty.', 'wp-hotel-booking' ) ?></p>
				<?php } ?>
            </div>
			<?php
		}

		/**
		 * Display list rooms.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_rooms( $atts ) {
			$atts = shortcode_atts( array(
				'room_type'   => '',
				'orderby'     => 'date',
				'order'       => 'DESC',
				'number_room' => - 1,
				'room_in'     => '',
				'room_not_in' => '',
			), $atts );

			$args = array(
				'post_type'      => 'hb_room',
				'posts_per_page' => $atts['number_room'],
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post_status'    => 'publish'
			);

			if ( isset( $atts['room_type'] ) && $atts['room_type'] ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'hb_room_type',
						'field'    => 'slug',
						'terms'    => $atts['room_type']
					)
				);
			}

			if ( isset( $atts['room_in'] ) && $atts['room_in'] ) {
				$args['post__in'] = explode( ',', $atts['room_in'] );
			}

			if ( isset( $atts['room_not_in '] ) && $atts['room_not_in '] ) {
				$args['post__not_in'] = explode( ',', $atts['room_not_in'] );
			}

			/* remove action */
			remove_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 );
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				hotel_booking_room_loop_start();
				while ( $query->have_posts() ) {
					$query->the_post();
					hb_get_template_part( 'content', 'room' );
				} // end of the loop.
				hotel_booking_room_loop_end();
			} else {
				_e( 'No room found', 'wp-hotel-booking' );
			}
			wp_reset_postdata();
			/* add action again */
			add_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 );
		}

		/**
		 * Display room slider.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_slider( $atts ) {
			$number_rooms = isset( $atts['rooms'] ) ? (int) $atts['rooms'] : 10;

			$args  = array(
				'post_type'      => 'hb_room',
				'posts_per_page' => $number_rooms,
				'orderby'        => 'date',
				'order'          => 'DESC'
			);
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				hb_get_template( 'shortcodes/rooms-carousel.php', array( 'atts' => $atts, 'query' => $query ) );
			}
		}

		/**
		 * Room filter by price.
		 *
		 * @param $atts
		 */
		public static function hb_widget_room_filter( $atts ) {
			// only for archive room page
			if ( is_post_type_archive( 'hb_room' ) || is_room_taxonomy() ) {
				hb_get_template( 'shortcodes/room-filter.php', array( 'atts' => $atts ) );
			}
		}

		/**
		 * Thankyou page.
		 *
		 * @param array $atts
		 *
		 * @return string
		 */
		public static function hotel_booking_thankyou( $atts = array() ) {
			$template = apply_filters( 'hotel_booking_thankyou_tpl', 'checkout/thank-you.php' );
			$args     = wp_parse_args( $atts, array(
				'booking_id'  => '',
				'booking_key' => ''
			) );
			ob_start();
			do_action( 'hb_wrapper_start' );
			hb_get_template( $template, $args );
			do_action( 'hb_wrapper_end' );

			return ob_get_clean();
		}

		/**
		 * Display hotel booking cart page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_cart( $atts ) {
			$template = apply_filters( 'hotel_booking_cart_template', 'cart/cart.php' );
			ob_start();
			do_action( 'hotel_booking_wrapper_shortcode_start' );
			hb_get_template( $template, $atts );
			do_action( 'hotel_booking_wrapper_shortcode_end' );

			return ob_get_clean();
		}

		/**
		 * Display hotel booking checkout page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_checkout( $atts ) {
			$customer = new stdClass;

			$customer->title = $customer->first_name = $customer->last_name = $customer->email = $customer->address = $customer->state = '';
			$customer->city  = $customer->postal_code = $customer->country = $customer->phone = $customer->fax = '';

			if ( is_user_logged_in() ) {
				$user = WPHB_User::get_current_user();

				$customer->title       = $user->title;
				$customer->first_name  = $user->first_name;
				$customer->last_name   = $user->last_name;
				$customer->email       = $user->email;
				$customer->address     = $user->address;
				$customer->state       = $user->state;
				$customer->city        = $user->city;
				$customer->postal_code = $user->postal_code;
				$customer->country     = $user->country;
				$customer->phone       = $user->phone;
				$customer->fax         = $user->fax;
			}

			$template      = apply_filters( 'hotel_booking_checkout_tpl', 'checkout/checkout.php' );
			$template_args = apply_filters( 'hotel_booking_checkout_tpl_template_args', array( 'customer' => $customer ) );
			ob_start();
			do_action( 'hotel_booking_wrapper_shortcode_start' );
			hb_get_template( $template, $template_args );
			do_action( 'hotel_booking_wrapper_shortcode_end' );

			return ob_get_clean();
		}

		/**
		 * Display hotel booking account page.
		 *
		 * @since 2.0
		 */
		public static function hotel_booking_account( $atts ) {
			$template = apply_filters( 'hotel_booking_account_template', 'account/account.php' );
			ob_start();
			do_action( 'hotel_booking_wrapper_shortcode_start' );
			hb_get_template( $template, $atts );
			do_action( 'hotel_booking_wrapper_shortcode_end' );

			return ob_get_clean();
		}
	}
}

WPHB_Shortcodes::init();
