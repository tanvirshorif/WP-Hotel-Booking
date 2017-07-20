<?php

/**
 * The template for displaying loop room archive page start.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/loop-start.php.
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

<?php global $hb_settings; ?>

<ul class="rooms tp-hotel-booking hb-catalog-column-<?php echo esc_attr( $hb_settings->get( 'catalog_number_column', 4 ) ) ?>">