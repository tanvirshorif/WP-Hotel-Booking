<?php

/**
 * The template for displaying customer info in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/customer.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 *
 * @param       customer
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<h3>
	<?php _e( 'Customer Details', 'wp-hotel-booking' ); ?>
</h3>

<div class="hb-customer clearfix">
    <!--    for user-->
	<?php hb_get_template( 'checkout/customer-existing.php' ); ?>
    <!--    for guest-->
	<?php hb_get_template( 'checkout/customer-new.php', array( 'customer' => $customer ) ); ?>
</div>

