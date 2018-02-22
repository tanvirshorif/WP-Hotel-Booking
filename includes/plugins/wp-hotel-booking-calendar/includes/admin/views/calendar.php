<?php
/**
 * Admin View: Room calendar.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Calendar/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>


<?php $room_id = intval( hb_get_request( 'hb-room' ) ); ?>

<div class="wrap" id="wp-hotel-booking-calendar">
    <h2><?php _e( 'Booking Calendar', 'wphb-calendar' ); ?></h2>
    <form method="post" name="room-booking-calendar">
        <p>
            <strong><?php _e( 'Select name of room', 'wphb-calendar' ); ?></strong>
            &nbsp;&nbsp;<?php echo hb_dropdown_rooms( array( 'selected' => $room_id ) ); ?>
        </p>
		<?php if ( $room_id ) {
			wp_enqueue_script( 'wphb-calendar-admin', WPHB_CALENDAR_URL . 'assets/js/admin.js', array(
				'jquery',
				'jquery-ui-sortable',
				'jquery-ui-datepicker',
				'wp-util'
			) );
			wp_localize_script( 'wphb-calendar-admin', 'wphb_calendar_booking', array( 'booking' => wphb_calendar_get_room_bookings( $room_id ) ) );
		} ?>
    </form>

    <h2 class="hotel-booking-fullcalendar-month"><?php printf( '%s', date_i18n( 'F, Y', time() ) ) ?></h2>
    <div class="hotel-booking-fullcalendar-toolbar">
        <div class="fc-right">
            <div class="fc-button-group">
                <button type="button" class="fc-prev-button fc-button fc-state-default fc-corner-left"
                        data-month="<?php echo date( 'm/d/Y', strtotime( '-1 month', time() ) ) ?>"
                        data-room=<?php echo esc_attr( $room_id ) ?>>
                    <span class="fc-icon fc-icon-left-single-arrow"></span>
                </button>
                <button type="button" class="fc-next-button fc-button fc-state-default fc-corner-right"
                        data-month="<?php echo date( 'm/d/Y', strtotime( '+1 month', time() ) ) ?>"
                        data-room=<?php echo esc_attr( $room_id ) ?>>
                    <span class="fc-icon fc-icon-right-single-arrow"></span>
                </button>
            </div>
        </div>
    </div>
    <div id="calendar"></div>
</div>