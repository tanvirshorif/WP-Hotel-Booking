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
?>

<?php
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

add_action( 'wphb_statistic_date_filter', 'wphb_statistic_date_filter', 20, 4 );

if ( ! function_exists( 'wphb_statistic_date_filter' ) ) {
	/**
	 * Show date filter.
	 *
	 * @param $tab
	 * @param $date_start
	 * @param $date_end
	 * @param $room_ids
	 */
	function wphb_statistic_date_filter( $tab, $date_start, $date_end, $room_ids ) { ?>
        <form method="GET">
            <input type="hidden" name="page" value="wphb-statistic"/>
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
            <input type="hidden" name="range" value="custom"/>
            <input type="text" class="wphb_statistic_check_in_date" name="report_in"
                   value="<?php echo esc_attr( $date_start ); ?>"/>
            <input type="hidden" name="check_in_timestamp"
                   value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : ''; ?>"/>
            <input type="text" class="wphb_statistic_check_out_date" name="report_out"
                   value="<?php echo esc_attr( $date_end ); ?>"/>
            <input type="hidden" name="check_out_timestamp"
                   value="<?php echo isset( $_REQUEST['check_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_out_timestamp'] ) ) : ''; ?>"/>
			<?php if ( isset( $room_ids ) && $room_ids ) { ?>
				<?php foreach ( (array) $room_ids as $key => $room ) { ?>
                    <input type="hidden" name="room_id[]" value="<?php echo esc_attr( $room ) ?>">
				<?php } ?>
			<?php } ?>
			<?php wp_nonce_field( 'wphb-statistic', 'wphb-statistic' ); ?>
            <button type="submit" class="button"><?php _e( 'Go', 'wphb-statistic' ) ?></button>
        </form>
	<?php }
}

add_action( 'wphb_statistic_actions', 'wphb_statistic_export_form', 10, 4 );

if ( ! function_exists( 'wphb_statistic_export_form' ) ) {
	/**
	 * Show export form.
	 *
	 * @param $tab
	 * @param $range
	 * @param $date_start
	 * @param $date_end
	 */
	function wphb_statistic_export_form( $tab, $range, $date_start, $date_end ) { ?>
        <form id="tp-hotel-booking-export" method="POST">
            <input type="hidden" name="page" value="page=wphb-statistic">
            <input type="hidden" name="range" value="<?php echo esc_attr( $range ); ?>">
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
			<?php if ( isset( $date_start ) ) { ?>
                <input type="hidden" name="report_in" value="<?php echo esc_attr( $date_start ); ?>">
			<?php } ?>
            <input type="hidden" name="check_in_timestamp"
                   value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : '' ?>">
			<?php if ( isset( $date_end ) ) { ?>
                <input type="hidden" name="report_out" value="<?php echo esc_attr( $date_end ); ?>">
			<?php } ?>
            <input type="hidden" name="check_out_timestamp"
                   value="<?php echo isset( $_REQUEST['check_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_out_timestamp'] ) ) : '' ?>">
			<?php wp_nonce_field( 'tp-hotel-booking-report-export', 'tp-hotel-booking-report-export' ) ?>
            <button type="submit"><?php _e( 'Export', 'wphb-statistic' ) ?></button>
        </form>
	<?php }
}
