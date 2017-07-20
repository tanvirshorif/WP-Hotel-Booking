<?php

/**
 * The template for displaying room info tabs in single room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/tabs.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
global $hb_room;

ob_start();
the_content();
$content = ob_get_clean();

$room_tabs = array(
	array(
		'id'      => 'hb_room_description',
		'title'   => __( 'Description', 'wp-hotel-booking' ),
		'content' => $content
	),
	array(
		'id'      => 'hb_room_additinal',
		'title'   => __( 'Additional Information', 'wp-hotel-booking' ),
		'content' => $hb_room->addition_information
	)
);

if ( comments_open() ) {
	$room_tabs[] = array(
		'id'      => 'hb_room_reviews',
		'title'   => __( 'Reviews', 'wp-hotel-booking' ),
		'content' => ''
	);
}

if ( hb_settings()->get( 'display_pricing_plans' ) ) {
	$room_tabs[] = array(
		'id'      => 'hb_room_pricing_plans',
		'title'   => __( 'Pricing Plans', 'wp-hotel-booking' ),
		'content' => ''
	);
}
$tabs = apply_filters( 'hotel_booking_single_room_information_tabs', $room_tabs );
?>

<?php do_action( 'hotel_booking_before_single_room_information' ); ?>

<div class="hb_single_room_details">

    <ul class="hb_single_room_tabs">
		<?php foreach ( $tabs as $key => $tab ) { ?>
            <li>
                <a href="#<?php echo esc_attr( $tab['id'] ) ?>">
					<?php do_action( 'hotel_booking_single_room_before_tabs_' . $tab['id'] ); ?>
					<?php printf( '%s', $tab['title'] ) ?>
					<?php do_action( 'hotel_booking_single_room_after_tabs_' . $tab['id'] ); ?>
                </a>
            </li>
		<?php } ?>
    </ul>

    <div class="hb_single_room_tabs_content">
		<?php foreach ( $tabs as $key => $tab ) { ?>
            <div id="<?php echo esc_attr( $tab['id'] ) ?>" class="hb_single_room_tab_details">
				<?php do_action( 'hotel_booking_single_room_before_tabs_content_' . $tab['id'] ); ?>
				<?php printf( '%s', $tab['content'] ); ?>
				<?php do_action( 'hotel_booking_single_room_after_tabs_content_' . $tab['id'] ); ?>
            </div>
		<?php } ?>
    </div>

</div>

<?php do_action( 'hotel_booking_after_single_room_information' ); ?>

