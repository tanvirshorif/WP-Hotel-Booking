<?php

/**
 * The template for displaying booking addition information in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/addition-information.php.
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

<div class="hb-addition-information">
    <div class="hb-col-padding hb-col-border">
        <h4>
			<?php _e( 'Addition Information', 'wp-hotel-booking' ); ?>
        </h4>
        <label for="addition_information"></label>
        <textarea id="addition_information" name="addition_information"></textarea>
    </div>
</div>