<?php
/**
 * WP Hotel Booking template functions.
 *
 * @version    2.0
 * @author        ThimPress
 * @package    WP_Hotel_Booking_Statistic/Functions
 * @category    Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'hb_template_path' ) ) {
	/**
	 * Get plugin template path.
	 *
	 * @return mixed
	 */
	function hb_template_path() {
		return apply_filters( 'hb_template_path', 'wp-hotel-booking' );
	}
}

if ( ! function_exists( 'hb_get_template_part' ) ) {
	/**
	 * Get template part
	 *
	 * @param $slug
	 * @param string $name
	 *
	 * @return mixed|string
	 */
	function hb_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", hb_template_path() . "/{$slug}-{$name}.php" ) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( WPHB_TEMPLATES . "{$slug}-{$name}.php" ) ) {
			$template = WPHB_TEMPLATES . "{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", hb_template_path() . "{$slug}.php" ) );
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'hb_get_template_part', $template, $slug, $name );
		}
		if ( $template && file_exists( $template ) ) {
			load_template( $template, false );
		}

		return $template;
	}
}

if ( ! function_exists( 'hb_get_template' ) ) {
	/**
	 * Get templates passing attributes and including the file
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function hb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = hb_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );

			return;
		}
		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'hb_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'hb_before_template_part', $template_name, $template_path, $located, $args );

		if ( $located && file_exists( $located ) ) {
			include( $located );
		}

		do_action( 'hb_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'hb_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *         yourtheme        /    $template_path    /    $template_name
	 *         yourtheme        /    $template_name
	 *         $default_path    /    $template_name
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return mixed
	 */
	function hb_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = hb_template_path();
		}

		if ( ! $default_path ) {
			$default_path = WPHB_TEMPLATES;
		}

		$template = null;
		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'hb_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'hb_get_template_content' ) ) {
	/**
	 * Get template content, for JS template.
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return string
	 */
	function hb_get_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		hb_get_template( $template_name, $args, $template_path, $default_path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'hb_enqueue_lightbox_assets' ) ) {
	/**
	 * Enqueue lightbox in search room page.
	 */
	function hb_enqueue_lightbox_assets() {
		wp_enqueue_script( 'wphb-lightbox2' );
		wp_enqueue_style( 'wphb-lightbox2' );
	}
}

if ( ! function_exists( 'hb_print_mini_cart_template' ) ) {
	/**
	 * Print mini cart JS template.
	 */
	function hb_print_mini_cart_template() {
		echo hb_get_template_content( 'cart/mini-cart-js-template.php' );
	}
}

if ( ! function_exists( 'hb_setup_room_data' ) ) {
	/**
	 * Setup room data, global $hb_room.
	 *
	 * @param $post
	 *
	 * @return bool|mixed|WPHB_Room
	 */
	function hb_setup_room_data( $post ) {
		/**
		 * Setup room data.
		 *
		 * @since 2.0
		 *
		 * @param $post
		 *
		 * @return bool|mixed
		 */
		unset( $GLOBALS['hb_room'] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post ) {
			$post = $GLOBALS['post'];
		}

		if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'hb_room' ) ) ) {
			return false;
		}

		return $GLOBALS['hb_room'] = WPHB_Room::instance( $post );
	}
}

/* * **************************************************************** Loop ***************************************************************** */

if ( ! function_exists( 'hotel_booking_page_title' ) ) {
	/**
	 * Page title.
	 *
	 * @param bool $echo
	 */
	function hotel_booking_page_title( $echo = true ) {
		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'wp-hotel-booking' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'wp-hotel-booking' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {
			$page_title = single_term_title( "", false );
		} else {
			$shop_page_id = hb_get_page_id( 'shop' );
			$page_title   = get_the_title( $shop_page_id );
		}

		$page_title = apply_filters( 'hotel_booking_page_title', $page_title );

		if ( $echo ) {
			echo sprintf( '%s', $page_title );
		} else {
			echo $page_title;
		}
	}
}

if ( ! function_exists( 'hotel_booking_room_loop_start' ) ) {
	/**
	 * Room loop start.
	 */
	function hotel_booking_room_loop_start() {
		ob_start();
		hb_get_template( 'loop/loop-start.php' );

		echo ob_get_clean();
	}
}

if ( ! function_exists( 'hotel_booking_room_loop_end' ) ) {
	/**
	 * Output the end of a room loop. By default this is a <ul>
	 */
	function hotel_booking_room_loop_end() {
		ob_start();
		hb_get_template( 'loop/loop-end.php' );

		echo ob_get_clean();
	}
}

if ( ! function_exists( 'hotel_booking_template_loop_room_title' ) ) {
	/**
	 * Show the room title in the room loop. By default this is an H3
	 */
	function hotel_booking_template_loop_room_title() {
		hb_get_template( 'loop/title.php' );
	}
}

if ( ! function_exists( 'hotel_booking_loop_room_thumbnail' ) ) {
	/**
	 * Room thumbnail.
	 */
	function hotel_booking_loop_room_thumbnail() {
		hb_get_template( 'loop/thumbnail.php' );
	}
}

if ( ! function_exists( 'hotel_booking_room_title' ) ) {
	/**
	 * Room title.
	 */
	function hotel_booking_room_title() {
		hb_get_template( 'loop/title.php' );
	}
}

if ( ! function_exists( 'hotel_booking_loop_room_price' ) ) {
	/**
	 * Room price/
	 */
	function hotel_booking_loop_room_price() {
		hb_get_template( 'loop/price.php' );
	}
}

if ( ! function_exists( 'hotel_booking_loop_room_rating' ) ) {
	/**
	 * Loop room rating.
	 */
	function hotel_booking_loop_room_rating() {
		global $hb_room;
		global $hb_settings;
		if ( $hb_settings->get( 'catalog_display_rating' ) ) {
			hb_get_template( 'loop/rating.php', array( 'rating' => $hb_room->average_rating() ) );
		}
	}
}

if ( ! function_exists( 'hotel_booking_after_room_loop' ) ) {
	/**
	 * After room loop.
	 */
	function hotel_booking_after_room_loop() {
		hb_get_template( 'loop/pagination.php' );
	}
}

if ( ! function_exists( 'hotel_booking_single_room_gallery' ) ) {
	/**
	 * Single room gallery.
	 */
	function hotel_booking_single_room_gallery() {
		hb_get_template( 'single-room/gallery.php' );
	}
}

if ( ! function_exists( 'hotel_booking_single_room_information' ) ) {
	/**
	 * Single room information.
	 */
	function hotel_booking_single_room_information() {
		hb_get_template( 'single-room/tabs.php' );
	}
}

if ( ! function_exists( 'hotel_show_pricing' ) ) {
	/**
	 * Single room pricing plan.
	 */
	function hotel_show_pricing() {
		hb_get_template( 'loop/pricing-plan.php' );
	}
}

if ( ! function_exists( 'hb_room_review_item' ) ) {
	/**
	 * Single room review item.
	 *
	 * @param $comment
	 * @param $args
	 * @param $depth
	 */
	function hb_room_review_item( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		hb_get_template( 'single-room/review-item.php', array(
			'comment' => $comment,
			'args'    => $args,
			'depth'   => $depth
		) );
	}
}

if ( ! function_exists( 'hb_body_class' ) ) {
	/**
	 * Add custom class for plugin pages.
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function hb_body_class( $classes ) {
		global $post;

		if ( ! isset( $post->ID ) ) {
			return $classes;
		}

		$classes = (array) $classes;

		switch ( $post->ID ) {
			case hb_get_page_id( 'rooms' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-rooms';
				break;
			case hb_get_page_id( 'cart' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-cart';
				break;
			case hb_get_page_id( 'checkout' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-checkout';
				break;
			case hb_get_page_id( 'search' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-search-rooms';
				break;
			case hb_get_page_id( 'account' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-account';
				break;
			case hb_get_page_id( 'terms' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-terms';
				break;
			case hb_get_page_id( 'thankyou' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-thank-you';
				break;
			default:
				break;
		}

		if ( is_room() || is_room_taxonomy() ) {
			$classes[] = 'wp-hotel-booking';
			$classes[] = 'wp-hotel-booking-room-page';
		}

		return array_unique( $classes );
	}
}

if ( ! function_exists( 'hotel_booking_single_room_related' ) ) {
	/**
	 * Single room related room.
	 */
	function hotel_booking_single_room_related() {
		hb_get_template( 'single-room/related-room.php' );
	}
}

if ( ! function_exists( 'hotel_booking_num_room_archive' ) ) {
	/**
	 * Set number room per page.
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	function hotel_booking_num_room_archive( $query ) {
		if ( ! is_admin() && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] === 'hb_room' && is_archive() ) {
			global $hb_settings;
			$query->set( 'posts_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
		}

		return $query;
	}
}

if ( ! function_exists( 'hotel_booking_after_loop_room_item' ) ) {
	/**
	 * After loop room item.
	 */
	function hotel_booking_after_loop_room_item() {
		global $hb_settings;
		if ( $hb_settings->get( 'enable_gallery_lightbox' ) ) {
			hb_get_template( 'loop/gallery-lightbox.php' );
		}
	}
}