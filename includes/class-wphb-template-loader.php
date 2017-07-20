<?php

/**
 * WP Hotel Booking template loader class.
 *
 * @class       WPHB_TemplateLoader
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_TemplateLoader' ) ) {

	/**
	 * Class WPHB_TemplateLoader.
	 *
	 * @since 2.0
	 */
	class WPHB_TemplateLoader {

		/**
		 * WPHB_TemplateLoader constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}

		/**
		 * Load a template.
		 *
		 * Handles template usage so that we can use our own templates instead of the themes.
		 *
		 * Templates are in the 'templates' folder. WP Hotel Booking looks for theme.
		 * overrides in /theme/wp-hotel-booking/ by default.
		 *
		 * @since 2.0
		 *
		 * @param mixed $template
		 *
		 * @return string
		 */
		public function template_loader( $template ) {
			$post_type = get_post_type();

			$file = '';
			$find = array();
			if ( $post_type !== 'hb_room' ) {
				return $template;
			}

			if ( is_post_type_archive( 'hb_room' ) ) {
				$file   = 'archive-room.php';
				$find[] = $file;
				$find[] = hb_template_path() . '/' . $file;
			} else if ( is_room_taxonomy() ) {
				$term     = get_queried_object();
				$taxonomy = $term->taxonomy;
				if ( strpos( $term->taxonomy, 'hb_' ) === 0 ) {
					$taxonomy = substr( $term->taxonomy, 3 );
				}

				$file = 'archive-room.php';

				$find[] = 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = hb_template_path() . '/taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = hb_template_path() . '/taxonomy-' . $taxonomy . '.php';
				$find[] = $file;
			} else if ( is_single() ) {
				$file   = 'single-room.php';
				$find[] = $file;
				$find[] = hb_template_path() . '/' . $file;
			}

			if ( $file ) {
				$find[]      = hb_template_path() . '/' . $file;
				$hb_template = untrailingslashit( WPHB_ABSPATH ) . '/templates/' . $file;
				$template    = locate_template( array_unique( $find ) );

				if ( ! $template && file_exists( $hb_template ) ) {
					$template = $hb_template;
				}
			}

			return $template;
		}

	}

}

new WPHB_TemplateLoader();
