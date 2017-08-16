<?php

/**
 * Admin View: Statistic admin view page.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Statistic/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
// global report variable
global $hb_report;

// statistic tabs
$report_tabs = apply_filters( 'hotel_booking_report_tab', array(
	array(
		'id'    => 'price',
		'title' => __( 'Booking Price', 'wphb-statistic' ),
	),
	array(
		'id'    => 'room',
		'title' => __( 'Room availability', 'wphb-statistic' ),
	)
) );

// set current tab
$current_tab = 'price';
if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] ) {
	$current_tab = sanitize_text_field( $_REQUEST['tab'] );
}
?>

<ul class="subsubsub">
	<?php $html = array(); ?>
	<?php foreach ( $report_tabs as $key => $tab ): ?>
		<?php $html[] =
			'<li>
				<a ' . ( $tab['id'] === $current_tab ? 'class="current" ' : '' ) . 'href="' . admin_url( 'admin.php?page=wphb-statistic&tab=' . $tab['id'] ) . '" >' . sprintf( '%s', $tab['title'] ) . '</a>
			</li>';
		?>
	<?php endforeach; ?>
	<?php echo implode( ' | ', $html ); ?>
</ul>


<?php //$rooms = hb_get_rooms(); ?>
<?php
$date = apply_filters( 'hotel_booking_report_date', array(
	array(
		'id'    => 'year',
		'title' => __( 'Year', 'wphb-statistic' )
	),
	array(
		'id'    => 'last_month',
		'title' => __( 'Last Month', 'wphb-statistic' )
	),
	array(
		'id'    => 'current_month',
		'title' => __( 'This Month', 'wphb-statistic' )
	),
	array(
		'id'    => '7day',
		'title' => __( 'Last 7 Days', 'wphb-statistic' )
	)
) );

?>

<?php
$current_range = '7day';
if ( isset( $_REQUEST['range'] ) && $_REQUEST['range'] ) {
	$current_range = sanitize_text_field( $_REQUEST['range'] );
}
?>
<div id="tp-hotel-booking-report" class="postbox">

    <div id="poststuff">
        <h3>
            <!--export-->
            <form id="tp-hotel-booking-export" method="POST">
                <input type="hidden" name="page"
                       value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( sanitize_text_field( $_REQUEST['page'] ) ) : '' ?>">
                <input type="hidden" name="range"
                       value="<?php echo isset( $_REQUEST['range'] ) ? esc_attr( sanitize_text_field( $_REQUEST['range'] ) ) : '7day' ?>">
                <input type="hidden" name="tab"
                       value="<?php echo isset( $_REQUEST['tab'] ) ? esc_attr( sanitize_text_field( $_REQUEST['tab'] ) ) : 'price' ?>">
				<?php if ( isset( $_REQUEST['report_in'] ) ): ?>
                    <input type="hidden" name="report_in"
                           value="<?php echo isset( $_REQUEST['report_in'] ) ? esc_attr( sanitize_text_field( $_REQUEST['report_in'] ) ) : '' ?>">
				<?php endif; ?>
                <input type="hidden" name="check_in_timestamp"
                       value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : '' ?>">
				<?php if ( isset( $_REQUEST['report_out'] ) ): ?>
                    <input type="hidden" name="report_out"
                           value="<?php echo isset( $_REQUEST['report_out'] ) ? esc_attr( sanitize_text_field( $_REQUEST['report_out'] ) ) : '' ?>">
				<?php endif; ?>
                <input type="hidden" name="check_out_timestamp"
                       value="<?php echo isset( $_REQUEST['check_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_out_timestamp'] ) ) : '' ?>">
				<?php wp_nonce_field( 'tp-hotel-booking-report-export', 'tp-hotel-booking-report-export' ) ?>
                <button type="submit"><?php _e( 'Export', 'wphb-statistic' ) ?></button>
            </form>
            <ul>
				<?php foreach ( $date as $key => $d ): ?>
                    <li <?php echo sprintf( '%s', $d['id'] === $current_range ? 'class="active"' : '' ) ?>>
                        <a href="<?php echo admin_url( 'admin.php?page=wphb-statistic&tab=' . $current_tab . '&range=' . $d['id'] ) ?>">
							<?php printf( '%s', $d['title'] ) ?>
                        </a>
                    </li>
				<?php endforeach; ?>
                <li>
                    <form method="GET">
                        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
                        <input type="hidden" name="tab"
                               value="<?php echo isset( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : 'price' ?>"/>
                        <input type="hidden" name="range" value="custom"/>
                        <input type="text" class="wphb_statistic_check_in_date" name="report_in"
                               value="<?php echo isset( $_REQUEST['report_in'] ) ? esc_attr( sanitize_text_field( $_REQUEST['report_in'] ) ) : ''; ?>"/>
                        <input type="hidden" name="check_in_timestamp"
                               value="<?php echo isset( $_REQUEST['check_in_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_in_timestamp'] ) ) : ''; ?>"/>
                        <input type="text" class="wphb_statistic_check_out_date" name="report_out"
                               value="<?php echo isset( $_REQUEST['report_out'] ) ? esc_attr( sanitize_text_field( $_REQUEST['report_out'] ) ) : ''; ?>"/>
                        <input type="hidden" name="check_out_timestamp"
                               value="<?php echo isset( $_REQUEST['check_out_timestamp'] ) ? esc_attr( sanitize_text_field( $_REQUEST['check_out_timestamp'] ) ) : ''; ?>"/>
						<?php if ( isset( $_GET['room_id'] ) && $_GET['room_id'] ): ?>
							<?php foreach ( (array) $_GET['room_id'] as $key => $room ): ?>
                                <input type="hidden" name="room_id[]" value="<?php echo esc_attr( $room ) ?>">
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_nonce_field( 'wphb-statistic', 'wphb-statistic' ); ?>
                        <button type="submit"><?php _e( 'Go', 'wphb-statistic' ) ?></button>
                    </form>
                </li>
            </ul>
        </h3>

    </div>

    <!-- booking_page_tp_hotel_booking_report -->
    <div id="tp-hotel-booking-report-main">
        <div id="chart-sidebar">
			<?php do_action( 'hotel_booking_chart_sidebar', $current_tab, $current_range ) ?>
        </div>

        <div id="tp-hotel-booking-chart-content">
			<?php do_action( 'hotel_booking_chart_canvas', $current_tab, $current_range ) ?>
        </div>
    </div>

</div>