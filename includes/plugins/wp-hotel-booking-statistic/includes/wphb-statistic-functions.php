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


if ( ! function_exists( 'wphb_statistic_sidebar' ) ) {

	/**
	 * Get admin view file for statistic sidebar.
	 *
	 * @param string $tab
	 * @param string $range
	 */
	function wphb_statistic_sidebar( $tab = '', $range = '' ) {
		if ( ! $tab || ! $range ) {
			return;
		}

		$file = apply_filters( "tp_hotel_booking_chart_sidebar_{$tab}_{$range}", '', $tab, $range );

		if ( ! $file || ! file_exists( $file ) ) {
			$file = apply_filters( "hotel_booking_chart_sidebar_layout", '', $tab, $range );
		}

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
}
add_action( 'hotel_booking_chart_sidebar', 'wphb_statistic_sidebar', 10, 2 );


if ( ! function_exists( 'wphb_statistic_canvas' ) ) {

	/**
	 * Get statistic canvas.
	 *
	 * @param string $tab
	 * @param string $range
	 */
	function wphb_statistic_canvas( $tab = '', $range = '' ) {
		if ( ! $tab || ! $range ) {
			return;
		}

		$file = apply_filters( "tp_hotel_booking_chart_{$tab}_{$range}_canvas", '', $tab, $range );

		if ( ! $file || ! file_exists( $file ) ) {
			$file = apply_filters( "hotel_booking_chart_layout_canvas", '', $tab, $range );
		}

		if ( file_exists( $file ) ) {
			require $file;
		}
	}

}
add_action( 'hotel_booking_chart_canvas', 'wphb_statistic_canvas', 10, 2 );


if ( ! function_exists( 'wphb_statistic_sidebar_layout' ) ) {

	/**
	 * Get statistic sidebar layout.
	 *
	 * @param $tab
	 * @param $range
	 *
	 * @return string
	 */
	function wphb_statistic_sidebar_layout( $tab, $range ) {
		$tab_range = WPHB_STATISTIC_ABSPATH . 'includes/admin/views/sidebar-' . $tab . '-' . $range . '.php';
		$tab       = WPHB_STATISTIC_ABSPATH . 'includes/admin/views/sidebar-' . $tab . '.php';
		if ( file_exists( $tab_range ) ) {
			return $tab_range;
		} else if ( file_exists( $tab ) ) {
			return $tab;
		}

		return WPHB_STATISTIC_ABSPATH . 'includes/admin/views/sidebar.php';
	}

}
add_filter( 'hotel_booking_chart_sidebar_layout', 'wphb_statistic_sidebar_layout', 10, 2 );


if ( ! function_exists( 'wphb_statistic_canvas_layout' ) ) {

	/**
	 * Get statistic canvas layout.
	 *
	 * @param $tab
	 *
	 * @return mixed
	 */
	function wphb_statistic_canvas_layout( $tab ) {
		$file = WPHB_STATISTIC_ABSPATH . 'includes/admin/views/canvas-' . strtolower( $tab ) . '.php';
		if ( file_exists( $file ) ) {
			return $file;
		}

		return false;
	}
}
add_filter( 'hotel_booking_chart_layout_canvas', 'wphb_statistic_canvas_layout' );