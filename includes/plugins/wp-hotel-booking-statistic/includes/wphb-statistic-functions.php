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
	 * @param $start
	 * @param $end
	 * @param $room_ids
	 */
	function wphb_statistic_date_filter( $tab, $start, $end, $room_ids ) { ?>
        <form method="GET">
            <input type="hidden" name="page" value="wphb-statistic"/>
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
            <input type="hidden" name="range" value="custom"/>
            <input type="text" class="wphb_statistic_check_in_date" name="report_in"
                   value="<?php echo esc_attr( $start ); ?>"/>
            <input type="hidden" name="check_in_timestamp"
                   value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : ''; ?>"/>
            <input type="text" class="wphb_statistic_check_out_date" name="report_out"
                   value="<?php echo esc_attr( $end ); ?>"/>
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
	 * @param $start
	 * @param $end
	 */
	function wphb_statistic_export_form( $tab, $range, $start, $end ) { ?>
        <form id="tp-hotel-booking-export" method="POST">
            <input type="hidden" name="page" value="page=wphb-statistic">
            <input type="hidden" name="range" value="<?php echo esc_attr( $range ); ?>">
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
			<?php if ( isset( $start ) ) { ?>
                <input type="hidden" name="report_in" value="<?php echo esc_attr( $start ); ?>">
			<?php } ?>
            <input type="hidden" name="check_in_timestamp"
                   value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : '' ?>">
			<?php if ( isset( $end ) ) { ?>
                <input type="hidden" name="report_out" value="<?php echo esc_attr( $end ); ?>">
			<?php } ?>
            <input type="hidden" name="check_out_timestamp"
                   value="<?php echo isset( $_REQUEST['check_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_out_timestamp'] ) ) : '' ?>">
			<?php wp_nonce_field( 'tp-hotel-booking-report-export', 'tp-hotel-booking-report-export' ) ?>
            <button type="submit"><?php _e( 'Export', 'wphb-statistic' ) ?></button>
        </form>
	<?php }
}

add_action( 'wphb_statistic_room_availability', 'wphb_statistic_select_room', 10, 4 );

if ( ! function_exists( 'wphb_statistic_select_room' ) ) {
	/**
	 * Select room form for statistic by room availability.
	 *
	 * @param $range
	 * @param $start
	 * @param $end
	 * @param $id
	 */
	function wphb_statistic_select_room( $range, $start, $end, $id ) {
		$room_statistic = WPHB_Statistic_Room::instance();
		?>
        <form method="GET">
            <h4><?php _e( 'Rooms Search', 'wphb-statistic' ) ?></h4>
			<?php wp_nonce_field( 'wphb-statistic', 'wphb-statistic' ); ?>
            <input type="hidden" name="page" value="wphb-statistic"/>
            <input type="hidden" name="tab" value="room"/>
            <input type="hidden" name="range" value="<?php echo esc_attr( $range ); ?>"/>
			<?php if ( isset( $start ) && $start ) { ?>
                <input type="hidden" name="report_in" value="<?php echo esc_attr( $start ); ?>">
			<?php } ?>
			<?php if ( isset( $_GET['report_in_timestamp'] ) ) { ?>
                <input type="hidden" name="report_in_timestamp"
                       value="<?php echo isset( $_GET['report_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_GET['report_in_timestamp'] ) ) : '' ?>">
			<?php } ?>
			<?php if ( isset( $end ) && $end ) { ?>
                <input type="hidden" name="report_out" value="<?php echo esc_attr( $end ); ?>"/>
			<?php } ?>
			<?php if ( isset( $_GET['report_out_timestamp'] ) ) { ?>
                <input type="hidden" name="report_out_timestamp"
                       value="<?php echo isset( $_GET['report_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_GET['report_out_timestamp'] ) ) : '' ?>">
			<?php } ?>

			<?php $rooms = $room_statistic->get_rooms(); ?>
            <select name="room_id[]" id="tp-hotel-booking-room_id" multiple="multiple" class="tokenize-sample">
				<?php foreach ( (array) $rooms as $key => $room ) { ?>
                    <option value="<?php echo esc_attr( $room->ID ) ?>"<?php echo ( in_array( $room->ID, $id ) ) ? ' selected' : '' ?>><?php printf( '%s', $room->post_title ) ?></option>
				<?php } ?>
            </select>
            <p>
                <button type="submit" class="button"><?php _e( 'Show', 'wphb-statistic' ) ?></button>
            </p>

        </form>
	<?php }
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