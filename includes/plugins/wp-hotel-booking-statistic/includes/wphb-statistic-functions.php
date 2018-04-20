<?php

/**
 * WP Hotel Booking Statistic functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking_Statistic/Functions
 * @category    Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wphb_statistic_get_template' ) ) {
	/**
	 * Get templates passing attributes and including the file.
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function wphb_statistic_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = wphb_statistic_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );

			return;
		}
		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'wphb_statistic_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'wphb_statistic_before_template_part', $template_name, $template_path, $located, $args );

		if ( $located && file_exists( $located ) ) {
			include( $located );
		}

		do_action( 'wphb_statistic_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'wphb_statistic_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return mixed
	 */
	function wphb_statistic_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = hb_template_path();
		}

		if ( ! $default_path ) {
			$default_path = WPHB_STATISTIC_ABSPATH . '/templates/';
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
		return apply_filters( 'wphb_statistic_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'wphb_statistic_is_statistic_page' ) ) {
	/**
	 * Check is admin statistic page.
	 *
	 * @return bool
	 */
	function wphb_statistic_is_statistic_page() {
		if ( is_admin() ) {
			// get current screen
			$screen = get_current_screen();

			return $screen->id == 'wp-hotel-booking_page_wphb-statistic';
		} else {
			return false;
		}
	}
}