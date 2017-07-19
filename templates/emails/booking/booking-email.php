<?php

/**
 * The template for displaying booking email for both admin and customer.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/booking/booking-email.php.
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

$settings = hb_settings();

// email heading
hb_get_template( 'emails/booking/header.php', array(
	'email_heading'      => $email_heading,
	'email_heading_desc' => $email_heading_desc
) );

// booking details
hb_get_template( 'emails/booking.php', array( 'booking' => $booking, 'options' => $settings ) );

// customer details
hb_get_template( 'emails/booking/customer.php', array( 'booking' => $booking, 'options' => $settings ) );

// email footer
hb_get_template( 'emails/booking/footer.php', array( 'booking' => $booking, 'options' => $settings ) );
